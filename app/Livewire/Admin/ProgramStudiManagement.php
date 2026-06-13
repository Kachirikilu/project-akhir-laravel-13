<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\ProdiManagement\WithDepartemenFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
use App\Livewire\Admin\ProdiManagement\WithProdiExcel;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiModal;
use App\Http\Services\RekapCapaian;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramStudiManagement extends Component
{
    use HasSortir;
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
    use WithProdiSearchFilters;
    use RekapCapaian;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = 'prodi';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

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
        $columns = [
            'prodi' => [1 => 'id', 2 => 'kode', 3 => 'program_studi', 4 => 'rekap_pr', 5 => 'index_pr', 6 => 'akreditas_pr', 7 => 'departemen', 8 => 'fakultas', 9 => 'strata', 10 => 'created_at', 11 => 'updated_at'],
            'departemen' => [1 => 'id', 2 => 'kode', 3 => 'departemen', 4 => 'rekap_dp', 5 => 'index_dp', 6 => 'akreditas_dp', 7 => 'fakultas', 8 => 'created_at', 9 => 'updated_at'],
            'fakultas' => [1 => 'id', 2 => 'kode', 3 => 'fakultas', 4 => 'rekap_fk', 5 => 'index_fk', 6 => 'akreditas_fk', 7 => 'created_at', 8 => 'updated_at'],
        ];
        $aliases = [
            'kode' => ['kode'],
            'program_studi' => ['departemen', 'fakultas'],

            'rekap_pr' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            'rekap_dp' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            'rekap_fk' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            'index_pr' => ['index_pr', 'index_dp', 'index_fk'],
            'index_dp' => ['index_pr', 'index_dp', 'index_fk'],
            'index_fk' => ['index_pr', 'index_dp', 'index_fk'],
            'akreditas_pr' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],
            'akreditas_dp' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],
            'akreditas_fk' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],

            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);

        if ($table === 'program_studi' && ($sortField === 'departemen' || $sortField === 'fakultas')) {
            $this->sortField = 'program_studi';
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
                $queryPr->onlyTrashed();
                $queryDp->onlyTrashed();
                $queryFk->onlyTrashed();
            }

            if ($this->switchTable === 'prodi') {
                $this->addRekapProdi($queryPr, 'rekap_pr');
                $this->addIndexProdi($queryPr, 'index_pr');
                $this->addAkreditasProdi($queryPr, 'akreditas_pr');
                $this->buttonStrataFilter($queryPr);
            } elseif ($this->switchTable === 'departemen') {
                $this->addRekapDepartemen($queryDp, 'rekap_dp');
                $this->addIndexDepartemen($queryDp, 'index_dp');
                $this->addAkreditasDepartemen($queryDp, 'akreditas_dp');
            } elseif ($this->switchTable === 'fakultas') {
                $this->addRekapFakultas($queryFk, 'rekap_fk');
                $this->addIndexFakultas($queryFk, 'index_fk');
                $this->addAkreditasFakultas($queryFk, 'akreditas_fk');
            }

            // =========================
            // PAGINATION
            // =========================
            if ($this->searchMode == 'full') {
                if ($this->switchTable === 'prodi') {
                    $prodis = $this->searchOutputPr($queryPr, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                } elseif ($this->switchTable === 'departemen') {
                    $departemens = $this->searchOutputPr($queryDp, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                } elseif ($this->switchTable === 'fakultas') {
                    $fakultas = $this->searchOutputPr($queryFk, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                }
            } else {
                if ($this->switchTable === 'prodi') {
                    $prodis = $queryPr->paginate($this->perPage);
                } elseif ($this->switchTable === 'departemen') {
                    $departemens = $queryDp->paginate($this->perPage);
                } elseif ($this->switchTable === 'fakultas') {
                    $fakultas = $queryFk->paginate($this->perPage);
                }
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
