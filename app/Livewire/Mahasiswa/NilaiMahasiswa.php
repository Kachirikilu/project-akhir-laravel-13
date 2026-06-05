<?php

namespace App\Livewire\Mahasiswa;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasModal;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;


// use App\Models\Kelas\NilaiMahasiswa;
use App\Livewire\Mahasiswa\NilaiMahasiswa\WithNilaiFilters;





use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiMahasiswa extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;

    use WithJadwalFilters;
    // use WithKelasDelete;
    use WithKelasFilters;
    use WithKelasModal;
    use WithMKSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;

    use WithNilaiFilters;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'id';

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
        'sortField' => ['except' => 'id'],
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
        if (Auth::user()->mahasiswa) {
            $this->mahasiswa_id = Auth::user()->mahasiswa->id;
        }

        // $this->inputPrFilter();
        // $this->inputDpFilter();
        // $this->inputFkFilter();
        // $this->inputRPSFilter();
        // $this->inputMKFilter();
        // $this->inputDosenFilter();

        try {
            // =========================
            // QUERY UTAMA (TABLE)
            // =========================
            $queryNilai = $this->inputNilaiSearch();

            // =========================
            // QUERY COUNT (TERPISAH 🔥)
            // =========================
            // $countNilai = Nilai::query();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();
                // $countNilai->onlyTrashed();
            }


            return view('livewire.mahasiswa.nilai-mahasiswa', [
                'nilai' => $queryNilai->paginate($this->perPage),
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.mahasiswa.nilai-mahasiswa', [
              
            ]);
        }
    }
}
