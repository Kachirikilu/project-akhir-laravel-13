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
        'refresh-stats-user' => 'refreshStatsUserList',
        'refresh-stats-rps' => 'refreshStatsRPSList',
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

    public function mount($switchTable = 'mahasiswa')
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
    public function refreshStatsUserList()
    {
        $this->clearUserStatsCache();
        $this->clearMahasiswaStatsCache();
    }

    #[On('refresh-stats-rps')]
    public function refreshStatsRPSList()
    {
        $this->clearObeStatsCache();
        $this->clearRpsStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearRpsProdiStatsCache();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function loadingRPSList() {}

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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'rekap_rps_pr', 5 => 'index_rps_pr', 6 => 'mutu_rps_pr', 7 => 'kode_mk', 8 => 'mk', 9 => 'semester', 10 => 'sks', 11 => 'sks_text', 12 => 'is_wajib', 13 => 'is_draf', 14 => 'revisi'],
            'mahasiswa' => [1 => 'kode', 2 => 'name', 3 => 'rekap_mhs', 4 => 'ip_mhs', 7 => 'mutu_mhs', 6 => 'count_rps', 7 => 'total_sks', 8 => 'angkatan', 9 => 'status'],
            // 'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'kode', 4 => 'name', 5 => 'rekap_mhs', 6 => 'ip_mhs', 7 => 'mutu_mhs', 8 => 'count_rps', 9 => 'total_sks', 10 => 'angkatan', 11 => 'status'],
        ];

        $aliases = [
            'rekap_rps_pr' => ['rekap_cpl_pr', 'rekap_cpmk_pr', 'rekap_scpmk_pr', 'rekap_mhs'],
            'index_rps_pr' => ['index_cpl_pr', 'index_cpmk_pr', 'index_scpmk_pr', 'ip_mhs'],
            'mutu_rps_pr' => ['mutu_cpl_pr', 'mutu_cpmk_pr', 'mutu_scpmk_pr', 'mutu_mhs'],

            'kode' => ['kode', 'name'],
            'deskripsi' => ['deskripsi', 'mk'],
            'materi' => ['materi'],
            'count_cpl' => ['count_cpl', 'count_rps', 'total_sks'],
            'is_draf' => ['is_draf', 'indikator'],
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

        $suffix = ($table && $table !== 'mahasiswa') ? "/{$table}" : '';

        $targetPath = "/nilai-management{$suffix}";

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
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
                    break;
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                switch ($this->switchTable) {
                    case 'rps':
                        $queryRPS->onlyTrashed();
                        break;
                }
            }
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                switch ($this->switchTable) {
                    case 'mahasiswa':
                        $queryUser->onlyTrashed();
                        break;
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

            switch ($this->switchTable) {
                case 'rps':
                    $stats = array_merge($stats, $this->getStatsObe($this->showDeleted));
                    $stats = array_merge($stats, $this->getStatsRps($this->showDeleted));
                    break;
                case 'mahasiswa':
                    $stats = array_merge($stats, $this->getStatsMahasiswa($this->showDeleted));
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
