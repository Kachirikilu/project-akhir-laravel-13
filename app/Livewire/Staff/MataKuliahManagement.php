<?php

namespace App\Livewire\Staff;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Staff\MKManagement\WithMKDelete;
use App\Livewire\Staff\MKManagement\WithMKExcel;
use App\Livewire\Staff\MKManagement\WithMKFilters;
use App\Livewire\Staff\MKManagement\WithMKModal;
use App\Models\Akademik\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MataKuliahManagement extends Component
{
    use HasToast;
    use WithMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithMKDelete;
    use WithMKExcel;
    use WithMKFilters;
    use WithMKModal;
    use WithPagination;
    use WithProdiSearchFilters;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';
    
    public $search = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshMKList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterMK' => ['except' => ''],
        'filterMKGG' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
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
        $this->reset(['search', 'filterMK']);
        $this->resetPage();
    }

    public function refreshMKList()
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
        $map = [
            'tatap_muka' => 'sks_tm',
            'praktikum' => 'sks_pr',
            'praktek_lapangan' => 'sks_pl',
            'simulasi' => 'sks_sm',
        ];

        if (isset($map[$table]) && str_starts_with($sortField, 'sks_')) {
            $this->sortField = $map[$table];
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = '/mata-kuliah-management'.($table ? '/'.$table : '');
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    
    public function render()
    {
        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();

        try {
            $queryMK = $this->inputMKSearch();
            $countMK = MataKuliah::query();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryMK->onlyTrashed();
                $countMK->onlyTrashed();
            }

            // =========================
            // STATS GLOBAL (FULL DATA)
            // =========================
            $totalMK = (clone $countMK)->count();
            $totalTatapMuka = (clone $countMK)->where('tipe_sks', 1)->count();
            $totalPraktikum = (clone $countMK)->where('tipe_sks', 2)->count();
            $totalPraktek = (clone $countMK)->where('tipe_sks', 3)->count();
            $totalSimulasi = (clone $countMK)->where('tipe_sks', 4)->count();

            // =========================
            // STATS PER TAB
            // =========================
            $tabQuery = clone $countMK;
            $this->buttonMKSwitch($tabQuery);

            $totalMKProdi = (clone $tabQuery)->whereHas('prodis', function ($q) {
                $q->where('prodis.id', Auth::user()->pr_id);
            })->count();
            $totalMKOpsi = (clone $tabQuery)->count();

            $totalWajib = (clone $tabQuery)->where('is_wajib', true)->count();
            $totalPilihan = (clone $tabQuery)->where('is_wajib', false)->count();
            $totalUni = (clone $tabQuery)->where('level_mk', 4)->count();

            // =========================
            // QUERY FINAL TABLE
            // =========================
            $this->buttonMKSwitch($queryMK);
            $this->buttonMKFilter($queryMK);

            $mk = $this->searchOutputMK($queryMK, $this->search, $this->perPage, $this->sortField, $this->sortDirection);

            return view('livewire.staff.mk-management', [
                'mks' => $mk,

                'totalGanjilGenap' => $this->totalGanjil + $this->totalGenap,
                'totalGanjil' => $this->totalGanjil,
                'totalGenap' => $this->totalGenap,

                'totalMK' => $totalMK,
                'totalTatapMuka' => $totalTatapMuka,
                'totalPraktikum' => $totalPraktikum,
                'totalPraktek' => $totalPraktek,
                'totalSimulasi' => $totalSimulasi,

                'totalMKProdi' => $totalMKProdi,
                'totalMKOpsi' => $totalMKOpsi,
                'totalWajib' => $totalWajib,
                'totalPilihan' => $totalPilihan,
                'totalUni' => $totalUni,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.mk-management', [
                'mks' => MataKuliah::whereRaw('1 = 0')->paginate($this->perPage),

                'totalGanjilGanjil' => '-',
                'totalGanjil' => '-',
                'totalGenap' => '-',

                'totalMK' => '-',
                'totalGanjil' => '-',
                'totalGenap' => '-',
                'totalTatapMuka' => '-',
                'totalPraktikum' => '-',
                'totalPraktek' => '-',
                'totalSimulasi' => '-',

                'totalMKProdi' => '-',
                'totalMKOpsi' => '-',
                'totalWajib' => '-',
                'totalPilihan' => '-',
                'totalUni' => '-',
            ]);
        }
    }
}
