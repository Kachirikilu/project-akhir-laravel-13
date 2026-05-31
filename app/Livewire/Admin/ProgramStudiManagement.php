<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithDepartemenFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
use App\Livewire\Admin\ProdiManagement\WithProdiExcel;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiModal;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramStudiManagement extends Component
{
    use HasToast;
    use WithDepartemenFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithPagination;
    use WithProdiDelete;
    use WithProdiExcel;
    use WithProdiFilters;
    use WithProdiModal;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = 'prodi';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterPr' => ['except' => ''],
        // 'switchTable' => ['except' => 'prodi'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
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

        $targetPath = '/program-studi-management' . (in_array($table, ['prodi', '', null], true) ? '' : '/' . $table);
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
        $this->inputDpFilter();
        $this->inputFkFilter();

        $queryPr = $this->inputPrSearch();
        $queryDp = $this->inputDpSearch();
        $queryFk = $this->inputFkSearch();

        try {

            $prodis = collect();
            $departemens = collect();
            $fakultas = collect();

            // =========================
            // SOFT DELETE
            // =========================
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryPr = $queryPr->onlyTrashed();
                $queryDp = $queryDp->onlyTrashed();
                $queryFk = $queryFk->onlyTrashed();
            }

            // =========================
            // PAGINATION
            // =========================
            if ($this->switchTable === 'prodi') {
                $this->buttonStrataFilter($queryPr);
                $prodis = $queryPr->paginate($this->perPage);
            } elseif ($this->switchTable === 'departemen') {
                $departemens = $queryDp->paginate($this->perPage);
            } elseif ($this->switchTable === 'fakultas') {
                $fakultas = $queryFk->paginate($this->perPage);
            }

            // =========================
            // COUNT (ISOLATED QUERY)
            // =========================
            $countPr = Prodi::query();
            $countDp = Departemen::query();
            $countFk = Fakultas::query();

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $countPr->onlyTrashed();
                $countDp->onlyTrashed();
                $countFk->onlyTrashed();
            }

            return view('livewire.admin.prodi-management', [
                'prodis' => $prodis,
                'departemens' => $departemens,
                'fakultas' => $fakultas,

                // 🔥 FIX DI SINI
                'totalProdis' => $countPr->count(),
                'totalSarjanas' => (clone $countPr)->where('strata', 'Sarjana')->count(),
                'totalMagisters' => (clone $countPr)->where('strata', 'Magister')->count(),
                'totalDoktors' => (clone $countPr)->where('strata', 'Doktor')->count(),

                'totalDepartemen' => (clone $countDp)->count(),
                'totalFakultas' => (clone $countFk)->count(),
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.prodi-management', [
                'prodis' => Prodi::whereRaw('1=0')->paginate($this->perPage),
                'departemens' => Departemen::whereRaw('1=0')->paginate($this->perPage),
                'fakultas' => Fakultas::whereRaw('1=0')->paginate($this->perPage),

                'totalProdis' => '-',
                'totalSarjanas' => '-',
                'totalMagisters' => '-',
                'totalDoktors' => '-',
                'totalDepartemen' => '-',
                'totalFakultas' => '-',
            ]);
        }
    }
}
