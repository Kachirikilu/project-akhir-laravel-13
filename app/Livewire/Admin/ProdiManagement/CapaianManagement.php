<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Http\Services\RekapCapaian;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;

use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Global\WithMKSearchFilters;


// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
// use App\Livewire\Staff\OBEManagement\WithRPSDelete;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKFilters;
use App\Livewire\Admin\UserManagement\WithUserFilters;

use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSModal;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKModal;
use App\Livewire\Staff\OBEManagement\ReferensiManagement\WithRefModal;
use App\Livewire\Admin\UserManagement\WithUserModal;

use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSDelete;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKDelete;
use App\Livewire\Admin\UserManagement\WithUserDelete;

use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\ProgramStudi\Prodi;
use App\Models\Auth\User;

use App\Livewire\Admin\ProdiManagement\CapaianManagement\WithRekapExcel;

use Livewire\Component;
use Livewire\WithPagination;

class CapaianManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;

    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;

    use WithRPSSearchFilters;
    use WithCPLSearchFilters;
    use WithCPMKSearchFilters;
    use WithSubCPMKSearchFilters;
    use WithUserSearchFilters;
    use WithReferensiSearchFilters;
    use WithMKSearchFilters;

    use WithRPSFilters;
    use WithCPLFilters;
    use WithCPMKFilters;
    use WithSubCPMKFilters;
    use WithUserFilters;

    use WithRPSModal;
    use WithCPLModal;
    use WithCPMKModal;
    use WithSubCPMKModal;
    use WithRefModal;
    use WithUserModal;

    use WithRPSDelete;
    use WithCPLDelete;
    use WithCPMKDelete;
    use WithSubCPMKDelete;
    use WithUserDelete;

    use WithRekapExcel;

    use WithPagination;




    public $perPage = 8;

    public $switchTable = 'cpl';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public Prodi $prodi;

    public $pr_id_url;

    public $kode_pr_url;

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterRPS' => ['except' => ''],
        'filterCPL' => ['except' => ''],
        'filterCPMK' => ['except' => ''],
        'filterSCPMK' => ['except' => ''],

        'filterStatus' => ['except' => ''],
        // 'switchTable' => ['except' => 'cpl'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' =>  ['except' => false],
    ];

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

    public function mount(
        $kode_pr = null,
        $switchTable = 'cpl'
    ) {
        $this->kode_pr_url = $kode_pr;
        $this->switchTable = $switchTable;

        [$strata, $kode] = array_pad(
            explode('-', $kode_pr, 2),
            2,
            null
        );

        $strataDb = match (strtoupper($strata ?? '')) {
            'S1' => 'Sarjana',
            'S2' => 'Magister',
            'S3' => 'Doktor',
            default => null,
        };

        $prodi = Prodi::query()
            ->with(['dp_rel.fk_rel'])
            ->where(function ($query) use ($kode) {

                $query->where('kode_pr', $kode)

                    ->orWhereHas('dp_rel', function ($q) use ($kode) {
                        $q->where('kode_dp', $kode);
                    })

                    ->orWhereHas('dp_rel.fk_rel', function ($q) use ($kode) {
                        $q->where('kode_fk', $kode);
                    });

                if ($kode === 'UNI') {
                    $query->orWhereHas('dp_rel.fk_rel', function ($q) {
                        $q->whereNotNull('id');
                    });
                }
            })
            ->where('strata', $strataDb)
            ->first();

        if (! $prodi) {
            foreach (['prodi.history', 'capaian.history'] as $key) {
                $history = session($key, []);
                if (isset($history[$kode_pr])) {
                    unset($history[$kode_pr]);
                    session([$key => $history]);
                }
            }
            abort(404, "Program Studi dengan Kode $kode_pr tidak ditemukan!");
        }

        $this->prodi = $prodi;
        $this->pr_id_url = $this->prodi->id;

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

        $historyKey = 'prodi.history'; 
        $history = session($historyKey, []);
        $compositeKey = $prodi->kode;
        $existingKey = array_search($prodi->id, array_column($history, 'pr_id'));
        if ($existingKey !== false) {
            $actualKeys = array_keys($history);
            unset($history[$actualKeys[$existingKey]]);
        }
        unset($history[$compositeKey]);

        $history[$compositeKey] = [
            'pr_id' => $prodi->id,
            'kode_pr' => $compositeKey,
            'url' => url()->current(),
        ];

        $history = array_slice($history, -5, null, true);
        ksort($history);

        session([
            $historyKey => $history,
        ]);
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

    public function refreshProdisList()
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
            // 'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'rekap_rps_pr', 5 => 'index_rps_pr', 6 => 'mutu_rps_pr', 7 => 'kode_mk', 8 => 'mk', 9 => 'semester', 10 => 'sks', 11 => 'sks_text', 12 => 'is_wajib', 13 => 'is_draf', 14 => 'revisi', 15 => 'created_at', 16 => 'updated_at'],
            // 'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpl_pr', 5 => 'index_cpl_pr', 5 => 'mutu_cpl_pr', 6 => 'count_rps_pr', 7 => 'count_rps', 8 => 'created_at', 9 => 'updated_at'],
            // 'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpmk_pr', 5 => 'index_cpmk_pr', 6 => 'mutu_cpmk_pr', 7 => 'count_cpl', 8 => 'created_at', 9 => 'updated_at'],
            // 'sub-cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_scpmk_pr', 5 => 'index_scpmk_pr', 6 => 'mutu_scpmk_pr', 5 => 'metode', 6 => 'materi', 7 => 'metodologi', 8 => 'indikator', 9 => 'created_at', 10 => 'updated_at'],
            // 'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'kode', 4 => 'name', 5 => 'rekap_mhs', 6 => 'ip_mhs', 7 => 'mutu_mhs', 8 => 'count_rps', 9 => 'total_sks', 10 => 'angkatan', 11 => 'status', 12 => 'created_at', 13 => 'updated_at'],
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'rekap_rps_pr', 5 => 'index_rps_pr', 6 => 'mutu_rps_pr', 7 => 'kode_mk', 8 => 'mk', 9 => 'semester', 10 => 'sks', 11 => 'sks_text', 12 => 'is_wajib', 13 => 'is_draf', 14 => 'revisi'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpl_pr', 5 => 'index_cpl_pr', 5 => 'mutu_cpl_pr', 6 => 'count_rps_pr', 7 => 'count_rps'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpmk_pr', 5 => 'index_cpmk_pr', 6 => 'mutu_cpmk_pr', 7 => 'count_cpl'],
            'sub-cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_scpmk_pr', 5 => 'index_scpmk_pr', 6 => 'mutu_scpmk_pr', 5 => 'metode', 6 => 'materi', 7 => 'metodologi', 8 => 'indikator'],
            'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'kode', 4 => 'name', 5 => 'rekap_mhs', 6 => 'ip_mhs', 7 => 'mutu_mhs', 8 => 'count_rps', 9 => 'total_sks', 10 => 'angkatan', 11 => 'status'],
        ];

        $aliases = [
            'rekap_rps_pr' => ['rekap_cpl_pr', 'rekap_cpmk_pr', 'rekap_scpmk_pr', 'rekap_mhs'],
            'index_rps_pr' => ['index_cpl_pr', 'index_cpmk_pr', 'index_scpmk_pr', 'ip_mhs'],
            'mutu_rps_pr' => ['mutu_cpl_pr', 'mutu_cpmk_pr', 'mutu_scpmk_pr', 'mutu_mhs'],

            'kode' => ['kode', 'name'],
            // 'name' => ['name', 'kode'],
            'deskripsi' => ['deskripsi', 'mk'],
            // 'mk' => ['mk', 'deskripsi'],
            'materi' => ['materi'],
            // 'count_rps' => ['count_rps', 'count_cpl'],
            'count_cpl' => ['count_cpl', 'count_rps', 'total_sks'],
            // 'akademik' => ['akademik', 'total_bobot'],
            // 'total_bobot' => ['total_bobot', 'akademik'],
            'is_draf' => ['is_draf', 'indikator'],
            // 'indikator' => ['indikator', 'is_draf'],
            // 'created_at' => ['created_at'],
            // 'updated_at' => ['updated_at'],
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
            'mahasiswa' => 'filterStatus',
        ];

        foreach ($allFilters as $tableParam => $filterVariable) {
            if ($tableParam !== $this->switchTable) {
                $this->$filterVariable = '';
            }
        }

        $limits = [
            'cpl' => 100,
            'rps' => 200,
            'mahasiswa' => 200,
            'cpmk' => 300,
            'sub-cpmk' => 500,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        // $targetPath = '/program-studi-management/kode/'.($table ? '/'.$table : '');
        $suffix = ($table && $table !== 'cpl') ? "/{$table}" : '';

        $targetPath = "/program-studi-management/kode/{$this->kode_pr_url}{$suffix}";
        
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        $prId = $this->pr_id_url;
        $queryRPS = $this->inputRPSSearch($prId, null , 1);
        $queryCPL = $this->inputCPLSearch($prId);
        $queryCPMK = $this->inputCPMKSearch($prId);
        $querySCPMK = $this->inputSCPMKSearch($prId);
        $queryUser = $this->inputUserSearch('mahasiswa', null, $prId);

        // $this->inputPrFilter();
        $this->inputRPSFilter();
        $this->inputCPLFilter();
        $this->inputCPMKFilter();


        try {
            $countRPS = RPS::where(function ($q) use ($prId) {
                $q->whereRelation('mk_rel.prodis', 'prodis.id', $prId);
            });
            $countCPL = CPL::where(function ($q) use ($prId) {
                $q->whereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $prId)
                    ->orWhereRelation('prodis', 'prodis.id', $prId);
            });
            $countCPMK = CPMK::where(function ($q) use ($prId) {
                $q->whereRelation('rps.mk_rel.prodis', 'prodis.id', $prId);
            });
            $countSCPMK = SubCPMK::where(function ($q) use ($prId) {
                $q->whereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $prId);
            });

            $countMahasiswa = User::where(function ($q) use ($prId) {
                $q->whereRelation('mahasiswa.pr_rel', 'prodis.id', $prId);
            });;

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryRPS->onlyTrashed();
                $queryCPL->onlyTrashed();
                $queryCPMK->onlyTrashed();
                $querySCPMK->onlyTrashed();
                $queryUser->onlyTrashed();

                $countRPS->onlyTrashed();
                $countCPL->onlyTrashed();
                $countCPMK->onlyTrashed();
                $countSCPMK->onlyTrashed();
                $countMahasiswa->onlyTrashed();
            }

            // =========================
            // TIME SETUP
            // =========================
            $now = now();
            $sixMonthsAgo = now()->subMonths(6);
            $currentYear = now()->year;
            // $threeYearsAgo = now()->subYears(3);
            $fiveYearsAgo = now()->subYears(5);
            // $tenYearsAgo = now()->subYears(10);

            $data = [
                'rps' => collect(),
                'cpl' => collect(),
                'cpmk' => collect(),
                'scpmk' => collect(),
                'users' => collect(),
            ];

            switch ($this->switchTable) {
                case 'rps':
                    $this->addRekapProdi($queryRPS, $this->pr_id_url, 'rekap_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addIndexProdi($queryRPS, $this->pr_id_url, 'index_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addAkreditasProdi($queryRPS, $this->pr_id_url, 'mutu_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
                    break;
                case 'cpl':
                    $this->addCountRpsCpl($queryCPL, $this->pr_id_url, 'count_rps_pr');
                    $this->addCountRpsCpl($queryCPL, null, 'count_rps');

                    $this->addRekapProdi($queryCPL, $this->pr_id_url, 'rekap_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addIndexProdi($queryCPL, $this->pr_id_url, 'index_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addAkreditasProdi($queryCPL, $this->pr_id_url, 'mutu_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'cpmk':
                    $this->addRekapProdi($queryCPMK, $this->pr_id_url, 'rekap_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addIndexProdi($queryCPMK, $this->pr_id_url, 'index_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addAkreditasProdi($queryCPMK, $this->pr_id_url, 'mutu_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'sub-cpmk':
                    $this->addRekapProdi($querySCPMK, $this->pr_id_url, 'rekap_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addIndexProdi($querySCPMK, $this->pr_id_url, 'index_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addAkreditasProdi($querySCPMK, $this->pr_id_url, 'mutu_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                // case 'mahasiswa':
                //     $this->addRekapMahasiswa($queryUser, 'rekap_mhs');
                //     $this->addIndexMahasiswa($queryUser, 'ip_mhs');
                //     $this->addMutuMahasiswa($queryUser, 'mutu_mhs');
                //     $this->addCountRpsMahasiswa($queryUser, 'count_rps');
                //     $this->addTotalSksMahasiswa($queryUser, 'total_sks');
                //     break;
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
                    case 'mahasiswa':
                        $data['users'] = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, null, 1);
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
                    case 'mahasiswa':
                        $data['users'] = $queryUser->paginate($this->perPage);
                        break;
                }
            }

            $stats = [
                'rps-prodi' => '🏦',
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

                'mahasiswa-aktif' => '🟢',
                'mahasiswa-non-aktif' => '🔴',
            ];

            $stats['rps'] = (clone $countRPS)->count();
            $stats['cpl'] = (clone $countCPL)->count();
            $stats['cpmk'] = (clone $countCPMK)->count();
            $stats['scpmk'] = (clone $countSCPMK)->count();
            $stats['mahasiswa'] = (clone $countMahasiswa)->count();

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
                case 'mahasiswa':
                    $stats = array_merge($stats, $this->getStatsMahasiswa($countMahasiswa));
                    break;
            }

            return view('livewire.admin.prodi-management.capaian-management', array_merge($data, [
                // 'users' => $users,
                'prodi' => $this->prodi,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator,
                'scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator,
                'ref_rps_modal_paginator' => $this->ref_rps_modal_paginator,
                'user_rps_modal_paginator' => $this->user_rps_modal_paginator,
                'stats' => $stats,
            ]));

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.prodi-management.capaian-management', [
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
                'cpl' => CPL::whereRaw('1=0')->paginate($this->perPage),
                'cpmk' => CPMK::whereRaw('1=0')->paginate($this->perPage),
                'scpmk' => SubCPMK::whereRaw('1=0')->paginate($this->perPage),
                'users' => User::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
            ], [
                // 'totalRPSSaya' => '-',
                // 'totalDosenProdi' => '-',
                // 'totalDosen' => '-',

                'cpl_rps_modal_paginator' => collect(),
                'cpmk_rps_modal_paginator' => collect(),
                'scpmk_rps_modal_paginator' => collect(),
                'ref_rps_modal_paginator' => collect(),
                'mahasiswa_rps_modal_paginator' => collect(),

                'stats' => [
                    'rps' => '-',
                    'rps-prodi' => '-',
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

                    'mahasiswa-aktif' => '-',
                    'mahasiswa-non-aktif' => '-',
                ],
            ]);
        }
    }
}
