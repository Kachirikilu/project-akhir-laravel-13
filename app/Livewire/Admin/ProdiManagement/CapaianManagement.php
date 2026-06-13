<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Http\Services\RekapCapaian;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
// use App\Livewire\Staff\RPSManagement\WithRPSFilters;
// use App\Livewire\Staff\RPSManagement\WithRPSDelete;
use App\Livewire\Staff\CPLManagement\WithCPLDelete;
use App\Livewire\Staff\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\CPLManagement\WithCPLModal;
use App\Livewire\Staff\CPMKManagement\WithCPMKFilters;
use App\Livewire\Staff\CPMKManagement\WithSubCPMKFilters;
use App\Livewire\Staff\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\RPSManagement\WithRPSModal;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class CapaianManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;
    use WithCPLDelete;
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLSearchFilters;
    use WithCPMKFilters;
    use WithCPMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSFilters;

    // use WithRPSFilters;
    // use WithRPSDelete;
    use WithRPSModal;
    use WithRPSSearchFilters;
    use WithSubCPMKFilters;

    public $showModal = false;

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
        'filterPr' => ['except' => ''],
        'switchTable' => ['except' => 'cpl'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

    public function mount(
        $kode_pr = null,
    ) {
        $this->kode_pr_url = $kode_pr;

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

        $this->prodi = Prodi::query()
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
            ->firstOrFail();

        $this->pr_id_url = $this->prodi->id;
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
        // $this->reset(['search', 'filterPr']);
        $this->reset('search');
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
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'kode_mk', 5 => 'mk', 6 => 'sks', 7 => 'sks_text', 8 => 'is_wajib', 9 => 'count-cpmk', 10 => 'count-scpmk', 11 => 'total_bobot', 12 => 'is_draf', 13 => 'revisi', 14 => 'created_at', 15 => 'updated_at'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count_rps', 5 => 'created_at', 6 => 'updated_at'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'count_cpl', 5 => 'count-scpmk', 6 => 'total_bobot', 7 => 'created_at', 8 => 'updated_at'],
            'scpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'metode', 5 => 'materi', 6 => 'metodologi', 7 => 'indikator', 8 => 'bobot', 9 => 'tugas', 10 => 'w_tugas', 11 => 'w_mandiri', 12 => 'created_at', 13 => 'updated_at'],
        ];
        $aliases = [
            'kode' => ['kode', 'name'],
            'name' => ['name', 'kode'],
            'deskripsi' => ['deskripsi', 'mk', 'judul'],
            'mk' => ['mk', 'deskripsi', 'judul'],
            'judul' => ['judul', 'deskripsi', 'mk'],
            'materi' => ['materi', 'penulis'],
            'penulis' => ['penulis', 'materi'],
            'count_rps' => ['count_rps', 'count_cpl'],
            'count_cpl' => ['count_cpl', 'count_rps'],
            'akademik' => ['akademik', 'bobot', 'total_bobot'],
            'bobot' => ['bobot', 'akademik', 'total_bobot'],
            'total_bobot' => ['total_bobot', 'akademik', 'bobot'],
            'is_draf' => ['is_draf', 'indikator'],
            'indikator' => ['indikator', 'is_draf'],
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
        ];

        foreach ($allFilters as $tableParam => $filterVariable) {
            if ($tableParam !== $this->switchTable) {
                $this->$filterVariable = '';
            }
        }

        $limits = [
            'cpl' => 100,
            'rps' => 200,
            'cpmk' => 300,
            'sub-cpmk' => 500,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        // $targetPath = '/obe-management'.($table ? '/'.$table : '');
        // $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        $prId = $this->pr_id_url;
        $queryRPS = $this->inputRPSSearch($prId);
        $queryCPL = $this->inputCPLSearch($prId);
        $queryCPMK = $this->inputCPMKSearch($prId);
        $querySCPMK = $this->inputSCPMKSearch($prId);

        // $this->inputPrFilter();
        $this->inputRPSFilter();
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

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryCPL->onlyTrashed();
                $countCPL->onlyTrashed();
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
            ];

            switch ($this->switchTable) {
                case 'rps':
                    $this->addRekapProdi($queryRPS, $this->pr_id_url, 'rekap_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addIndexProdi($queryRPS, $this->pr_id_url, 'index_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->addAkreditasProdi($queryRPS, $this->pr_id_url, 'akreditas_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                    $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);
                    break;
                case 'cpl':
                    $this->addCountRpsCpl($queryCPL, $this->pr_id_url, 'count_rps_pr');
                    $this->addCountRpsCpl($queryCPL, null, 'count_rps');

                    $this->addRekapProdi($queryCPL, $this->pr_id_url, 'rekap_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addIndexProdi($queryCPL, $this->pr_id_url, 'index_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->addAkreditasProdi($queryCPL, $this->pr_id_url, 'akreditas_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                    $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'cpmk':
                    $this->addRekapProdi($queryCPMK, $this->pr_id_url, 'rekap_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addIndexProdi($queryCPMK, $this->pr_id_url, 'index_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->addAkreditasProdi($queryCPMK, $this->pr_id_url, 'akreditas_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                    $this->buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                    break;
                case 'sub-cpmk':
                    $this->addRekapProdi($querySCPMK, $this->pr_id_url, 'rekap_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addIndexProdi($querySCPMK, $this->pr_id_url, 'index_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->addAkreditasProdi($querySCPMK, $this->pr_id_url, 'akreditas_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                    $this->buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
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
            ];

            $stats['rps'] = (clone $countRPS)->count();
            $stats['cpl'] = (clone $countCPL)->count();
            $stats['cpmk'] = (clone $countCPMK)->count();
            $stats['scpmk'] = (clone $countSCPMK)->count();

            switch ($this->switchTable) {
                case 'rps':
                    $stats = array_merge($stats, $this->getStatsRps($countRPS, $currentYear, $fiveYearsAgo));
                    break;
                case 'cpl':
                    $stats = array_merge($stats, $this->getStatsKurikulum($countCPL, 'cpl', $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo));
                    break;
                case 'sub-cpmk':
                    $stats = array_merge($stats, $this->getStatsKurikulum($countSCPMK, 'scpmk', $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo));
                    break;
            }

            return view('livewire.admin.prodi-management.capaian-management', array_merge($data, [
                'prodi' => $this->prodi,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
                'stats' => $stats,
            ]));

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.prodi-management.capaian-management', [
                'cpls' => CPL::whereRaw('1=0')->paginate($this->perPage),
                'prodis' => $this->prodi,
                'cpl_rps_modal_paginator' => collect(),
            ]);
        }
    }
}
