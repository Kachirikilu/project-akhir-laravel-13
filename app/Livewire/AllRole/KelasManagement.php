<?php

namespace App\Livewire\AllRole;

use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\Global\WithKelasSearchFilters;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\HasStats;
use App\Models\Kelas\Kelas;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class KelasManagement extends Component
{
    use HasSortir;
    use HasToast;
    use HasStats;
    use WithKelasSearchFilters;
    use WithKelasFilters;
    use WithPagination;

    public $perPage = 8;

    public $switchTable = '';

    public $switchTable2 = 'card';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public $selectedPrId;

    public $selectedMKId;

    public $selectedDosenId;

    public $selectedRPSId;

    protected $listeners = [
        'refresh-table' => 'refreshKelasList',
        'refresh-data-kelas' => 'refreshKelasList',
        'refresh-stats-kelas' => 'refreshStatsKelasList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterKelas' => ['except' => ''],
        'filterKelasgg' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

    public function mount($switchTable = '', $switchTable2 = 'card')
    {
        $this->switchTable = $switchTable;
        $this->switchTable2 = $switchTable2;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterKelas', 'filterKelasgg']);
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    #[On('switch-table-updated')]
    public function updateSwitchTable($switchTable)
    {
        $this->switchTable = $switchTable;
    }

    #[On('selected-pr-id-updated')]
    public function updateSelectedPrId($selectedPrId)
    {
        $this->selectedPrId = $selectedPrId;
    }
    #[On('selected-mk-id-updated')]
    public function updateSelectedMKId($selectedMKId)
    {
        $this->selectedMKId = $selectedMKId;
    }
    #[On('selected-rps-id-updated')]
    public function updateSelectedRPSId($selectedRPSId)
    {
        $this->selectedRPSId = $selectedRPSId;
    }
    #[On('selected-dosen-id-updated')]
    public function updateSelectedDosenId($selectedDosenId)
    {
        $this->selectedDosenId = $selectedDosenId;
    }

    #[On('refresh-data-kelas')]
    #[On('refresh-table')]
    public function refreshKelasList()
    {
        $this->resetPage();
    }
    #[On('refresh-stats-kelas')]
    public function refreshStatsKelasList()
    {
        $this->clearKelasStatsCache();
    }

    public function refreshStats() {
        $this->refreshStatsKelasList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik Kelas berhasil diperbarui!', type: 'info', variant: 'info');
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'card' => [1 => 'kode', 2 => 'kode_rps', 3 => 'kelas', 4 => 'mk', 5 => 'semester'],
            'table' => [1 => 'id', 2 => 'kode', 3 => 'kode_rps', 4 => 'kelas', 5 => 'program_studi', 6 => 'hari_pelaksanaan', 7 => 'jam_pelaksanaan', 8 => 'kapasitas', 9 => 'tanggal_pelaksanaan', 10 => 'kode_mk', 11 => 'mk', 12 => 'semester', 13 => 'sks', 14 => 'pembelajaran', 15 => 'wajib', 16 => 'created_at', 17 => 'updated_at'],
        ];
        $aliases = [
            'kode' => ['kode'],
            'kode_rps' => ['kode_rps', 'kode_rps'],
            'kelas' => ['kelas', 'hari_pelaksanaan', 'jam_pelaksanaan', 'kapasitas', 'tanggal_pelaksanaan'],
            'mk' => ['mk', 'semester', 'sks', 'pembelajaran', 'wajib'],
            'semester' => ['semester'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable2($table)
    {
        $this->switchTable2 = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        if ($table == 'card') {
            $table = '';
        }

        $targetPath = $this->buildTargetPath($this->switchTable, $table);

        $this->dispatch('table-switched', switchTable2: $this->switchTable2, switchTable: $this->switchTable, targetUrl: $targetPath);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;

        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = $this->buildTargetPath($table, $this->switchTable2);

        $this->dispatch('table-switched', switchTable2: $this->switchTable2, switchTable: $this->switchTable, targetUrl: $targetPath);
    }

    private function buildTargetPath($table, $table2)
    {
        $path = '/kelas-management';

        if ($table2) {
            $path .= '/'.$table2;

            if ($table) {
                $path .= '/'.$table;
            }
        } elseif ($table) {
            $path .= '/all/'.$table;
        }

        return $path;
    }

    public function render()
    {
        try {
            // =========================
            // QUERY UTAMA (TABLE)
            // =========================
            $queryKelas = $this->inputKelasSearch();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryKelas->onlyTrashed();
            }
            $stats = $this->getStatsKelas($this->showDeleted);

            $this->buttonKelasFilter($queryKelas);

            if ($this->searchMode == 'complex') {
                $kelas = $this->searchOutputKelas($queryKelas, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $kelas = $queryKelas->paginate($this->perPage);
            }


            return view('livewire.all-role.kelas-management', [
                'kelas' => $kelas,
                'stats' => $stats ?? null,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management', [
                'kelas' => Kelas::whereRaw('1 = 0')->paginate($this->perPage),

                'totalGanjilGanjil' => '-',
                'stats' => [
                    'kelas-saya' => '-',
                    'kelas-prodi' => '-',

                    'kelas' => '-',
                    'kelas-tp' => '-',
                    'kelas-pr' => '-',
                    'kelas-pl' => '-',
                    'kelas-sm' => '-',

                    'kelas-wajib' => '-',
                    'kelas-pilihan' => '-',
                    'kelas-uni' => '-',
                ],
            ]);
        }
    }
}
