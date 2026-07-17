<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Staff\MkManagement\WithMKExcel;
use App\Livewire\Staff\MkManagement\WithMKFilters;
use App\Models\Akademik\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class MataKuliahManagement extends Component
{
    use HasSortir;
    use HasToast;
    use HasStats;
    use WithMKExcel;
    use WithMKFilters;
    use WithMKSearchFilters;
    use WithPagination;


    public $perPage = 8;

    public $switchTable = '';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public $selectedPrId;
    public $selectedDpId;
    public $selectedFkId;

    protected $listeners = [
        'refresh-table' => 'refreshMKsList',
        'refresh-data-mk' => 'refreshMKsList',
        'refresh-stats-mk' => 'refreshStatsMKsList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterMK' => ['except' => ''],
        'filterMKgg' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' =>  ['except' => false],
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
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

    #[On('selected-dp-id-updated')]
    public function updateSelectedDpId($selectedDpId)
    {
        $this->selectedDpId = $selectedDpId;
    }

    #[On('selected-fk-id-updated')]
    public function updateSelectedFkId($selectedFkId)
    {
        $this->selectedFkId = $selectedFkId;
    }

    #[On('refresh-data-mk')]
    #[On('refresh-table')]
    public function refreshMKsList()
    {
        $this->resetPage();
    }

    #[On('refresh-stats-mk')]
    public function refreshStatsMKsList()
    {
        $this->clearMkStatsCache();
    }

    public function refreshStats() {
        $this->refreshStatsMKsList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik Mata Kuliah berhasil diperbarui!', type: 'info', variant: 'info');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterMK']);
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        $map = [
            'tatap_muka' => 'sks_tm',
            'praktikum' => 'sks_pr',
            'praktek_lapangan' => 'sks_pl',
            'simulasi' => 'sks_sm',
        ];

        if (isset($map[$table]) && str_starts_with($sortField, 'sks_')) {
            $this->sortField = $map[$table];
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = '/mata-kuliah-management'.($table ? '/'.$table : '');
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    // public function placeholder()
    // {
    //     return view('livewire.global.livewire-skeletons.table-placeholder');
    // }

    public function render()
    {
        // $this->inputPrFilter();
        // $this->inputDpFilter();
        // $this->inputFkFilter();

        try {
            $queryMK = $this->inputMKSearch();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryMK->onlyTrashed();
            }
            $stats = $this->getStatsMk($this->showDeleted);

            $this->buttonMKSwitch($queryMK);
            $this->buttonMKFilter($queryMK);

            if ($this->searchMode == 'complex') {
                $mks = $this->searchOutputMK($queryMK, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $mks = $queryMK->paginate($this->perPage);
            }

            return view('livewire.staff.mk-management', [
                'mks' => $mks,
                'stats' => $stats,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.mk-management', [
                'mks' => MataKuliah::whereRaw('1 = 0')->paginate($this->perPage),

                'stats' => [
                    'mK' => '-',
                    'mk-tp' => '-',
                    'mk-pr' => '-',
                    'mk-pl' => '-',
                    'mk-sm' => '-',

                    'mk-prodi' => '-',
                    'mk-opsi' => '-',
                    'mk-wajib' => '-',
                    'mk-pilihan' => '-',
                    'mk-uni' => '-',
                ],
            ]);
        }
    }
}
