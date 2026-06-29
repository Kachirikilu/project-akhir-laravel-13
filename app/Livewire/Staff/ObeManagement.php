<?php

namespace App\Livewire\Staff;

use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
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
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Global\WithTimDosenSearchFilters;
use App\Livewire\Staff\OBEManagement\TimDosenManagement\WithTimDosenDelete;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLDelete;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKModal;
use App\Livewire\Staff\OBEManagement\ReferensiManagement\WithRefDelete;
use App\Livewire\Staff\OBEManagement\ReferensiManagement\WithRefFilters;
use App\Livewire\Staff\OBEManagement\ReferensiManagement\WithRefModal;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithDosenFilters;
use App\Livewire\Staff\OBEManagement\TimDosenManagement\WithTimDosenFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSDelete;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSModal;
use App\Livewire\Staff\OBEManagement\TimDosenManagement\WithTimDosenModal;
use App\Livewire\Staff\OBEManagement\WithOBEExcel;
use App\Models\Akademik\CPL;
use App\Models\Akademik\TimDosen;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ObeManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithCPLDelete;
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLSearchFilters;
    use WithTimDosenDelete;
    use WithCPMKDelete;
    use WithCPMKFilters;
    use WithCPMKModal;
    use WithCPMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithDosenFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;
    use WithMKSearchFilters;
    use WithOBEExcel;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRefDelete;
    use WithReferensiSearchFilters;
    use WithTimDosenSearchFilters;
    use WithRefFilters;
    use WithRefModal;
    use WithRPSDelete;
    use WithRPSFilters;
    use WithRPSModal;
    use WithRPSSearchFilters;
    use WithSubCPMKDelete;
    use WithSubCPMKFilters;
    use WithSubCPMKModal;
    use WithSubCPMKSearchFilters;
    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;
    use WithTimDosenFilters;
    use WithTimDosenModal;

    public $switchTable = 'rps';

    public $perPage = 8;

    public $search = '';

    public $searchMode = 'simple';

    public $showDeleted = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    protected $listeners = [
        'refresh-table'       => 'refreshObesList',
        'refresh-data-obe'    => 'refreshObesList',
        'refresh-data-rps'    => 'refreshObesList',
        'refresh-data-cpl'    => 'refreshObesList',
        'refresh-data-cpmk'   => 'refreshObesList',
        'refresh-data-scpmk'  => 'refreshObesList',
        'refresh-data-ref'    => 'refreshObesList',
        'refresh-data-tim-dosen' => 'refreshObesList',
        'refresh-data-user'   => 'refreshObesList',
        'loadDraft'           => 'loadDraft',
        'saveToDraft'         => 'saveToDraft',
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

    public function mount($switchTable = 'rps')
    {
        $this->switchTable = $switchTable;
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

    #[On('refresh-data-obe')]
    #[On('refresh-data-rps')]
    #[On('refresh-data-cpl')]
    #[On('refresh-data-cpmk')]
    #[On('refresh-data-scpmk')]
    #[On('refresh-data-ref')]
    #[On('refresh-data-tim-dosen')]
    #[On('refresh-data-user')]
    #[On('refresh-table')]
    public function refreshObesList()
    {
        $this->resetPage();
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
            'dosen' => [1 => 'id', 2 => 'dosen_id', 3 => 'kode', 4 => 'name', 5 => 'count_rps', 6 => 'total_sks', 7 => 'status', 8 => 'progtam_studi', 9 => 'created_at', 10 => 'updated_at'],
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
            'is_draf' => ['is_draf', 'indikator'],
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

    public function updatedSwitchTable($value)
    {
        $this->dispatch('switch-table-changed', table: $value);
    }

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

            $countRPS = RPS::query();
            $countCPL = CPL::query();
            $countCPMK = CPMK::query();
            $countSCPMK = SubCPMK::query();
            $countRef = Referensi::query();
            $countTimDosen = TimDosen::query();
            $countDosen = User::whereHas('dosen');

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
                    case 'dosen':
                    $queryUser->onlyTrashed();
                        break;
                }

                $countRPS->onlyTrashed();
                $countCPL->onlyTrashed();
                $countCPMK->onlyTrashed();
                $countSCPMK->onlyTrashed();
                $countRef->onlyTrashed();
                $countTimDosen->onlyTrashed();
                $countDosen->onlyTrashed();
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

            if ($this->searchMode == 'full') {
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
                'rps-saya' => '🏦',
                'rps-prodi' => '🏦',
                'rps-prodi-non-aktif' => '❌',
                'rps-akademik' => '📘',
                'rps-rev-new' => '✨',
                'rps-aktif' => '✅',
                'rps-draf' => '📝',
                'rps-older-5' => '⏳',

                'cpl-month' => '🎯',
                'cpl-6-months' => '⏱️',
                'cpl-year' => '📆',
                'cpl-older-5' => '⏳',

                'cpmk-month' => '🧩',
                'cpmk-6-months' => '⏱️',
                'cpmk-year' => '📆',
                'cpmk-older-5' => '⏳',

                'scpmk-month' => '🔗',
                'scpmk-6-months' => '⏱️',
                'scpmk-year' => '📆',
                'scpmk-older-5' => '⏳',

                'ref-year' => '📚',
                'ref-2-3-years' => '2️⃣',
                'ref-4-5-years' => '4️⃣',
                'ref-6-10-years' => '🔟',
                'ref-older-10' => '⏳',

                'tim-dosen-rps' => '✅',
                'tim-dosen-non-rps' => '❌',

                'tim-dosen-saya' => '👥',
                'tim-dosen-prodi' => '🏛️',
                'tim-dosen-all' => '👥',
                'tim-dosen-rps' => '✅',
                'tim-dosen-non-rps' => '❌',

                'dosen-rps' => '✅',
                'dosen-non-rps' => '❌',
                'dosen-prodi' => '🏛️',
                'dosen-all' => '👥',
                'dosen-aktif' => '🟢',
                'dosen-non-aktif' => '🔴',
            ];

            $stats['rps'] = (clone $countRPS)->count();
            $stats['cpl'] = (clone $countCPL)->count();
            $stats['cpmk'] = (clone $countCPMK)->count();
            $stats['scpmk'] = (clone $countSCPMK)->count();
            $stats['ref'] = (clone $countRef)->count();
            $stats['tim-dosen'] = (clone $countTimDosen)->count();
            $stats['dosen'] = (clone $countDosen)->count();

            if (Auth::user()->dosen) {
                $stats['rps-saya'] = (clone $countRPS)->whereHas('tim_dosens.dosens', function ($q) {
                    $q->where('dosens.id', Auth::user()->dosen->id);
                })->count();
            }

            // =========================
            // SWITCH STATS (TIDAK OVERWRITE)
            // =========================
            switch ($this->switchTable) {
                case 'rps':
                    $stats = array_merge($stats, $this->getStatsRps($countRPS, $currentYear, $fiveYearsAgo));
                    break;
                case 'cpl':
                    $stats = array_merge($stats, $this->getStatsKurikulum($countCPL, 'cpl', $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo));
                    break;
                case 'cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulum($countCPMK, 'cpmk', $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo));
                    break;
                case 'sub-cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulum($countSCPMK, 'scpmk', $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo));
                    break;
                case 'referensi':
                    $stats = array_merge($stats, $this->getStatsReferensi($countRef, $currentYear));
                    break;
                case 'tim-dosen':
                    $stats = array_merge($stats, $this->getStatsTimDosen($countTimDosen));
                    break;
                case 'dosen':
                    $stats = array_merge($stats, $this->getStatsDosen($countDosen));
                    break;
            }

            // =========================
            // TOTAL (NO GET)
            // =========================
            return view('livewire.staff.obe-management', array_merge($data, [
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
                'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
                'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,
                'tim_dosen_rps_modal_paginator' => $this->tim_dosen_rps_modal_paginator,
                'user_rps_modal_paginator' => $this->user_rps_modal_paginator,

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
                // 'totalRPSSaya' => '-',
                'cpl_rps_modal_paginator' => collect(),
                'cpmk_rps_modal_paginator' => collect(),
                'scpmk_rps_modal_paginator' => collect(),
                'ref_rps_modal_paginator' => collect(),
                'tim_dosen_rps_items_list' => collect(),
                'user_rps_modal_paginator' => collect(),

                'stats' => [
                    'rps' => '-',
                    'rps_saya' => '-',
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
