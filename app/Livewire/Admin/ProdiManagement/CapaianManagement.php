<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Http\Services\RekapCapaian;
use App\Livewire\Admin\ProdiManagement\CapaianManagement\WithRekapExcel;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
// use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\ObeManagement\CplManagement\WithCPLFilters;
use App\Livewire\Staff\ObeManagement\CpmkManagement\WithCPMKFilters;
use App\Livewire\Staff\ObeManagement\CpmkManagement\WithSubCPMKFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CapaianManagement extends Component
{
    use HasAkreditas;
    use HasGetByKode;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;
    use WithCPLFilters;
    use WithCPLSearchFilters;
    use WithCPMKFilters;
    use WithCPMKSearchFilters;
    use WithPagination;
    use WithProdiFilters;
    use WithProdiSearchFilters;
    use WithRekapExcel;
    use WithRPSFilters;
    use WithRPSSearchFilters;
    use WithSubCPMKFilters;
    use WithSubCPMKSearchFilters;
    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $perPage = 8;

    public $switchTable = 'capaian';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    // protected Prodi $prodi;

    public $prodi;

    public $prodi_data = [];

    public $pr_id_url;

    public $kode_pr_url;

    public $kode_pr_db;

    public $strata_db;

    // public $selectedPrId;
    public $selectedDpId;

    public $selectedFkId;

    public $selectedRPSId;

    public $selectedCPLId;

    public $selectedCPMKId;

    public $selectedSCPMKId;

    public $isProdiDsn;

    protected $listeners = [
        'refresh-table' => 'refreshCapaiansList',
        'refresh-data-capaian' => 'refreshCapaiansList',
        'refresh-data-obe' => 'refreshCapaiansList',
        'refresh-data-rps' => 'refreshCapaiansList',
        'refresh-data-cpl' => 'refreshCapaiansList',
        'refresh-data-cpmk' => 'refreshCapaiansList',
        'refresh-data-scpmk' => 'refreshCapaiansList',
        'refresh-data-ref' => 'refreshCapaiansList',
        'refresh-data-user' => 'refreshCapaiansList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterRPS' => ['except' => ''],
        'filterCPL' => ['except' => ''],
        'filterCPMK' => ['except' => ''],
        'filterSCPMK' => ['except' => ''],

        'filterStatus' => ['except' => ''],
        // 'switchTable' => ['except' => 'capaian'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

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

    #[On('refresh-data-capaian')]
    #[On('refresh-data-obe')]
    #[On('refresh-data-rps')]
    #[On('refresh-data-cpl')]
    #[On('refresh-data-cpmk')]
    #[On('refresh-data-scpmk')]
    #[On('refresh-data-ref')]
    #[On('refresh-data-user')]
    #[On('refresh-table')]
    public function refreshCapaiansList()
    {
        $this->resetPage();
    }

    #[On('refresh-stats-obe')]
    public function refreshStatsOBEsList()
    {
        $this->clearObeStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
    }

    #[On('refresh-stats-rps')]
    public function refreshStatsRPSsList()
    {
        $this->clearObeStatsCache();
        $this->clearRpsStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
        $this->clearRpsProdiStatsCache($this->pr_id_url);
    }

    #[On('refresh-stats-cpl')]
    public function refreshStatsCPLsList()
    {
        $this->clearObeStatsCache();
        $this->clearCplStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
        $this->clearCplProdiStatsCache($this->pr_id_url);
    }

    #[On('refresh-stats-cpmk')]
    public function refreshStatsCPMKsList()
    {
        $this->clearObeStatsCache();
        $this->clearCpmkStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
        $this->clearCpmkProdiStatsCache($this->pr_id_url);
    }

    #[On('refresh-stats-scpmk')]
    public function refreshStatsSubCPMKsList()
    {
        $this->clearObeStatsCache();
        $this->clearScpmkStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
        $this->clearScpmkProdiStatsCache($this->pr_id_url);
    }

    #[On('refresh-stats-user')]
    public function refreshStatsUsersList()
    {
        $this->clearUserStatsCache();
        $this->clearObeStatsCache();
        // $this->clearDosenStatsCache();
        $this->clearObeProdiStatsCache($this->pr_id_url);
        $this->clearMahasiswaProdiStatsCache($this->pr_id_url);
    }

    public function refreshStats()
    {
        $this->refreshStatsOBEsList();
        $this->refreshStatsRPSsList();
        $this->refreshStatsCPLsList();
        $this->refreshStatsCPMKsList();
        $this->refreshStatsSubCPMKsList();
        $this->refreshStatsUsersList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik OBE berhasil diperbarui!', type: 'info', variant: 'info');
    }

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

    protected function loadProdiData()
    {
        if (empty($this->kode_pr_db) && ! empty($this->kode_pr_url)) {
            [$strata, $kode] = array_pad(explode('-', $this->kode_pr_url, 2), 2, null);
            $strataDb = match (strtoupper($strata ?? '')) {
                'S1' => 'Sarjana',
                'S2' => 'Magister',
                'S3' => 'Doktor',
                default => null,
            };

            $this->kode_pr_db = $kode;
            $this->strata_db = $strataDb;
        }
        $baseQuery = Prodi::query()
            ->with(['dp_rel.fk_rel'])
            ->where(function ($q) {
                $q->where('kode_pr', $this->kode_pr_db)
                    ->orWhereHas('dp_rel', function ($q2) {
                        $q2->where('kode_dp', $this->kode_pr_db);
                    })
                    ->orWhereHas('dp_rel.fk_rel', function ($q3) {
                        $q3->where('kode_fk', $this->kode_pr_db);
                    });

                if ($this->kode_pr_db === 'UNI') {
                    $q->orWhereHas('dp_rel.fk_rel', function ($q4) {
                        $q4->whereNotNull('id');
                    });
                }
            })
            ->where('strata', $this->strata_db);

        $prodi = $baseQuery->first();

        if (! $prodi) {
            return null;
        }

        $aggQuery = Prodi::query()->where('prodis.id', $prodi->id);
        // $this->addRekapProdiPr($aggQuery, 'rekap_pr');
        // $this->addIndexProdiPr($aggQuery, 'index_pr');
        // $this->addAkreditasProdiPr($aggQuery, 'akreditas_pr');
        $this->addMataKuliahProdiPr($aggQuery, 'count_mk', 'count_rps', 'count_rps_aktif', 'count_rps_draf');

        $agg = $aggQuery->first();
        if ($agg) {
            foreach ($agg->getAttributes() as $k => $v) {
                if (! array_key_exists($k, $prodi->getAttributes()) || $k === 'rekap_pr' || $k === 'index_pr' || $k === 'akreditas_pr' || str_starts_with($k, 'count_')) {
                    $prodi->setAttribute($k, $v);
                }
            }
        }

        return $prodi;
    }

    // private function normalizeSwitchTable(?string $switchTable): string
    // {
    //     return match ($switchTable) {
    //         'cpl' => 'capaian',
    //         'sub-cpmk' => 'sub-cpmk',
    //         default => $switchTable ?? 'capaian',
    //     };
    // }

    // private function externalSwitchTableSegment(string $switchTable): string
    // {
    //     return match ($switchTable) {
    //         'capaian' => '',
    //         'sub-cpmk' => '/sub-cpmk',
    //         default => "/$switchTable",
    //     };
    // }

    public function mount(
        $isProdiDsn = false,
        $kode_pr = null,
        $switchTable = 'cpl'
    ) {
        $this->isProdiDsn = $isProdiDsn;

        $this->kode_pr_url = $kode_pr;
        // $this->switchTable = $this->normalizeSwitchTable($switchTable);

        $this->updatedShowDeleted();

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

        $this->kode_pr_db = $kode;
        $this->strata_db = $strataDb;

        $prodi = $this->loadProdiData();

        // dump($prodi, $kode_pr, $switchTable);
        $this->prodi_data['id'] = $prodi->id ?? null;
        $this->prodi_data['kode_pr'] = $prodi->kode_pr ?? null;
        $this->prodi_data['kode_fk'] = $prodi->kode_fk ?? null;
        $this->prodi_data['prodi'] = $prodi->prodi ?? null;
        $this->prodi_data['fakultas_fk'] = $prodi->fakultas_fk ?? null;
        $this->prodi_data['akreditas_pr'] = $prodi->akreditas_pr ?? null;
        $this->prodi_data['rekap_pr'] = $prodi->rekap_pr ?? null;
        $this->prodi_data['index_pr'] = $prodi->index_pr ?? null;
        $this->prodi_data['akreditas_pr'] = $prodi->akreditas_pr ?? null;
        $this->prodi_data['count_mk'] = $prodi->count_mk ?? null;
        $this->prodi_data['count_rps_aktif'] = $prodi->count_rps_aktif ?? null;

        if (! $prodi) {
            abort(404, 'Data tidak ditemukan!');
        }

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

        $this->pr_id_url = $prodi->id ?? null;

        $historyKey = 'prodi.history';
        $history = session($historyKey, []);
        $compositeKey = $prodi->kode_pr ?? null;
        $existingKey = array_search($prodi->id, array_column($history, 'pr_id'));
        if ($existingKey !== false) {
            $actualKeys = array_keys($history);
            unset($history[$actualKeys[$existingKey]]);
        }
        unset($history[$compositeKey]);

        $history[$compositeKey] = [
            'pr_id' => $prodi->id ?? null,
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

    public function loadingRPSssList() {}

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
            'capaian' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpl_pr', 5 => 'index_cpl_pr', 5 => 'mutu_cpl_pr', 6 => 'count_rps_pr', 7 => 'count_rps'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpmk_pr', 5 => 'index_cpmk_pr', 6 => 'mutu_cpmk_pr', 7 => 'count_cpl'],
            'sub-cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_scpmk_pr', 5 => 'index_scpmk_pr', 6 => 'mutu_scpmk_pr', 5 => 'metode', 6 => 'materi', 7 => 'metodologi', 8 => 'indikator'],
            'mahasiswa' => [1 => 'kode', 2 => 'name', 3 => 'rekap_mhs', 4 => 'ipk_mhs', 7 => 'mutu_mhs', 6 => 'count_rps', 7 => 'total_sks', 8 => 'angkatan', 9 => 'status', 10 => 'kampus', 11 => 'program_studi'],
            // 'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'kode', 4 => 'name', 5 => 'rekap_mhs', 6 => 'ipk_mhs', 7 => 'mutu_mhs', 8 => 'count_rps', 9 => 'total_sks', 10 => 'angkatan', 11 => 'status'],
        ];

        $aliases = [
            'rekap_rps_pr' => ['rekap_cpl_pr', 'rekap_cpmk_pr', 'rekap_scpmk_pr', 'rekap_mhs'],
            'index_rps_pr' => ['index_cpl_pr', 'index_cpmk_pr', 'index_scpmk_pr', 'ipk_mhs'],
            'mutu_rps_pr' => ['mutu_cpl_pr', 'mutu_cpmk_pr', 'mutu_scpmk_pr', 'mutu_mhs'],

            'kode' => ['kode', 'name'],
            'deskripsi' => ['deskripsi', 'mk'],
            'materi' => ['materi'],
            'count_cpl' => ['count_cpl', 'count_rps', 'total_sks'],
            'is_draf' => ['is_draf', 'indikator', 'status'],
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
            'capaian' => 'filterCPL',
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
            'capaian' => 100,
            'rps' => 200,
            'mahasiswa' => 200,
            'cpmk' => 300,
            'sub-cpmk' => 500,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        $suffix = ($table && $table !== 'cpl') ? "/{$table}" : '';

        $targetPath = "/program-studi-management/kode/{$this->kode_pr_url}{$suffix}";

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
            // Ensure prodi is loaded on every render to avoid Livewire serializing an Eloquent
            // model into the public payload and ending up with an empty model after hydrate.
            // $this->prodi = $this->loadProdiData();
            $prId = $this->pr_id_url;

            $queryRPS = collect();
            $queryCPL = collect();
            $queryCPMK = collect();
            $querySCPMK = collect();
            $queryUser = collect();

            switch ($this->switchTable) {
                case 'rps':
                    $queryRPS = $this->inputRPSSearch(null, null, 1);
                    break;
                case 'cpl':
                    $queryCPL = $this->inputCPLSearch($prId);
                    break;
                case 'cpmk':
                    $queryCPMK = $this->inputCPMKSearch($prId);
                    break;
                case 'sub-cpmk':
                    $querySCPMK = $this->inputSCPMKSearch($prId);
                    break;
                case 'mahasiswa':
                    $queryUser = $this->inputUserSearch('mahasiswa', null, $prId);
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
                }
            }

            if (Auth::user()->admin) {
                if ($this->showDeleted && $this->AuthCheck('admin')) {
                    if ($this->switchTable == 'mahasiswa') {
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
                    $this->addRekapProdi($queryRPS, $prId, 'rekap_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addIndexProdi($queryRPS, $prId, 'index_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addAkreditasProdi($queryRPS, $prId, 'mutu_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year, $prId);
                    break;
                case 'cpl':
                    $this->addCountRpsCpl($queryCPL, $prId, 'count_rps_pr');
                    $this->addCountRpsCpl($queryCPL, null, 'count_rps');

                    $this->addRekapProdi($queryCPL, $prId, 'rekap_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addIndexProdi($queryCPL, $prId, 'index_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addAkreditasProdi($queryCPL, $prId, 'mutu_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'cpmk':
                    $this->addRekapProdi($queryCPMK, $prId, 'rekap_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addIndexProdi($queryCPMK, $prId, 'index_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addAkreditasProdi($queryCPMK, $prId, 'mutu_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'sub-cpmk':
                    $this->addRekapProdi($querySCPMK, $prId, 'rekap_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addIndexProdi($querySCPMK, $prId, 'index_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addAkreditasProdi($querySCPMK, $prId, 'mutu_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'mahasiswa':
                    // $this->addRekapMahasiswa($queryUser, 'rekap_mhs');
                    // $this->addIndexMahasiswa($queryUser, 'ipk_mhs');
                    // $this->addMutuMahasiswa($queryUser, 'mutu_mhs');
                    // $this->addCountRpsMahasiswa($queryUser, 'count_rps');
                    // $this->addTotalSksMahasiswa($queryUser, 'total_sks');
                    $this->addCountRpsMahasiswa($queryUser, 'count_rps');
                    $this->addTotalSksMahasiswa($queryUser, 'total_sks');
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

                'mahasiswa-aktif' => '🟢',
                'mahasiswa-non-aktif' => '🔴',
            ];

            // $stats['rps'] = (clone $countRPS)->count();
            // $stats['cpl'] = (clone $countCPL)->count();
            // $stats['cpmk'] = (clone $countCPMK)->count();
            // $stats['scpmk'] = (clone $countSCPMK)->count();
            // $stats['mahasiswa'] = (clone $countMahasiswa)->count();
            $stats = array_merge($stats, $this->getStatsObeProdi($this->showDeleted, $prId));

            switch ($this->switchTable) {
                case 'rps':
                    // $stats = array_merge($stats, $this->getStatsRps($this->showDeleted));
                    $stats = array_merge($stats, $this->getStatsRpsProdi($this->showDeleted, $prId));
                    break;
                case 'cpl':
                    $stats = array_merge($stats, $this->getStatsKurikulumProdi('cpl', $this->showDeleted, $prId));
                    break;
                case 'cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulumProdi('cpmk', $this->showDeleted, $prId));
                    break;
                case 'sub-cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulumProdi('scpmk', $this->showDeleted, $prId));
                    break;
                case 'mahasiswa':
                    $stats = array_merge($stats, $this->getStatsMahasiswaProdi($this->showDeleted, $prId));
                    break;
            }

            return view('livewire.admin.prodi-management.capaian-management', array_merge($data, [
                // 'users' => $users,
                // 'prodi' => $this->prodi,
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

                    'capaian' => '-',
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
