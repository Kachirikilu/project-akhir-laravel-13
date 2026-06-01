<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalModal;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
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

    public $isJadwalMhs = false;

    public $kode;

    public Kelas $kelas;

    public $perPage = 8;

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

    // public function mount($isJadwalMhs = false, $kode = null, $switchTable = 'jadwal-card')
    // {
    //     if ($kode !== null || ! $isJadwalMhs) {
    //         $this->kode = $kode;
    //         $this->kelas = Kelas::where('kode_kelas', $kode)
    //             ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
    //             ->firstOrFail();
    //     } else {
    //         $this->isJadwalMhs = $isJadwalMhs;
    //     }
    //     $this->switchTable = $switchTable;
    // }

    public function mount($isJadwalMhs = false, $kode = null, $switchTable = 'jadwal-card')
    {
        $this->isJadwalMhs = $isJadwalMhs;
        $this->switchTable = $switchTable;

        if (! $this->isJadwalMhs && $kode !== null) {
            $this->kode = $kode;
            $this->kelas = Kelas::where('kode_kelas', $kode)
                ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
                ->firstOrFail();
        }
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

        $base = $this->isJadwalMhs ? 'jadwal-kelas' : 'kelas-management/kelas';
        $suffix = ($table && $table !== 'jadwal-card') ? "/{$table}" : '';
        
        $targetPath = "/{$base}/{$this->kode}{$suffix}";
        $targetPath = preg_replace('#(?<!:)/+#', '/', $targetPath);
        $targetPath = '/' . ltrim(rtrim($targetPath, '/'), '/');

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            if (! $this->isJadwalMhs) {
                $queryJadwal = $this->inputJadwalSearch($this->kelas->id);
                $countJadwal = KelasJadwal::where('kelas_id', $this->kelas->id);
            } else {
                $queryJadwal = $this->inputJadwalSearch();
                $countJadwal = KelasJadwal::query();
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryJadwal->onlyTrashed();
                $countJadwal->onlyTrashed();
            }

            // if ($this->switchTable == 'jadwal-table') {
                $jadwals = $queryJadwal->paginate($this->perPage);
            // } else {
            //     $jadwals = $queryJadwal->get();
            // }

            if (Auth::user()->mahasiswa) {
                $userId = Auth::id();
                $jadwals->load('mahasiswas:id,user_id');
                $jadwals->getCollection()->transform(function ($jadwal) use ($userId) {
                    if (! $this->isJadwalMhs) {
                        if ($jadwal->password == '' || $jadwal->password == null) {
                            $jadwal->with_pw = false;
                        } else {
                            $jadwal->with_pw = true;
                        }
                        $jadwal->is_my_class = $jadwal->mahasiswas->contains('user_id', $userId);
                    } else {
                        $jadwal->is_my_class = true;
                    }
                    $jadwal->password = '';

                    return $jadwal;
                });
            }

            return view('livewire.all-role.kelas-management.jadwal-management', [
                'jadwals' => $jadwals,
                'kelas' => $this->kelas ?? null,
                'totalJadwalKelas' => $countJadwal->count(),
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management.jadwal-management', [
                'jadwals' => KelasJadwal::whereRaw('1 = 0')->paginate($this->perPage),
                'kelas' => $this->kelas ?? null,
            ]);
        }
    }
}
