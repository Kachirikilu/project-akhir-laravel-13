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
        // 'switchTable' => ['except' => 'sesi-card'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount($kode, $kode_jadwal, $id_jadwal, $switchTable = 'sesi-card')
    {
        $this->kode = $kode;
        $this->kelas = Kelas::where('kode_kelas', $kode)
            ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
            ->firstOrFail();

        $this->id_jadwal = $id_jadwal;
        $this->jadwal = KelasJadwal::where('id', $id_jadwal)->firstOrFail();
        $this->kode_jadwal = $this->jadwal->kode_jadwal;

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

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'sesi-card' => [1 => 'pertemuan_ke', 2 => 'jumlah_absensi', 3 => 'tanggal_pelaksanaan', 4 => 'metode', 5 => 'kode_scpmk', 6 => 'bobot'],
            'sesi-table' => [1 => 'id', 2 => 'metode', 3 => 'pertemuan_ke', 4 => 'hari_pelaksanaan', 5 => 'jam_pelaksanaan', 6 => 'jumlah_absensi', 7 => 'tanggal_pelaksanaan', 8 => 'kode_scpmk', 9 => 'bobot', 10 => 'tugas', 11 => 'w_tugas', 12 => 'w_mandiri'],
            'mahasiswa' => [1 => 'id', 2 => 'nim', 3 => 'name', 4 => 'mhs_poin_absensi', 5 => 'mhs_masuk', 6 => 'mhs_tidak_masuk', 7 => 'angkatan', 8 => 'status', 9 => 'prodi'],
        ];
        $aliases = [
            'pertemuan_ke' => ['pertemuan_ke', 'nim', 'name'],
            'nim' => ['pertemuan_ke'],
            'jumlah_absensi' => ['jumlah_absensi', 'mhs_poin_absensi', 'mhs_masuk', 'mhs_tidak_masuk'],
            'mhs_poin_absensi' => ['jumlah_absensi'],
            'name' => ['pertemuan_ke'],
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


        $targetUrl = route('kelas-management', ['switchTable' => $table]);
        if ($table == 'sesi-card' || $table == '' || $table == null) {
            $targetPath = "/kelas-management/kelas/{$this->kode}/jadwal/{$this->kode_jadwal}/{$this->id_jadwal}";
        } else {
            $targetPath = "/kelas-management/kelas/{$this->kode}/jadwal/{$this->kode_jadwal}/{$this->id_jadwal}/{$table}";
        }

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            $idJadwal = $this->id_jadwal;
            $querySesi = $this->inputSesiSearch($idJadwal);

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

            // =========================================================================
            // [AWALAN TEMPAT MODIFIKASI FITUR SEARCH ABSENSI & PERSEN MAHASISWA]
            // =========================================================================
            $search = trim($this->search);
            if (! empty($search)) {
                $totalSesiUntukPersen = (clone $countSesi)->count() ?: 1;
                $cleanNumber = preg_replace('/[^0-9.]/', '', $search);

                if (is_numeric($cleanNumber) && $cleanNumber !== '') {
                    if (str_contains($search, '%')) {
                        $queryUser->havingRaw('(mhs_poin_absensi / (2 * ?)) * 100 LIKE ?', [$totalSesiUntukPersen, "%{$cleanNumber}%"]);
                    } else {
                        $queryUser->having('mhs_absensi', '=', $cleanNumber)
                            ->orHaving('mhs_masuk', '=', $cleanNumber)
                            ->orHaving('mhs_hadir', '=', $cleanNumber)
                            ->orHaving('mhs_terlambat', '=', $cleanNumber)
                            ->orHaving('mhs_izin', '=', $cleanNumber)
                            ->orHaving('mhs_sakit', '=', $cleanNumber)
                            ->orHaving('mhs_dispensasi', '=', $cleanNumber)
                            ->orHaving('mhs_absen', '=', $cleanNumber)
                            ->orHaving('mhs_tidak_masuk', '=', $cleanNumber)
                            ->orHaving('mhs_poin_absensi', '=', $cleanNumber);
                    }
                }
            }
            // =========================================================================
            // [AKHIRAN TEMPAT MODIFIKASI FITUR SEARCH]
            // =========================================================================

            $sesis = collect();
            $users = collect();

            switch ($this->switchTable) {
                case 'sesi-card':
                case 'sesi-table':
                    $sesis = $this->searchOutputSesi($querySesi, $idJadwal);
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
