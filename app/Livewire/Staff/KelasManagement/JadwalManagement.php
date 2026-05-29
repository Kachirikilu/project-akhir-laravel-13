<?php

namespace App\Livewire\Staff\KelasManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\WithJadwalModal;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalManagement extends Component
{
    use HasToast;
    use WithJadwalFilters;
    use WithJadwalModal;
    use WithKelasModal;
    use WithMahasiswaSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;

    public $search = '';

    public $kode;

    public Kelas $kelas;

    public $perPage = 6;

    public $sortField = 'label_kelas';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    public $switchTable = 'jadwal-card';

    protected $listeners = ['refresh-table' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 6],
        'sortField' => ['except' => 'label_kelas'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($kode, $switchTable = 'jadwal-card')
    {
        $this->kode = $kode;
        $this->kelas = Kelas::where('kode_kelas', $kode)
            ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
            ->firstOrFail();

        $this->switchTable = $switchTable;
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

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->resetPage();

        if ($table == 'jadwal-card' || $table == '' || $table == null) {
            $targetPath = "/kelas-management/kelas/{$this->kode}";
        } else {
            $targetPath = "/kelas-management/kelas/{$this->kode}/{$table}";
        }
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            $queryJadwal = $this->inputJadwalSearch($this->kelas->id);
            $countJadwal = KelasJadwal::where('kelas_id', $this->kelas->id);

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryJadwal->onlyTrashed();
                $countJadwal->onlyTrashed();
            }

            $jadwals = $queryJadwal->paginate($this->perPage);


            // totalJadwal

            if (Auth::user()->mahasiswa) {
                $userId = Auth::id();
                $jadwals->load('mahasiswas:id,user_id');
                $jadwals->getCollection()->transform(function ($jadwal) use ($userId) {
                    if ($jadwal->password == '' || $jadwal->password == null) {
                        $jadwal->with_pw = false;
                    } else {
                        $jadwal->with_pw = true;
                    }
                    $jadwal->password = '';
                    $jadwal->is_my_class = $jadwal->mahasiswas->contains('user_id', $userId);

                    return $jadwal;
                });
            }

            return view('livewire.staff.kelas-management.jadwal-management', [
                'jadwals' => $jadwals,
                'kelas' => $this->kelas,
                'totalJadwalKelas' => $countJadwal->count(),
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
