<?php

namespace App\Livewire\Staff;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithTimDosenSearchFilters;
// use App\Livewire\Staff\ObeManagement\TimDosenManagement\WithTimDosenDelete;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\ObeManagement\CplManagement\WithCPLFilters;
// use App\Livewire\Staff\ObeManagement\CpmkManagement\WithSubCPMKDelete;
use App\Livewire\Staff\ObeManagement\CpmkManagement\WithCPMKFilters;
use App\Livewire\Staff\ObeManagement\CpmkManagement\WithSubCPMKFilters;
use App\Livewire\Staff\ObeManagement\ReferensiManagement\WithRefFilters;
// use App\Livewire\Staff\ObeManagement\ReferensiManagement\WithRefModal;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithDosenFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Livewire\Staff\ObeManagement\TimDosenManagement\WithTimDosenFilters;
use App\Livewire\Staff\ObeManagement\WithOBEExcel;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Akademik\TimDosen;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ObeManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithCPLFilters;
    use WithCPLSearchFilters;

    // use WithTimDosenDelete;
    use WithCPMKFilters;
    use WithCPMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithDosenFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;
    use WithMKSearchFilters;
    use WithOBEExcel;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithReferensiSearchFilters;
    use WithRefFilters;

    // use WithRefModal;
    use WithRPSFilters;
    use WithRPSSearchFilters;

    // use WithSubCPMKDelete;
    use WithSubCPMKFilters;
    use WithSubCPMKSearchFilters;
    use WithTimDosenFilters;
    use WithTimDosenSearchFilters;
    use WithUserFilters;
    use WithUserSearchFilters;

    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $searchMode = 'simple';

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $selectedPrId;

    public $selectedFkId;

    public $selectedMKId;

    public $selectedRPSId;

    public $selectedCPLId;

    public $selectedCPMKId;

    public $selectedSCPMKId;

    public $selectedDosenId;

    protected $listeners = [
        'refresh-table' => 'refreshOBEsList',
        'refresh-data-obe' => 'refreshOBEsList',
        'refresh-data-rps' => 'refreshOBEsList',
        'refresh-data-cpl' => 'refreshOBEsList',
        'refresh-data-cpmk' => 'refreshOBEsList',
        'refresh-data-scpmk' => 'refreshOBEsList',
        'refresh-data-ref' => 'refreshOBEsList',
        'refresh-data-tim-dosen' => 'refreshOBEsList',
        'refresh-data-user' => 'refreshOBEsList',
        'refresh-stats-obe' => 'refreshStatsOBEsList',
        'refresh-stats-rps' => 'refreshStatsRPSsList',
        'refresh-stats-cpl' => 'refreshStatsCPLsList',
        'refresh-stats-cpmk' => 'refreshStatsCPMKsList',
        'refresh-stats-scpmk' => 'refreshStatsSubCPMKsList',
        'refresh-stats-ref' => 'refreshStatsRefsList',
        'refresh-stats-tim-dosen' => 'refreshStatsTimDosensList',
        'refresh-stats-user' => 'refreshStatsUsersList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        // 'switchTable' => ['except' => 'rps'],
        'filterRPS' => ['except' => ''],
        'filterRPSgg' => ['except' => ''],
        'filterCPMK' => ['except' => ''],
        'filterSCPMK' => ['except' => ''],
        'filterCPL' => ['except' => ''],
        'filterRef' => ['except' => ''],
        'filterTimDosen' => ['except' => ''],
        'filterDosen' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

    public function mount($switchTable)
    {
        if (empty($switchTable)) {
            return redirect()->route('obe-management', ['switchTable' => 'rps']);
        }
        $this->switchTable = $switchTable;


        $this->updatedShowDeleted();
        // $this->cplNameSearch = [
        //     // 'rps' => '',
        //     'cpmk' => '',
        // ];
        // $this->cpl_id_array = [
        //     // 'rps' => [],
        //     'cpmk' => [],
        // ];
        // $this->cpl_items_array = [
        //     // 'rps' => [],
        //     'cpmk' => [],
        // ];

        $this->refNameSearch = [
            'rps' => '',
            'cpmk' => '',
            'scpmk' => '',
        ];
        $this->ref_id_array = [
            'rps' => [],
            'cpmk' => [],
            'scpmk' => [],
        ];
        $this->ref_items_array = [
            'rps' => [],
            'cpmk' => [],
            'scpmk' => [],
        ];
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

    #[On('selected-fk-id-updated')]
    public function updateSelectedFkId($selectedFkId)
    {
        $this->selectedFkId = $selectedFkId;
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

    #[On('selected-cpl-id-updated')]
    public function updateSelectedCPLId($selectedCPLId)
    {
        $this->selectedCPLId = $selectedCPLId;
    }

    #[On('selected-cpmk-id-updated')]
    public function updateSelectedCPMKId($selectedCPMKId)
    {
        $this->selectedCPMKId = $selectedCPMKId;
    }

    #[On('selected-scpmk-id-updated')]
    public function updateSelectedSCPMKId($selectedSCPMKId)
    {
        $this->selectedSCPMKId = $selectedSCPMKId;
    }

    #[On('selected-dosen-id-updated')]
    public function updateSelectedDosenId($selectedDosenId)
    {
        $this->selectedDosenId = $selectedDosenId;
    }

    #[On('refresh-data-obe')]
    #[On('refresh-data-rps')]
    #[On('refresh-data-cpl')]
    #[On('refresh-data-cpmk')]
    #[On('refresh-data-scpmk')]
    #[On('refresh-data-ref')]
    #[On('refresh-data-tim-dosen')]
    #[On('refresh-data-user')]
    #[On('refresh-table')]
    public function refreshOBEsList()
    {
        $this->resetPage();
    }

    #[On('refresh-stats-obe')]
    public function refreshStatsOBEsList()
    {
        $this->clearObeStatsCache();
        $this->clearObeProdiStatsCache();
    }

    #[On('refresh-stats-rps')]
    public function refreshStatsRPSsList()
    {
        $this->clearObeStatsCache();
        $this->clearRpsStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearRpsProdiStatsCache();
    }

    #[On('refresh-stats-cpl')]
    public function refreshStatsCPLsList()
    {
        $this->clearObeStatsCache();
        $this->clearCplStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearCplProdiStatsCache();
    }

    #[On('refresh-stats-cpmk')]
    public function refreshStatsCPMKsList()
    {
        $this->clearObeStatsCache();
        $this->clearCpmkStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearCpmkProdiStatsCache();
    }

    #[On('refresh-stats-scpmk')]
    public function refreshStatsSubCPMKsList()
    {
        $this->clearObeStatsCache();
        $this->clearScpmkStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearScpmkProdiStatsCache();
    }

    #[On('refresh-stats-ref')]
    public function refreshStatsReferensisList()
    {
        $this->clearObeStatsCache();
        $this->clearReferensiStatsCache();
    }

    #[On('refresh-stats-tim-dosen')]
    public function refreshStatsTimDosensList()
    {
        $this->clearObeStatsCache();
        $this->clearTimDosenStatsCache();
    }

    #[On('refresh-stats-user')]
    public function refreshStatsUsersList()
    {
        $this->clearUserStatsCache();
        $this->clearObeStatsCache();
        $this->clearDosenStatsCache();
    }

    public function refreshStats()
    {
        $this->refreshStatsOBEsList();
        $this->refreshStatsRPSsList();
        $this->refreshStatsCPLsList();
        $this->refreshStatsCPMKsList();
        $this->refreshStatsSubCPMKsList();
        $this->refreshStatsReferensisList();
        $this->refreshStatsTimDosensList();
        $this->refreshStatsUsersList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik OBE berhasil diperbarui!', type: 'info', variant: 'info');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function loadingRPSsList() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterRPS', 'filterRPSgg', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef', 'filterTimDosen', 'filterDosen', 'filterStatus']);
        $this->resetPage();
    }

    public function updatedSearch()
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
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'kode_mk', 5 => 'mk', 6 => 'semester', 7 => 'sks', 8 => 'sks_text', 9 => 'is_wajib', 10 => 'count-cpmk', 11 => 'count-scpmk', 12 => 'total_bobot', 13 => 'is_draf', 14 => 'revisi', 15 => 'created_at', 16 => 'updated_at'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count_rps', 5 => 'created_at', 6 => 'updated_at'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count_cpl', 5 => 'count-scpmk', 6 => 'total_bobot', 7 => 'created_at', 8 => 'updated_at'],
            'sub-cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'metode', 5 => 'materi', 6 => 'metodologi', 7 => 'indikator', 8 => 'bobot', 9 => 'tugas', 10 => 'w_tugas', 11 => 'w_mandiri', 12 => 'created_at', 13 => 'updated_at'],
            'referensi' => [1 => 'id', 2 => 'kode', 3 => 'judul', 4 => 'penulis', 5 => 'penerbit', 6 => 'tahun', 7 => 'link', 8 => 'created_at', 9 => 'updated_at'],
            'tim-dosen' => [1 => 'id', 2 => 'kode', 3 => 'nama_tim', 4 => 'ketua_tim', 5 => 'nip_ketua', 6 => 'count_dosen', 7 => 'count_koordinator', 8 => 'count_pengajar', 9 => 'count_asisten', 10 => 'program_studi', 11 => 'created_at', 12 => 'updated_at'],
            'dosen' => [1 => 'kode', 2 => 'name', 3 => 'count_rps', 4 => 'total_sks', 5 => 'status', 6 => 'program_studi'],
        ];
        $aliases = [
            'kode' => ['kode', 'name', 'nama_tim'],
            // 'name' => ['name', 'kode'],
            'deskripsi' => ['deskripsi', 'mk', 'judul', 'name', 'ketua_tim'],
            // 'mk' => ['mk', 'deskripsi', 'judul'],
            // 'judul' => ['judul', 'deskripsi', 'mk'],
            'materi' => ['materi', 'penulis'],
            // 'penulis' => ['penulis', 'materi'],
            // 'count_rps' => ['count_rps', 'count_cpl'],
            'count_cpl' => ['count_cpl', 'count_rps', 'total_sks', 'count_dosen', 'count_koordinator', 'count_pengajar', 'count_asisten'],
            // 'akademik' => ['akademik', 'bobot', 'total_bobot'],
            'bobot' => ['bobot', 'total_bobot'],
            // 'total_bobot' => ['total_bobot', 'akademik', 'bobot'],
            'is_draf' => ['is_draf', 'indikator', 'status'],
            // 'indikator' => ['indikator', 'is_draf'],
            'program_studi' => ['program_studi'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->dispatch('table-switched', switchTable: $table);
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $allFilters = [
            'rps' => 'filterRPS',
            'cpl' => 'filterCPL',
            'cpmk' => 'filterCPMK',
            'sub-cpmk' => 'filterSCPMK',
            'referensi' => 'filterRef',
            'tim-dosen' => 'filterTimDosen',
            'dosen' => 'filterDosen',
            'status' => 'filterStatus',
        ];

        foreach ($allFilters as $tableParam => $filterVariable) {
            if ($tableParam !== $this->switchTable) {
                $this->$filterVariable = '';
            }
        }

        $limits = [
            'cpl' => 100,
            'tim-dosen' => 150,
            'referensi' => 150,
            'rps' => 200,
            'dosen' => 200,
            'cpmk' => 300,
            'sub-cpmk' => 500,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        // if ($table == '' || $table == null) {
        //     $targetPath = '/obe-management';
        // } else {
        //     $targetPath = '/obe-management/'.$table;
        // }
        $targetPath = '/obe-management'.($table ? '/'.$table : '');

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function updatedSwitchTable()
    {
        $this->updatedShowDeleted();
    }

    public function updatedShowDeleted()
    {
        if (Auth::user()->dosen && $this->switchTable == 'dosen') {
            $this->showDeleted = false;
        }
    }

    // public function placeholder()
    // {
    //     return view('livewire.global.livewire-skeletons.table-placeholder');
    // }

    public function render()
    {
        // $this->updatedMKNameSearch($this->mkNameSearch);
        // $this->updatedPrNameSearch($this->prNameSearch);
        try {
            // =========================
            // QUERY BASE
            // =========================
            $queryRPS = collect();
            $queryCPL = collect();
            $queryCPMK = collect();
            $querySCPMK = collect();
            $queryRef = collect();
            $queryTimDosen = collect();
            $queryUser = collect();

            switch ($this->switchTable) {
                case 'rps':
                    $queryRPS = $this->inputRPSSearch();
                    break;
                case 'cpl':
                    $queryCPL = $this->inputCPLSearch();
                    break;
                case 'cpmk':
                    $queryCPMK = $this->inputCPMKSearch();
                    break;
                case 'sub-cpmk':
                    $querySCPMK = $this->inputSCPMKSearch();
                    break;
                case 'referensi':
                    $queryRef = $this->inputRefSearch();
                    break;
                case 'tim-dosen':
                    $queryTimDosen = $this->inputTimDosenSearch();
                    break;
                case 'dosen':
                    $queryUser = $this->inputUserSearch('dosen');
                    break;
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                switch ($this->switchTable) {
                    case 'rps':
                        $queryRPS->onlyTrashed();
                        break;
                    case 'cpl':
                        $queryCPL->onlyTrashed();
                        break;
                    case 'cpmk':
                        $queryCPMK->onlyTrashed();
                        break;
                    case 'sub-cpmk':
                        $querySCPMK->onlyTrashed();
                        break;
                    case 'referensi':
                        $queryRef->onlyTrashed();
                        break;
                    case 'tim-dosen':
                        $queryTimDosen->onlyTrashed();
                        break;
                }
            }

            if (Auth::user()->admin) {
                if ($this->showDeleted && $this->AuthCheck('admin')) {
                    if ($this->switchTable == 'dosen') {
                        $queryUser->onlyTrashed();
                    }
                }
            }

            // =========================
            // TIME SETUP
            // =========================
            $now = now();
            $sixMonthsAgo = now()->subMonths(6);
            $currentYear = now()->year;
            $threeYearsAgo = now()->subYears(3);
            $fiveYearsAgo = now()->subYears(5);
            $tenYearsAgo = now()->subYears(10);

            // =========================
            // PAGINATION
            // =========================
            $data = [
                'rps' => collect(),
                'cpl' => collect(),
                'cpmk' => collect(),
                'scpmk' => collect(),
                'ref' => collect(),
                'tim_dosens' => collect(),
                'users' => collect(),
            ];

            switch ($this->switchTable) {
                case 'rps':
                    $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
                    break;
                case 'cpl':
                    $this->addCountRpsCpl($queryCPL, null, 'count_rps');
                    $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'cpmk':
                    $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'sub-cpmk':
                    $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'referensi':
                    $this->buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);
                    break;
                case 'tim-dosen':
                    $this->addCountRpsTimDosen($queryTimDosen, 'count_rps');
                    $this->addTotalSksTimDosen($queryTimDosen, 'total_sks');
                    $this->buttonTimDosenFilter($queryTimDosen);
                    break;
                case 'dosen':
                    $this->addCountRpsDosen($queryUser, 'count_rps');
                    $this->addTotalSksDosen($queryUser, 'total_sks');
                    $this->buttonUserFilter($queryUser);
                    break;
            }

            if ($this->searchMode == 'complex') {
                switch ($this->switchTable) {
                    case 'rps':
                        $data['rps'] = $this->searchOutputRPS($queryRPS, $this->search, $this->searchBobotRPS, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'cpl':
                        $data['cpl'] = $this->searchOutputCPL($queryCPL, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'cpmk':
                        $data['cpmk'] = $this->searchOutputCPMK($queryCPMK, $this->search, $this->searchBobotCPMK, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'sub-cpmk':
                        $data['scpmk'] = $this->searchOutputSCPMK($querySCPMK, $this->search, $this->searchBobotSCPMK, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'referensi':
                        $data['ref'] = $this->searchOutputRef($queryRef, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'tim-dosen':
                        $data['tim_dosens'] = $this->searchOutputTimDosen($queryTimDosen, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                        break;
                    case 'dosen':
                        $data['users'] = $this->searchOutputUser($queryUser, $this->search, null, $this->perPage, $this->sortField, $this->sortDirection, null, 1);
                        break;
                }
            } else {
                switch ($this->switchTable) {
                    case 'rps':
                        $data['rps'] = $queryRPS->paginate($this->perPage);
                        break;
                    case 'cpl':
                        $data['cpl'] = $queryCPL->paginate($this->perPage);
                        break;
                    case 'cpmk':
                        $data['cpmk'] = $queryCPMK->paginate($this->perPage);
                        break;
                    case 'sub-cpmk':
                        $data['scpmk'] = $querySCPMK->paginate($this->perPage);
                        break;
                    case 'referensi':
                        $data['ref'] = $queryRef->paginate($this->perPage);
                        break;
                    case 'tim-dosen':
                        $data['tim_dosens'] = $queryTimDosen->paginate($this->perPage);
                        break;
                    case 'dosen':
                        $data['users'] = $queryUser->paginate($this->perPage);
                        break;
                }
            }

            $stats = [
                'rps' => '🏦',
                'rps-saya' => '🏦',
                'rps-prodi' => '🏦',
                'rps-prodi-non-aktif' => '❌',
                'rps-akademik' => '📘',
                'rps-rev-new' => '✨',
                'rps-aktif' => '✅',
                'rps-draf' => '📝',
                'rps-older-5' => '⏳',

                'cpl' => '🎯',
                'cpl-month' => '🎯',
                'cpl-6-months' => '⏱️',
                'cpl-year' => '📆',
                'cpl-older-5' => '⏳',

                'cpmk' => '🧩',
                'cpmk-month' => '🧩',
                'cpmk-6-months' => '⏱️',
                'cpmk-year' => '📆',
                'cpmk-older-5' => '⏳',

                'scpmk' => '🔗',
                'scpmk-month' => '🔗',
                'scpmk-6-months' => '⏱️',
                'scpmk-year' => '📆',
                'scpmk-older-5' => '⏳',

                'ref' => '📚',
                'ref-year' => '📚',
                'ref-2-3-years' => '2️⃣',
                'ref-4-5-years' => '4️⃣',
                'ref-6-10-years' => '🔟',
                'ref-older-10' => '⏳',

                'tim-dosen' => '✅',
                'tim-dosen-rps' => '✅',
                'tim-dosen-non-rps' => '❌',
                'tim-dosen-saya' => '👥',
                'tim-dosen-prodi' => '🏛️',
                'tim-dosen-all' => '👥',
                'tim-dosen-rps' => '✅',
                'tim-dosen-non-rps' => '❌',

                'dosen' => '✅',
                'dosen-rps' => '✅',
                'dosen-non-rps' => '❌',
                'dosen-prodi' => '🏛️',
                'dosen-all' => '👥',
                'dosen-aktif' => '🟢',
                'dosen-non-aktif' => '🔴',
            ];

            $stats = array_merge($stats, $this->getStatsObe($this->showDeleted));

            // =========================
            // SWITCH STATS (TIDAK OVERWRITE)
            // =========================
            switch ($this->switchTable) {
                case 'rps':
                    $stats = array_merge($stats, $this->getStatsRps($this->showDeleted));
                    break;
                case 'cpl':
                    $stats = array_merge($stats, $this->getStatsKurikulum('cpl', $this->showDeleted));
                    break;
                case 'cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulum('cpmk', $this->showDeleted));
                    break;
                case 'sub-cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulum('scpmk', $this->showDeleted));
                    break;
                case 'referensi':
                    $stats = array_merge($stats, $this->getStatsReferensi($this->showDeleted));
                    break;
                case 'tim-dosen':
                    $stats = array_merge($stats, $this->getStatsTimDosen($this->showDeleted));
                    break;
                case 'dosen':
                    $stats = array_merge($stats, $this->getStatsDosen($this->showDeleted));
                    break;
            }

            // =========================
            // TOTAL (NO GET)
            // =========================
            return view('livewire.staff.obe-management', array_merge($data, [
                'stats' => $stats,
            ]));
        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.obe-management', [
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
                'cpl' => CPL::whereRaw('1=0')->paginate($this->perPage),
                'cpmk' => CPMK::whereRaw('1=0')->paginate($this->perPage),
                'scpmk' => SubCPMK::whereRaw('1=0')->paginate($this->perPage),
                'ref' => Referensi::whereRaw('1=0')->paginate($this->perPage),
                'tim_dosens' => TimDosen::whereRaw('1=0')->paginate($this->perPage),
                'users' => User::whereRaw('1=0')->whereHas('dosen')->paginate($this->perPage),
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

                    'cpl' => '-',
                    'cpl-month' => '-',
                    'cpl-6-months' => '-',
                    'cpl-year' => '-',
                    'cpl-older-5' => '-',

                    'cpmk' => '-',
                    'cpmk-month' => '-',
                    'cpmk-6-months' => '-',
                    'cpmk-year' => '-',
                    'cpmk-older-5' => '-',

                    'scpmk' => '-',
                    'scpmk-month' => '-',
                    'scpmk-6-months' => '-',
                    'scpmk-year' => '-',
                    'scpmk-older-5' => '-',

                    'ref' => '-',
                    'ref-year' => '-',
                    'ref-2-3-years' => '-',
                    'ref-4-5-years' => '-',
                    'ref-6-10-years' => '-',
                    'ref-older-10' => '-',

                    'tim-dosen-saya' => '-',
                    'tim-dosen-prodi' => '-',
                    'tim-dosen-all' => '-',
                    'tim-dosen-rps' => '-',
                    'tim-dosen-non-rps' => '-',

                    'dosen' => '-',
                    'dosen-rps' => '-',
                    'dosen-non-rps' => '-',
                    'dosen-prodi' => '-',
                    'dosen-all' => '-',
                    'dosen-aktif' => '-',
                    'dosen-non-aktif' => '-',
                ],
            ]);
        }
    }
}
