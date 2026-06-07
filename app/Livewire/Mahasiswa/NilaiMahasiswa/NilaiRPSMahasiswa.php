<?php

namespace App\Livewire\Mahasiswa\NilaiMahasiswa;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithNilaiSearchFilters;
use App\Livewire\Mahasiswa\NilaiMahasiswa\NilaiRPSMahasiswa\WithNilaiRPSFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
// use App\Models\Kelas\NilaiMahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiRPSMahasiswa extends Component
{
    use HasToast;

    // use WithKelasDelete;
    use WithNilaiRPSFilters;
    use WithNilaiFilters;
    use WithNilaiSearchFilters;
    use WithPagination;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'id';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    public $mahasiswa_id;

    public $ganjil_genap;

    public $akademik;

    protected $listeners = ['refresh-table' => 'refreshKelassList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterNilai' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($switchTable = '', $ganjil_genap = null, $akademik = null)
    {
        $this->switchTable = $switchTable;
        $this->ganjil_genap = $ganjil_genap;
        $this->akademik = $akademik;

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

    public function render()
    {
        try {
            $queryNilai = $this->inputNilaiRPSSearch($this->mahasiswa_id);

            if (property_exists($this, 'showDeleted') && $this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();
            }

            if (! empty(trim($this->search))) {
                $searchTerm = '%'.trim($this->search).'%';
                $queryNilai->where(function ($q) use ($searchTerm) {
                    $q->whereHas('rps_rel', function ($sub) use ($searchTerm) {
                        $sub->where('nama_mata_kuliah', 'like', $searchTerm)
                            ->orWhere('kode_mata_kuliah', 'like', $searchTerm);
                    })
                        ->orWhere('nilai', 'like', $searchTerm);
                });
            }

            $queryNilai->orderBy($this->sortField, $this->sortDirection);

            $perPage = $this->perPage ?? 10;
            $paginatedRps = $queryNilai->paginate($perPage);

            return view('livewire.mahasiswa.nilai-mahasiswa.nilai-rps-mahasiswa', [
                'detailNilaiRps' => $paginatedRps,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.mahasiswa.nilai-mahasiswa.nilai-rps-mahasiswa', [
                'detailNilaiRps' => new LengthAwarePaginator([], 0, $this->perPage ?? 10),
            ]);
        }
    }
}
