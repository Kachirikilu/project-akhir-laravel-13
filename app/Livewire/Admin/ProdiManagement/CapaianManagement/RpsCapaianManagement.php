<?php

namespace App\Livewire\Admin\ProdiManagement\CapaianManagement;

use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Models\Akademik\CPL;
use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class RpsCapaianManagement extends Component
{
    use HasSortir;
    use HasToast;
    use WithPagination;
    use WithRPSFilters;
    use WithRPSSearchFilters;



    public $perPage = 8;

    public $switchTable = 'rps';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public Prodi $prodi;

    public $pr_id_url;

    public $kode_pr_url;

    public CPL $cpl;

    public $cpl_id_url;

    public $kode_cpl_url;

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterPr' => ['except' => ''],
        // 'switchTable' => ['except' => 'prodi'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' =>  ['except' => false],
    ];

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

    public function mount(
        $kode_cpl = null,
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


        if ($kode_cpl) {

            $parts = explode('-', strtoupper($kode_cpl), 2);

            $prefix = $parts[0] ?? null;
            $kodeAsli = $parts[1] ?? null;

            $this->cpl = CPL::with('prodis.dp_rel.fk_rel')
                ->where('kode_cpl', $kodeAsli)
                ->get()
                ->first(function ($cpl) use ($prefix) {

                    $prodi = $cpl->prodis->first();

                    if (! $prodi) {
                        return false;
                    }

                    $expectedPrefix = match ((int) $cpl->level_cpl) {
                        1 => $prodi->kode_pr,
                        2 => $prodi->dp_rel?->kode_dp,
                        3 => $prodi->dp_rel?->fk_rel?->kode_fk,
                        4 => 'UNI',
                        default => null,
                    };

                    return strtoupper($expectedPrefix) === strtoupper($prefix);
                });

            abort_if(! $this->cpl, 404);

            $this->cpl_id_url = $this->cpl->id;
            $this->kode_cpl_url = $this->cpl->kode;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterPr']);
        $this->resetPage();
    }

    public function refreshProdisList()
    {
        $this->resetPage();
    }


    private function syncSortField($table, $sortField)
    {
        if ($sortField != 'id' && $sortField != 'kode') {
            if ($table === 'prodi') {
                $this->sortField = 'prodi';
            } elseif ($table === 'departemen') {
                $this->sortField = 'departemen';
                $this->filterPr = '';
            } elseif ($table === 'fakultas') {
                $this->sortField = 'fakultas';
                $this->filterPr = '';
            }
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);

        $limits = [
            'prodi' => 75,
            'departemen' => 50,
            'fakultas' => 10,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        $this->resetPage();

        $targetPath = '/program-studi-management'.(in_array($table, ['prodi', '', null], true) ? '' : '/'.$table);
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function buttonStrataFilter($queryPr)
    {
        if (in_array($this->filterPr, ['sarjana', 'magister', 'doktor'])) {
            $queryPr->where('strata', ucfirst($this->filterPr));
        }
    }

    public function render()
    {
        // $this->inputDpFilter();
        // $this->inputFkFilter();

        // $queryPr = $this->inputPrSearch();
        // $queryDp = $this->inputDpSearch();
        // $queryFk = $this->inputFkSearch();
        $queryRPS = $this->inputRPSSearch($this->pr_id_url, $this->cpl_id_url);

        try {

            // =========================
            // SOFT DELETE
            // =========================
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryRPS = $queryRPS->onlyTrashed();
            }

            // =========================
            // PAGINATION
            // =========================

            $now = now();
            $sixMonthsAgo = now()->subMonths(6);
            $currentYear = now()->year;
            $threeYearsAgo = now()->subYears(3);
            $fiveYearsAgo = now()->subYears(5);
            $tenYearsAgo = now()->subYears(10);

            $this->buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgo->year);

            $rps = collect();
            if ($this->searchMode == 'complex') {
                $rps = $this->searchOutputRPS($queryRPS, $this->search, $this->searchBobotRPS, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $rps = $queryRPS->paginate($this->perPage);
            }

            // =========================
            // COUNT (ISOLATED QUERY)
            // =========================
            $countRPS = RPS::query();

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $countRPS->onlyTrashed();
            }

            return view('livewire.admin.prodi-management.capaian-management.rps-capaian-management', [
                'rps' => $rps,
                'cpls' => $this->cpl,
                'prodi' => $this->prodi,
            ]);
            // return view('livewire.admin.prodi-management.capaian-management.rps-capaian-management', array_merge($data, [
            //     'cpls' => $this->cpl,
            //     'prodi' => $this->prodi,
            // ]));

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.prodi-management.capaian-management.rps-capaian-management', [
                'rps' => RPS::whereRaw('1=0')->paginate($this->perPage),
            ], [
                'cpls' => $this->cpl,
                'prodis' => $this->prodi,
            ]);
        }
    }
}
