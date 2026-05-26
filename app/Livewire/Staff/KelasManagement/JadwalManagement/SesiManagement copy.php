<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement\WithSesiFilters;
use App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement\WithSesiModal;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Models\Auth\User;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\Kelas\KelasSesi;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class SesiManagement extends Component
{
    use WithMahasiswaSearchFilters;
    use WithPagination;
    use WithRPSShow;
    use WithSesiFilters;
    use WithSesiModal;
    use WithUserFilters;

    public $search = '';

    public $kode;

    public $kode_jadwal;

    public $kelas;

    public $jadwal;

    public $id_jadwal;

    public $perPage = 8;

    public $sortField = 'pertemuan_ke';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    public $switchTable = 'sesi-card';

    protected $listeners = ['refresh-table' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'sortField' => ['except' => 'pertemuan_ke'],
        'switchTable' => ['except' => 'sesi-card'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($kode, $kode_jadwal, $id_jadwal)
    {
        $this->kode = $kode;
        $this->kelas = Kelas::where('kode_kelas', $kode)
            ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
            ->firstOrFail();

        $this->id_jadwal = $id_jadwal;
        $this->jadwal = KelasJadwal::where('id', $id_jadwal)->firstOrFail();
        $this->kode_jadwal = $this->jadwal->kode_jadwal;
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

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'sesi-card' => [1 => 'id', 2 => 'pertemuan_ke', 3 => 'hari_pelaksanaan', 4 => 'jam_pelaksanaan', 5 => 'jumlah_absensi', 6 => 'tanggal_pelaksanaan', 8 => 'created_at', 9 => 'updated_at'],
            'sesi-table' => [1 => 'id', 2 => 'pertemuan_ke', 3 => 'hari_pelaksanaan', 4 => 'jam_pelaksanaan', 5 => 'jumlah_absensi', 6 => 'tanggal_pelaksanaan', 8 => 'created_at', 9 => 'updated_at'],
            'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'name', 4 => 'email', 5 => 'mhs_masuk', 6 => 'identity1', 7 => 'nik', 8 => 'angkatan', 9 => 'status', 10 => 'prodi', 11 => 'created_at', 12 => 'updated_at'],
        ];
        $aliases = [
            'pertemuan_ke' => ['name'],
            'name' => ['pertemuan_ke'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $allFilters = ['filterSesi', 'filterMahasiswa'];

        foreach ($allFilters as $filter) {
            if ($filter !== 'filter'.strtoupper($this->switchTable)) {
                $this->$filter = '';
            }
        }

        $limits = [
            'sesi-card' => 16,
            'sesi-table' => 16,
            'mahasiswa' => 200,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }
    }

    public function render()
    {
        try {
            $idJadwal = $this->id_jadwal;

            $querySesi = $this->inputSesiSearch2($idJadwal);

            // $querySesi->selectSub(function ($query) {
            //     $query->selectRaw("COALESCE(
            //         (SELECT metode FROM kelas_sesi_overrides WHERE kelas_sesi_overrides.sesi_id = kelas_sesi.id LIMIT 1),
            //         (SELECT metode FROM scpmk_atrs WHERE scpmk_atrs.sesi_id = kelas_sesi.id LIMIT 1)
            //     )");
            // }, 'metode');

            $statuses = [
                'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir', 'Terlambat', 'Izin', 'Sakit', 'Dispensasi')",
                'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir', 'Terlambat')",
                'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",
                'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",
                'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",
                'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",
                'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",
                'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",
                'mhs_tidak_masuk' => "(mahasiswa_kehadiran.status IN ('Absen', 'Sakit', 'Izin', 'Dispensasi') OR mahasiswa_kehadiran.status IS NULL)",
                'mhs_poin_absensi' => "CASE 
                            WHEN mahasiswa_kehadiran.status IN ('Hadir', 'Dispensasi') THEN 2
                            WHEN mahasiswa_kehadiran.status IN ('Terlambat', 'Izin', 'Sakit') THEN 1
                            ELSE 0 
                        END",
            ];

            $queryUser = $this->inputUserSearch('mahasiswa', $idJadwal)->select('users.*');

            foreach ($statuses as $alias => $condition) {
                $queryUser->selectSub(function ($query) use ($idJadwal, $alias, $condition) {
                    if ($alias === 'mhs_poin_absensi') {
                        $rawSql = "COALESCE(SUM({$condition}), 0)";
                    } else {
                        $rawSql = "COALESCE(SUM(CASE WHEN {$condition} THEN 1 ELSE 0 END), 0)";
                    }

                    $query->selectRaw($rawSql)
                        ->from('mahasiswa_kehadiran')
                        ->join('kelas_sesi', 'mahasiswa_kehadiran.sesi_id', '=', 'kelas_sesi.id')
                        ->join('mahasiswas', 'mahasiswa_kehadiran.mahasiswa_id', '=', 'mahasiswas.id')
                        ->whereColumn('mahasiswas.user_id', 'users.id')
                        ->where('kelas_sesi.kj_id', $idJadwal);
                }, $alias);
            }

            $countSesi = KelasSesi::where('kj_id', $idJadwal);
            $countMahasiswa = User::whereHas('mahasiswa.jadwals', function ($q) use ($idJadwal) {
                $q->where('kj_id', $idJadwal);
            });

            if ($this->showDeleted) {
                $querySesi->onlyTrashed();
                $queryUser->onlyTrashed();

                $countSesi->onlyTrashed();
                $countMahasiswa->onlyTrashed();
            }

            $sesis = collect();
            $users = collect();

            switch ($this->switchTable) {
                case 'sesi-card':
                case 'sesi-table':
                    $accessorFields = ['metode', 'kode_scpmk', 'bobot', 'tugas', 'w_tugas', 'w_mandiri', 'deskripsi', 'materi'];
                    $sesis = $this->searchOutputSesi($querySesi, $accessorFields);
                    break;
                case 'mahasiswa':
                    $users = $queryUser->paginate($this->perPage);
                    break;
            }

            return view('livewire.staff.kelas-management.jadwal-management.sesi-management', [
                'sesis' => $sesis,
                'users' => $users,
                'kelas' => $this->kelas,
                'totalSesiKelas' => $countSesi->count() ?? 16,
                'totalMahasiswaKelas' => $countMahasiswa->count() ?? 500,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.kelas-management.jadwal-management.sesi-management', [
                'sesis' => KelasSesi::whereRaw('1 = 0')->paginate($this->perPage),
                'users' => User::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
                'kelas' => $this->kelas,
                'totalSesiKelas' => '-',
                'totalMahasiswaKelas' => '-',
            ]);
        }
    }
}
