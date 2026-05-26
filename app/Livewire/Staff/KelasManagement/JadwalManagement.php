<?php

namespace App\Livewire\Staff\KelasManagement;

use App\Livewire\Staff\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\WithJadwalModal;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalManagement extends Component
{
    use WithJadwalFilters;
    use WithJadwalModal;
    use WithRPSShow;
    use WithMahasiswaSearchFilters;
    use HasToast;

    use WithPagination;

    public $search = '';

    public $kode;

    public Kelas $kelas;

    public $perPage = 6;

    public $sortField = 'label_kelas';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 6],
        'sortField' => ['except' => 'label_kelas'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($kode)
    {
        $this->kode = $kode;
        $this->kelas = Kelas::where('kode_kelas', $kode)
            ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
            ->firstOrFail();
    }

    public function loadingTable() {}

    public function updatingSearch()
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
    }

    public function render()
    {
        try {
            $queryJadwal = $this->inputJadwalSearch($this->kelas->id);
            if ($this->showDeleted) {
                $queryJadwal->onlyTrashed();
            }

            return view('livewire.staff.kelas-management.jadwal-management', [
                'jadwals' => $queryJadwal->paginate($this->perPage),
                'kelas' => $this->kelas,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.kelas-management.jadwal-management', [
                'jadwals' => KelasJadwal::whereRaw('1 = 0')->paginate($this->perPage),
                'kelas' => $this->kelas,
            ]);
        }
    }
}
