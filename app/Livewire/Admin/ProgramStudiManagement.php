<?php

namespace App\Livewire\Admin;

use App\Http\Services\RekapCapaian;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Admin\ProdiManagement\WithDepartemenFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\ProdiManagement\WithProdiDelete;
use App\Livewire\Admin\ProdiManagement\WithProdiExcel;
use App\Livewire\Admin\ProdiManagement\WithProdiFilters;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\HasStats;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ProgramStudiManagement extends Component
{
    use HasSortir;
    use HasToast;
    use HasStats;
    use RekapCapaian;
    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithDepartemenFilters;
    use WithFakultasFilters;
    use WithPagination;
    use WithProdiDelete;
    use WithProdiExcel;
    use WithProdiFilters;

    public $perPage = 8;

    public $switchTable = '';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public $selectedDpId;

    public $selectedFkId;

    protected $listeners = [
        'refresh-table' => 'refreshProdisList',
        'refresh-data-prodi' => 'refreshDataProdisList',
        'refresh-stats-prodi' => 'refreshStatsProdisList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterPr' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
    }

    #[On('switch-table-updated')]
    public function updateSwitchTable($switchTable)
    {
        $this->switchTable = $switchTable;
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

    #[On('refresh-data-pr')]
    #[On('refresh-table')]
    public function refreshProdisList()
    {
        $this->resetPage();
    }

    #[On('refresh-stats-pr')]
    public function refreshStatsProdisList()
    {
        $this->clearProdiStatsCache();
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
            '' => [1 => 'id', 2 => 'kode', 3 => 'program_studi', 4 => 'rekap_pr', 5 => 'index_pr', 6 => 'count_mk', 6 => 'count_rps_aktif', 6 => 'count_rps_draf', 7 => 'akreditas_pr', 8 => 'departemen', 9 => 'fakultas', 10 => 'strata', 11 => 'created_at', 12 => 'updated_at'],
            'departemen' => [1 => 'id', 2 => 'kode', 3 => 'departemen', 4 => 'rekap_dp', 5 => 'index_dp', 6 => 'akreditas_dp', 7 => 'fakultas', 8 => 'created_at', 9 => 'updated_at'],
            'fakultas' => [1 => 'id', 2 => 'kode', 3 => 'fakultas', 4 => 'rekap_fk', 5 => 'index_fk', 6 => 'akreditas_fk', 7 => 'created_at', 8 => 'updated_at'],
        ];
        $aliases = [
            'kode' => ['kode'],
            'program_studi' => ['departemen', 'fakultas'],

            'rekap_pr' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            // 'rekap_dp' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            // 'rekap_fk' => ['rekap_pr', 'rekap_dp', 'rekap_fk'],
            'index_pr' => ['index_pr', 'index_dp', 'index_fk'],
            // 'index_dp' => ['index_pr', 'index_dp', 'index_fk'],
            // 'index_fk' => ['index_pr', 'index_dp', 'index_fk'],
            'akreditas_pr' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],
            // 'akreditas_dp' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],
            // 'akreditas_fk' => ['akreditas_pr', 'akreditas_dp', 'akreditas_fk'],

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
            '' => 75,
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

    // public function placeholder()
    // {
    //     return view('livewire.global.livewire-skeletons.table-placeholder');
    // }

    public function render()
    {
        try {
            // $this->inputDpFilter();
            // $this->inputFkFilter();

            $queryPr = collect();
            $queryDp = collect();
            $queryFk = collect();

            if ($this->switchTable === 'fakultas') {
                $queryFk = $this->inputFkSearch();
            } elseif ($this->switchTable === 'departemen') {
                $queryDp = $this->inputDpSearch();
            } else {
                $queryPr = $this->inputPrSearch();
            }

            $prodis = collect();
            $departemens = collect();
            $fakultas = collect();

            // =========================
            // SOFT DELETE
            // =========================
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                if ($this->switchTable === 'fakultas') {
                    $queryFk->onlyTrashed();

                } elseif ($this->switchTable === 'departemen') {
                    $queryDp->onlyTrashed();

                } else {
                    $queryPr->onlyTrashed();

                }
            }
            $stats = $this->getStatsProdi($this->showDeleted);


            if ($this->switchTable === 'fakultas') {
                $this->addRekapFakultasFk($queryFk, 'rekap_fk');
                $this->addIndexFakultasFk($queryFk, 'index_fk');
                $this->addAkreditasFakultasFk($queryFk, 'akreditas_fk');
            } elseif ($this->switchTable === 'departemen') {
                $this->addRekapDepartemenDp($queryDp, 'rekap_dp');
                $this->addIndexDepartemenDp($queryDp, 'index_dp');
                $this->addAkreditasDepartemenDp($queryDp, 'akreditas_dp');
            } else {
                $this->addRekapProdiPr($queryPr, 'rekap_pr');
                $this->addIndexProdiPr($queryPr, 'index_pr');
                $this->addAkreditasProdiPr($queryPr, 'akreditas_pr');
                $this->addMataKuliahProdiPr($queryPr, 'count_mk', 'count_rps', 'count_rps_aktif', 'count_rps_draf');
                $this->buttonStrataFilter($queryPr);
            }

            // =========================
            // PAGINATION
            // =========================
            if ($this->searchMode == 'full') {
                if ($this->switchTable === 'fakultas') {
                    $fakultas = $this->searchOutputPr($queryFk, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                } elseif ($this->switchTable === 'departemen') {
                    $departemens = $this->searchOutputPr($queryDp, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                } else {
                    $prodis = $this->searchOutputPr($queryPr, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                }
            } else {
                if ($this->switchTable === 'fakultas') {
                    $fakultas = $queryFk->paginate($this->perPage);
                } elseif ($this->switchTable === 'departemen') {
                    $departemens = $queryDp->paginate($this->perPage);
                } else {
                    $prodis = $queryPr->paginate($this->perPage);
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

                'stats' => $stats,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.prodi-management', [
                'prodis' => Prodi::whereRaw('1=0')->paginate($this->perPage),
                'departemens' => Departemen::whereRaw('1=0')->paginate($this->perPage),
                'fakultas' => Fakultas::whereRaw('1=0')->paginate($this->perPage),

                'stats' => [
                    'prodi' => '-',
                    'sarjana' => '-',
                    'magister' => '-',
                    'doktor' => '-',
                    'departemen' => '-',
                    'fakultas' => '-',
                ],
            ]);
        }
    }
}
