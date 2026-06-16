<?php

namespace App\Livewire\Mahasiswa;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithNilaiSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Mahasiswa\NilaiMahasiswa\WithNilaiFilters;
// use App\Models\Kelas\NilaiMahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiMahasiswa extends Component
{
    use HasToast;
    // use WithKelasDelete;
    use WithNilaiFilters;

    use WithNilaiSearchFilters;
    use WithPagination;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'semester';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public $mahasiswa_id;

    protected $listeners = ['refresh-table' => 'refreshKelassList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterNilai' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'semester'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;

        if (! $this->AuthCheck('mahasiswa')) {
            return;
        } else {
            $this->mahasiswa_id = Auth::user()->mahasiswa->id;
        }
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
        $this->reset(['search', 'filterNilai']);
        $this->resetPage();
    }

    public function refreshNilaiList()
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

        $targetUrl = route('nilai-mahasiswa', ['switchTable' => $table]);
        if ($table == '' || $table == null) {
            $targetPath = '/nilai-mahasiswa';
        } else {
            $targetPath = '/nilai-mahasiswa/'.$table;
        }

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    // public function render()
    // {
    //     try {
    //         $queryNilai = $this->inputNilaiSearch($this->mahasiswa_id);

    //         if ($this->showDeleted && $this->AuthCheck('staff')) {
    //             $queryNilai->onlyTrashed();
    //         }

    //         return view('livewire.mahasiswa.nilai-mahasiswa', [
    //             'nilai' => $queryNilai->paginate($this->perPage),
    //         ]);

    //     } catch (QueryException $e) {
    //         $message = 'Terjadi kesalahan database: '.$e->getMessage();
    //         session()->flash('error', $message);
    //         $this->toast(text: $message, variant: 'danger');

    //         return view('livewire.mahasiswa.nilai-mahasiswa', [

    //         ]);
    //     }
    // }

    // public function render()
    // {
    //     try {
    //         $mahasiswa = Auth::user()->mahasiswa ?? null;
    //         $angkatan = $mahasiswa?->angkatan ? (int) $mahasiswa->angkatan : null;

    //         $queryNilai = $this->inputNilaiSemesterSearch($this->mahasiswa_id);

    //         if ($this->showDeleted && $this->AuthCheck('staff')) {
    //             $queryNilai->onlyTrashed();
    //         }

    //         // $queryNilaiSearch = $this->searchOutputNilai($queryNilai, $this->search, $this->perPage, $this->sortField, $this->sortDirection);

    //         $allNilaiRaw = $queryNilai->get();
    //         $calculatedPeriode = $this->indexIPK($allNilaiRaw, $angkatan);

    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;
    //         $perPage = $this->perPage ?? 8;
    //         $currentItems = $calculatedPeriode->slice(($currentPage - 1) * $perPage, $perPage)->values();

    //         $paginatedPeriode = new LengthAwarePaginator(
    //             $currentItems,
    //             $calculatedPeriode->count(),
    //             $perPage,
    //             $currentPage,
    //             [
    //                 'path' => Paginator::resolveCurrentPath(),
    //                 'pageName' => method_exists($this, 'paginatorPageName') ? $this->paginatorPageName() : 'page',
    //             ]
    //         );

    //         return view('livewire.mahasiswa.nilai-mahasiswa', [
    //             'periodes' => $paginatedPeriode,
    //         ]);

    //     } catch (QueryException $e) {
    //         $message = 'Terjadi kesalahan database: '.$e->getMessage();
    //         session()->flash('error', $message);
    //         if (method_exists($this, 'toast')) {
    //             $this->toast(text: $message, variant: 'danger');
    //         }

    //         return view('livewire.mahasiswa.nilai-mahasiswa', [
    //             'periodes' => new LengthAwarePaginator([], 0, $this->perPage ?? 10),
    //         ]);
    //     }
    // }

    public function render()
    {
        try {
            $mahasiswa = Auth::user()->mahasiswa ?? null;
            $angkatan = $mahasiswa?->angkatan ? (int) $mahasiswa->angkatan : null;

            $queryNilai = $this->inputNilaiSemesterSearch($this->mahasiswa_id);

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();
            }

            $allNilaiRaw = $queryNilai->get();
            $calculatedPeriode = $this->indexIPK($allNilaiRaw, $angkatan);

            $perPage = $this->perPage ?? 8;
            $paginatedPeriode = $this->searchOutputNilai(
                $calculatedPeriode,
                $this->search,
                $perPage,
                $this->sortField,
                $this->sortDirection
            );

            return view('livewire.mahasiswa.nilai-mahasiswa', [
                'periodes' => $paginatedPeriode,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.mahasiswa.nilai-mahasiswa', [
                'periodes' => new LengthAwarePaginator([], 0, $this->perPage ?? 8),
            ]);
        }
    }
}
