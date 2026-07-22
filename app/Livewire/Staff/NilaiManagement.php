<?php

namespace App\Livewire\Staff;

use App\Http\Services\RekapCapaian;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
// use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
// use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\NilaiManagement\WithNilaiMahasiswaExcel;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
// use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSDelete;
use App\Livewire\Staff\ObeManagement\WithOBEExcel;
use Illuminate\Support\Facades\Auth;

// use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSModal;
// use App\Models\Akademik\CPL;
use App\Models\Auth\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;
    // use WithRPSDelete;
    use WithOBEExcel;


    // use WithDepartemenSearchFilters;
    // use WithFakultasSearchFilters;
    use WithRPSSearchFilters;
    use WithNilaiMahasiswaExcel;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSFilters;

    // use WithRPSModal;
    // use WithRPSSearchFilters;
    use WithUserDelete;
    use WithUserFilters;

    // use WithUserModal;
    use WithUserSearchFilters;

    public $perPage = 8;

    public $switchTable = 'mahasiswa';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'desc';

    public $showDeleted = false;

    public $selectedPrId;

    public $selectedDpId;

    public $selectedFkId;

    public $selectedMKId;
    public $selectedDosenId;

    protected $listeners = [
        'refresh-table' => 'refreshUsersList',
        'refresh-data-rps-mahasiswa' => 'refreshNilaisList',
        'refresh-stats-user' => 'refreshStatsUsersList',
        'refresh-stats-rps' => 'refreshStatsRPSsList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        // 'switchTable' => ['except' => 'cpl'],
        'filterStatus' => ['except' => ''],

        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'desc'],
        'showDeleted' => ['except' => false],
    ];

    public function mount($switchTable)
    {
        if (empty($switchTable)) {
            return redirect()->route('nilai-management', ['switchTable' => 'mahasiswa']);
        }
        $this->switchTable = $switchTable;
        $this->updatedShowDeleted();

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

    #[On('refresh-data-rps-mahasiswa')]
    #[On('refresh-data-rps')]
    #[On('refresh-table')]
    public function refreshNilaisList()
    {
        $this->resetPage();
    }

    #[On('selected-mk-id-updated')]
    public function updateSelectedMKId($selectedMKId)
    {
        $this->selectedMKId = $selectedMKId;
    }

    #[On('selected-dosen-id-updated')]
    public function updateSelectedDosenId($selectedDosenId)
    {
        $this->selectedDosenId = $selectedDosenId;
    }

    #[On('refresh-stats-user')]
    public function refreshStatsUsersList()
    {
        $this->clearUserStatsCache();
        $this->clearMahasiswaStatsCache();
    }

    #[On('refresh-stats-rps')]
    public function refreshStatsRPSsList()
    {
        $this->clearObeStatsCache();
        $this->clearRpsStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearRpsProdiStatsCache();
    }

    public function refreshStats() {
        $this->refreshStatsUsersList();
        $this->refreshStatsRPSsList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik User & RPS berhasil diperbarui!', type: 'info', variant: 'info');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingRPSsList() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        // $this->reset(['search', 'filterPr']);
        $this->reset(['search', 'searchAngkatan']);
        $this->resetPage();
    }

    public function refreshUsersList()
    {
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'is_draf', 5 => 'revisi'],
            'mahasiswa' => [1 => 'kode', 2 => 'name', 3 => 'rekap_mhs', 4 => 'ipk_mhs', 7 => 'mutu_mhs', 6 => 'count_rps', 7 => 'total_sks', 8 => 'angkatan', 9 => 'status', 10 => 'kampus', 11 => 'program_studi'],
        ];

        $aliases = [
            'kode' => ['kode'],
            'akademik' => ['akademik', 'angkatan'],
            'is_draf' => ['is_draf', 'status'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        // $table = $this->normalizeSwitchTable($table);
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $allFilters = [
            'rps' => 'filterRPS',
            'mahasiswa' => 'filterStatus',
        ];

        foreach ($allFilters as $tableParam => $filterVariable) {
            if ($tableParam !== $this->switchTable) {
                $this->$filterVariable = '';
            }
        }

        $limits = [
            'rps' => 200,
            'mahasiswa' => 200,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }


        // $suffix = ($table && $table !== 'mahasiswa') ? "/{$table}" : '';
        $suffix = $table ? "/{$table}" : '';

        $targetPath = "/nilai-management{$suffix}";

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

 
    public function updatedSwitchTable()
    {
        $this->updatedShowDeleted();
    }

    public function updatedShowDeleted()
    {
        if (Auth::user()->dosen && $this->switchTable == 'mahasiswa') {
            $this->showDeleted = false;
        }
    }

    public function render()
    {

        try {
            // $this->inputPrFilter();
            // $this->inputDpFilter();
            // $this->inputFkFilter();
            $queryRPS = collect();
            $queryUser = collect();

            switch ($this->switchTable) {
                case 'rps':
                    $queryRPS = $this->inputRPSSearch(null, null, 1);
                    break;
                case 'mahasiswa':
                    $queryUser = $this->inputUserSearch('mahasiswa');
                    $this->addCountRpsMahasiswa($queryUser, 'count_rps');
                    $this->addTotalSksMahasiswa($queryUser, 'total_sks');
                    break;
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                switch ($this->switchTable) {
                    case 'rps':
                        $queryRPS->onlyTrashed();
                        break;
                }
            }
            if (Auth::user()->admin) {
                if ($this->showDeleted && $this->AuthCheck('admin')) {
                    switch ($this->switchTable) {
                        case 'mahasiswa':
                            $queryUser->onlyTrashed();
                            break;
                    }
                }
            }

            $now = now();
            $currentYear = now()->year;
            $fiveYearsAgo = now()->subYears(5);

            $data = [
                'rps' => collect(),
                'users' => collect(),
            ];

            if ($this->switchTable == 'rps') {
                $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
            }

            if ($this->searchMode == 'complex') {
                switch ($this->switchTable) {
                    case 'rps':
                        $data['rps'] = $this->searchOutputRPS($queryRPS, $this->search, $this->searchBobotRPS, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'mahasiswa':
                        $data['users'] = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, null, 1);
                        break;
                }
            } else {
                switch ($this->switchTable) {
                    case 'rps':
                        $data['rps'] = $queryRPS->paginate($this->perPage);
                        break;
                    case 'mahasiswa':
                        $data['users'] = $queryUser->paginate($this->perPage);
                        break;
                }
            }

            $stats = [
                'rps-saya' => '🏦',
                'rps-prodi' => '🏦',
                'rps-prodi-non-aktif' => '❌',
                'rps-akademik' => '📘',
                'rps-rev-new' => '✨',
                'rps-aktif' => '✅',
                'rps-draf' => '📝',
                'rps-older-5' => '⏳',

                'mahasiswa-prodi' => '🏛️',
                'mahasiswa-opsi' => '⚙️',
                'mahasiswa-aktif' => '🟢',
                'mahasiswa-non-aktif' => '🔴',
            ];
            $stats = array_merge($stats, $this->getStatsObe($this->showDeleted));
                    $stats = array_merge($stats, $this->getStatsMahasiswa($this->showDeleted));


            switch ($this->switchTable) {
                case 'rps':
                    $stats = array_merge($stats, $this->getStatsRps($this->showDeleted));
                    break;
            }

            return view('livewire.staff.nilai-management', array_merge($data, [
                'stats' => $stats,
            ]));

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.nilai-management', [
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
                'users' => User::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
            ], [
                'stats' => [
                    'rps' => '-',
                    'rps-saya' => '-',
                    'rps-prodi' => '-',
                    'rps-prodi-non-aktif' => '-',
                    'rps-akademik' => '-',
                    'rps-rev-new' => '-',
                    'rps-aktif' => '-',
                    'rps-draf' => '-',
                    'rps-older-5' => '-',

                    'mahasiswa-prodi' => '-',
                    'mahasiswa-opsi' => '-',
                    'mahasiswa-aktif' => '-',
                    'mahasiswa-non-aktif' => '-',
                ],
            ]);
        }
    }
}
