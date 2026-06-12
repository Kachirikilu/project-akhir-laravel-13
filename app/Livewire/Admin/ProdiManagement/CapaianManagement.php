<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Admin\ProdiManagement\CapaianManagement\WithRekapCPLProdi;
use App\Livewire\Staff\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\CPLManagement\WithCPLDelete;
use App\Livewire\Staff\CPLManagement\WithCPLModal;
// use App\Livewire\Staff\RPSManagement\WithRPSFilters;
// use App\Livewire\Staff\RPSManagement\WithRPSDelete;
use App\Livewire\Staff\RPSManagement\WithRPSModal;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Models\Akademik\CPL;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class CapaianManagement extends Component
{
    use HasSortir;
    use HasToast;

    use WithRekapCPLProdi;

    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLDelete;
    // use WithRPSFilters;
    // use WithRPSDelete;
    use WithRPSModal;
    use WithCPLSearchFilters;
    use WithRPSSearchFilters;

    use WithPagination;

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

    public $strata_pr_url;

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
    ];

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

    public function mount(
        $strata = null,
        $kode_pr = null,
    ) {
        $this->strata_pr_url = $strata;
        $this->kode_pr_url = $kode_pr;

        $strataDb = match (strtoupper($strata ?? '')) {
            'S1' => 'Sarjana',
            'S2' => 'Magister',
            'S3' => 'Doktor',
            default => $strata,
        };

        $this->prodi = Prodi::query()
            ->with(['dp_rel.fk_rel'])
            ->where(function ($query) use ($kode_pr) {
                $query->where('kode_pr', $kode_pr)

                    ->orWhereHas('dp_rel', function ($q) use ($kode_pr) {
                        $q->where('kode_dp', $kode_pr);
                    })

                    ->orWhereHas('dp_rel.fk_rel', function ($q) use ($kode_pr) {
                        $q->where('kode_fk', $kode_pr);
                    });
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
        $this->reset(['search', 'filterPr']);
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
        $queryCPL = $this->inputCPLSearch($this->pr_id_url);

        try {
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryCPL = $queryCPL->onlyTrashed();
            }

            $this->addCountRpsCpl($queryCPL, $this->pr_id_url, 'count_rps_pr');
            $this->addCountRpsCpl($queryCPL, null, 'count_rps');
            
            $this->addRekapCplProdi($queryCPL, $this->pr_id_url, 'rekap_cpl_pr');
            $this->addIndexCplProdi($queryCPL, $this->pr_id_url, 'index_cpl_pr');
            $this->addAkreditasCplProdi($queryCPL, $this->pr_id_url, 'akreditas_cpl_pr');

            $cpls = collect();
            if ($this->searchMode == 'full') {
                $cpls = $this->searchOutputCPL($queryCPL, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $cpls = $queryCPL->paginate($this->perPage);
            }

            $countCPL = CPL::query();
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $countCPL->onlyTrashed();
            }

            return view('livewire.admin.prodi-management.capaian-management', [
                'cpls' => $cpls,
                'prodi' => $this->prodi,
                'cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator,
            ]);

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
