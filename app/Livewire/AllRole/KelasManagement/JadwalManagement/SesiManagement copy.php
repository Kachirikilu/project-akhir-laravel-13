<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiAbsenModal;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithSesiFilters;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithSesiModal;
use App\Livewire\Global\WithKelasSesiSearchFilters;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Models\Auth\User;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\Kelas\KelasSesi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SesiManagement extends Component
{
    use WithNilaiAbsenModal;
    use WithJadwalModal;
    use WithKelasSesiSearchFilters;
    use WithMahasiswaSearchFilters;
    use WithNilaiExcel;
    use WithPagination;
    use WithRPSShow;
    use WithSesiFilters;
    use WithSesiModal;
    use WithUserFilters;
    use WithUserSearchFilters;

    public $search = '';

    public $isJadwalMhs = false;

    public $kode;

    public $kode_jadwal;

    public $kelas;

    public $jadwal;

    public $jadwal_id;

    public $rps_id;

    public $kode_rps;

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
        'showDeleted' =>  ['except' => false],
    ];

    // public function mount($kode, $kode_jadwal, $jadwal_id, $switchTable = 'sesi-card')
    // {
    //     $this->kode = $kode;
    //     $this->kelas = Kelas::where('kode_kelas', $kode)
    //         ->orWhereRaw("REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')", [$kode])
    //         ->firstOrFail();

    //     $this->jadwal_id = $jadwal_id;
    //     $this->jadwal = KelasJadwal::where('id', $jadwal_id)->firstOrFail();
    //     $this->kode_jadwal = $this->jadwal->kode_jadwal;

    //     $this->switchTable = $switchTable;
    // }

    public function mount(
        $isJadwalMhs = false,
        $kode = null,
        $kode_jadwal = null,
        $switchTable = 'sesi-card'
    ) {
        $this->kode = $kode;
        $this->isJadwalMhs = $isJadwalMhs;

        $this->kelas = Kelas::query()
            ->where('kode_kelas', $kode)
            ->orWhereRaw(
                "REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')",
                [$kode]
            )
            ->firstOrFail();

        $this->kode_jadwal = $kode_jadwal;

        $parts = explode('-', $kode_jadwal);

        if (count($parts) < 3) {
            abort(404, 'Format Kode Jadwal Kelas tidak valid!');
        }

        $labelKelas = $parts[0];
        $kodeWilayah = $parts[1];

        $tahunBlok = $parts[2];

        $this->jadwal = KelasJadwal::query()
            ->where('kelas_id', $this->kelas->id)
            ->where('label_kelas', $labelKelas)
            ->where('kode_wilayah', $kodeWilayah)
            ->whereRaw(
                '
            CASE
                WHEN YEAR(tanggal_mulai) >= 3000
                    THEN YEAR(tanggal_mulai)

                WHEN YEAR(tanggal_mulai) >= 2100
                    THEN RIGHT(YEAR(tanggal_mulai), 3)

                WHEN YEAR(tanggal_mulai) >= 2000
                    THEN RIGHT(YEAR(tanggal_mulai), 2)

                ELSE YEAR(tanggal_mulai)
            END = ?
            ',
                [$tahunBlok]
            )
            ->firstOrFail();

        $this->jadwal_id = $this->jadwal->id;
        $this->rps_id = $this->jadwal->rps_id;
        $this->kode_rps = $this->jadwal->kode_rps;
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
        if (Auth::user()->admin || Auth::user()->dosen) {
            $mahasiswa = [1 => 'mahasiswa_id', 2 => 'pertemuan_ke', 3 => 'name', 4 => 'mhs_poin_absensi', 5 => 'mhs_masuk', 6 => 'mhs_dispensasi', 7 => 'mhs_terlambat', 8 => 'mhs_izin', 9 => 'mhs_sakit', 10 => 'mhs_tidak_masuk', 11 => 'angkatan', 12 => 'status', 13 => 'prodi'];
        } else {
            $mahasiswa = [1 => 'mahasiswa_id', 2 => 'pertemuan_ke', 3 => 'name', 4 => 'angkatan', 5 => 'status', 6 => 'prodi'];
        }
        $columns = [
            'sesi-card' => [1 => 'pertemuan_ke', 2 => 'total_absensi', 3 => 'tanggal_pelaksanaan', 4 => 'metode', 5 => 'kode_scpmk', 6 => 'bobot'],
            'sesi-table' => [1 => 'id', 2 => 'metode', 3 => 'pertemuan_ke', 4 => 'hari_pelaksanaan', 5 => 'jam_pelaksanaan', 6 => 'total_absensi', 7 => 'tanggal_pelaksanaan', 8 => 'kode_scpmk', 9 => 'bobot', 10 => 'tugas', 11 => 'w_tugas', 12 => 'w_mandiri'],
            'mahasiswa' => $mahasiswa,
        ];
        $aliases = [
            'id' => ['id', 'mahasiswa_id'],
            'mahasiswa_id' => ['mahasiswa_id', 'id'],
            'pertemuan_ke' => ['pertemuan_ke', 'name'],
            'total_absensi' => ['total_absensi', 'mhs_poin_absensi', 'mhs_masuk', 'mhs_dispensasi', 'mhs_terlambat', 'mhs_hadir', 'mhs_sakit', 'mhs_tidak_masuk'],
            'mhs_poin_absensi' => ['total_absensi'],
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

        $currentTable = $table ?? $this->table ?? 'sesi-table';

        if ($this->switchTable == 'mahasiswa') {
            if ($this->perPage == 2) {
                $this->perPage = 3;
            } elseif ($this->perPage == 4) {
                $this->perPage = 5;
            } elseif ($this->perPage == 16) {
                $this->perPage = 15;
            }
        } elseif ($this->switchTable == 'sesi-card' || $this->switchTable == 'sesi-table') {
            if ($this->perPage == 3) {
                $this->perPage = 2;
            } elseif ($this->perPage == 5) {
                $this->perPage = 4;
            } elseif ($this->perPage == 10) {
                $this->perPage = 8;
            } elseif ($this->perPage >= 15) {
                $this->perPage = 16;
            }
        }

        $base = $this->isJadwalMhs ? 'jadwal-kelas' : 'kelas-management/kelas';
        $suffix = ($table && $table !== 'sesi-card') ? "/{$table}" : '';

        $targetPath = "/{$base}/{$this->kode}/jadwal/{$this->kode_jadwal}{$suffix}";

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            $idJadwal = $this->jadwal_id;
            $querySesi = $this->inputSesiSearch($idJadwal);

            if (Auth::user()->mahasiswa) {
                $mahasiswaId = Auth::user()->mahasiswa->id;
                $isInJadwal = KelasJadwal::where('id', $idJadwal)
                    ->whereHas('mahasiswas', function ($q) use ($mahasiswaId) {
                        $q->where('mahasiswas.id', $mahasiswaId);
                    })
                    ->exists();

                if (! $isInJadwal) {
                    $message = 'Anda tidak terdaftar di Kelas ini!';
                    $this->toast(text: $message, variant: 'danger');

                    $history = session('jadwal.history', []);
                    $compositeKey = $this->kode.'_'.$this->kode_jadwal;
                    unset($history[$compositeKey]);
                    session(['jadwal.history' => $history]);
                    $this->redirect(route('jadwal-management', $this->kode));
                }
            }

            /**
             * =========================
             * AMBIL SESI + CEK EXPIRED
             * =========================
             */
            if (Auth::user()->mahasiswa) {
                $mahasiswaId = Auth::user()->mahasiswa->id ?? null;

                $sesiList = KelasSesi::with([
                    'jadwal_rel',
                    'override',
                    'kehadirans' => function ($query) use ($mahasiswaId) {
                        $query->where('mahasiswa_id', $mahasiswaId);
                    },
                ])
                    ->where('kj_id', $idJadwal)
                    ->get();
            } else {
                $sesiList = KelasSesi::with(['jadwal_rel', 'override'])
                    ->where('kj_id', $idJadwal)
                    ->get();

            }

            $now = now();

            $expiredSesiIds = $sesiList->filter(function ($sesi) {
                $jamAkhir = $sesi->jam_berakhir;
                if (! $jamAkhir) {
                    return false;
                }

                return Carbon::parse($sesi->tanggal.' '.$jamAkhir)
                    ->lt(now());
            })->pluck('id')->all();

            $queryUser = $this->inputUserSearch('mahasiswa', $idJadwal)->select('users.*');

            $expiredCount = (int) count($expiredSesiIds ?: []);

            $statuses = [
                'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Izin','Sakit','Dispensasi')",
                'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')",
                'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",
                'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",
                'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",
                'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",
                'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",
                'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",
                'mhs_poin_absensi' => "CASE 
                        WHEN mahasiswa_kehadiran.status IN ('Hadir','Dispensasi') THEN 2
                        WHEN mahasiswa_kehadiran.status IN ('Terlambat','Izin','Sakit') THEN 1
                        ELSE 0
                    END",
            ];

            if (Auth::user()->admin || Auth::user()->dosen) {
                $queryUser->selectSub(function ($query) use ($idJadwal) {
                    $query->from('nilai_mahasiswa')->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                        ->whereColumn('mahasiswas.user_id', 'users.id')
                        ->where('nilai_mahasiswa.kj_id', $idJadwal)
                        ->where('nilai_mahasiswa.rps_id', $this->rps_id)
                        ->where('nilai_mahasiswa.ganjil_genap', (string) $this->jadwal->ganjil_genap)
                        ->where('nilai_mahasiswa.tahun_akademik', (string) $this->jadwal->tahun_akademik)
                        ->selectRaw('COALESCE(nilai_mahasiswa.nilai, 0)')
                        ->limit(1);

                }, 'mhs_nilai_akhir');

                $queryUser->selectSub(function ($query) use ($idJadwal) {

                    $query->from('nilai_mahasiswa')
                        ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                        ->whereColumn('mahasiswas.user_id', 'users.id')
                        ->where('nilai_mahasiswa.kj_id', $idJadwal)
                        ->where('nilai_mahasiswa.rps_id', $this->rps_id)
                        ->where('nilai_mahasiswa.ganjil_genap', (string) $this->jadwal->ganjil_genap)
                        ->where('nilai_mahasiswa.tahun_akademik', (string) $this->jadwal->tahun_akademik)
                        ->selectRaw('
                            CASE
                                WHEN nilai_mahasiswa.nilai >= 86 THEN 4.00
                                WHEN nilai_mahasiswa.nilai >= 80 THEN 3.70
                                WHEN nilai_mahasiswa.nilai >= 75 THEN 3.30
                                WHEN nilai_mahasiswa.nilai >= 70 THEN 3.00
                                WHEN nilai_mahasiswa.nilai >= 65 THEN 2.70
                                WHEN nilai_mahasiswa.nilai >= 60 THEN 2.30
                                WHEN nilai_mahasiswa.nilai >= 56 THEN 2.00
                                WHEN nilai_mahasiswa.nilai >= 40 THEN 1.00
                                ELSE 0
                            END
                        ')
                        ->limit(1);

                }, 'mhs_nilai_index');

                $queryUser->selectSub(function ($query) use ($idJadwal) {

                    $query->from('nilai_mahasiswa')
                        ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                        ->whereColumn('mahasiswas.user_id', 'users.id')
                        ->where('nilai_mahasiswa.kj_id', $idJadwal)
                        ->where('nilai_mahasiswa.rps_id', $this->rps_id)
                        ->where('nilai_mahasiswa.ganjil_genap', (string) $this->jadwal->ganjil_genap)
                        ->where('nilai_mahasiswa.tahun_akademik', (string) $this->jadwal->tahun_akademik)
                        ->selectRaw("
                            CASE
                                WHEN nilai_mahasiswa.nilai >= 86 THEN 'A'
                                WHEN nilai_mahasiswa.nilai >= 80 THEN 'A-'
                                WHEN nilai_mahasiswa.nilai >= 75 THEN 'B+'
                                WHEN nilai_mahasiswa.nilai >= 70 THEN 'B'
                                WHEN nilai_mahasiswa.nilai >= 65 THEN 'B-'
                                WHEN nilai_mahasiswa.nilai >= 60 THEN 'C+'
                                WHEN nilai_mahasiswa.nilai >= 56 THEN 'C'
                                WHEN nilai_mahasiswa.nilai >= 40 THEN 'D'
                                ELSE 'E'
                            END
                        ")
                        ->limit(1);
                }, 'mhs_nilai_mutu');

                foreach ($statuses as $alias => $condition) {
                    $queryUser->selectSub(function ($query) use ($idJadwal, $alias, $condition) {
                        if ($alias === 'mhs_poin_absensi') {
                            $rawSql = "COALESCE(SUM($condition), 0)";
                        } else {
                            $rawSql = "COALESCE(SUM(CASE WHEN $condition THEN 1 ELSE 0 END), 0)";
                        }

                        $query->selectRaw($rawSql)
                            ->from('mahasiswa_kehadiran')
                            ->join('kelas_sesi', 'mahasiswa_kehadiran.sesi_id', '=', 'kelas_sesi.id')
                            ->join('mahasiswas', 'mahasiswa_kehadiran.mahasiswa_id', '=', 'mahasiswas.id')
                            ->whereColumn('mahasiswas.user_id', 'users.id')
                            ->where('kelas_sesi.kj_id', $idJadwal);
                    }, $alias);
                }

                // $queryUser->selectRaw("
                //     GREATEST(0, ? - (
                //         (SELECT COALESCE(SUM(CASE WHEN mahasiswa_kehadiran.status = 'Hadir' THEN 1 ELSE 0 END), 0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id WHERE mahasiswas.user_id = users.id AND kelas_sesi.kj_id = ?) +
                //         (SELECT COALESCE(SUM(CASE WHEN mahasiswa_kehadiran.status = 'Terlambat' THEN 1 ELSE 0 END), 0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id WHERE mahasiswas.user_id = users.id AND kelas_sesi.kj_id = ?) +
                //         (SELECT COALESCE(SUM(CASE WHEN mahasiswa_kehadiran.status = 'Izin' THEN 1 ELSE 0 END), 0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id WHERE mahasiswas.user_id = users.id AND kelas_sesi.kj_id = ?) +
                //         (SELECT COALESCE(SUM(CASE WHEN mahasiswa_kehadiran.status = 'Sakit' THEN 1 ELSE 0 END), 0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id WHERE mahasiswas.user_id = users.id AND kelas_sesi.kj_id = ?) +
                //         (SELECT COALESCE(SUM(CASE WHEN mahasiswa_kehadiran.status = 'Dispensasi' THEN 1 ELSE 0 END), 0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id WHERE mahasiswas.user_id = users.id AND kelas_sesi.kj_id = ?)
                //     )) as mhs_tidak_masuk
                // ", [$expiredCount, $idJadwal, $idJadwal, $idJadwal, $idJadwal, $idJadwal]);
                $queryUser->selectRaw("
                        GREATEST(0, ? - (
                            SELECT COUNT(*)
                            FROM mahasiswa_kehadiran
                            JOIN kelas_sesi ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id
                            JOIN mahasiswas ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id
                            WHERE mahasiswas.user_id = users.id
                            AND kelas_sesi.kj_id = ?
                            AND mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')
                        )) as mhs_tidak_masuk
                    ", [
                    $expiredCount,
                    $idJadwal,
                ]);
            }

            /**
             * =========================
             * COUNTING
             * =========================
             */
            $countSesi = KelasSesi::where('kj_id', $idJadwal);

            $countMahasiswa = User::whereHas('mahasiswa.jadwals', function ($q) use ($idJadwal) {
                $q->where('kj_id', $idJadwal);
            });

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $querySesi->onlyTrashed();
                $queryUser->onlyTrashed();
                $countSesi->onlyTrashed();
                $countMahasiswa->onlyTrashed();
            }

            /**
             * =========================
             * SEARCH FILTER
             * =========================
             */
            // $search = trim($this->search);

            // if (! empty($search)) {

            //     $totalSesiUntukPersen = (clone $countSesi)->count() ?: 1;
            //     $cleanNumber = preg_replace('/[^0-9.]/', '', $search);

            //     if (is_numeric($cleanNumber) && $cleanNumber !== '') {

            //         if (str_contains($search, '%')) {
            //             $queryUser->havingRaw(
            //                 '(mhs_poin_absensi / (2 * ?)) * 100 LIKE ?',
            //                 [$totalSesiUntukPersen, "%{$cleanNumber}%"]
            //             );
            //         } else {
            //             $queryUser->having('mhs_absensi', '=', $cleanNumber)
            //                 ->orHaving('mhs_masuk', '=', $cleanNumber)
            //                 ->orHaving('mhs_hadir', '=', $cleanNumber)
            //                 ->orHaving('mhs_terlambat', '=', $cleanNumber)
            //                 ->orHaving('mhs_izin', '=', $cleanNumber)
            //                 ->orHaving('mhs_sakit', '=', $cleanNumber)
            //                 ->orHaving('mhs_dispensasi', '=', $cleanNumber)
            //                 ->orHaving('mhs_absen', '=', $cleanNumber)
            //                 ->orHaving('mhs_tidak_masuk', '=', $cleanNumber)
            //                 ->orHaving('mhs_poin_absensi', '=', $cleanNumber);
            //         }
            //     }
            // }

            /**
             * =========================
             * DEFAULT DATA
             * =========================
             */
            $sesis = collect();
            $users = collect();

            $absensi = [
                'mhs_poin_absensi' => 0,
                'mhs_poin_absensi_percent' => 0,
                'mhs_absensi' => 0,
                'mhs_masuk' => 0,
                'mhs_hadir' => 0,
                'mhs_terlambat' => 0,
                'mhs_izin' => 0,
                'mhs_sakit' => 0,
                'mhs_dispensasi' => 0,
                'mhs_absen' => 0,
                'mhs_tidak_masuk' => 0,
            ];

            /**
             * =========================
             * OUTPUT SWITCH
             * =========================
             */
            switch ($this->switchTable) {
                case 'sesi-card':
                    $sesis = $querySesi->get();
                    break;
                case 'sesi-table':
                    // $sesis = $this->searchOutputSesi($querySesi, $idJadwal);
                    $sesis = $this->searchOutputSesi($querySesi, $this->search, $this->perPage, $this->sortField, $this->sortDirection, $idJadwal);
                    break;
                case 'mahasiswa':
                    // $users = $queryUser->paginate($this->perPage);
                    // $users = $this->searchOutputUser($queryUser, $idJadwal);
                    $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, $idJadwal);
                    break;
            }

            /**
             * =========================
             * SUMMARY
             * =========================
             */
            $totalSesiKelas = $countSesi->count() ?: 0;

            $summaryQuery = User::query()
                ->whereHas('mahasiswa.jadwals', function ($q) use ($idJadwal) {
                    $q->where('kj_id', $idJadwal);
                })
                ->select('users.*');

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $summaryQuery->onlyTrashed();
            }

            if (Auth::user()->admin || Auth::user()->dosen || Auth::user()->mahasiswa) {
                foreach ($statuses as $alias => $condition) {

                    $summaryQuery->selectSub(function ($query) use ($idJadwal, $alias, $condition) {

                        if ($alias === 'mhs_poin_absensi') {
                            $rawSql = "COALESCE(SUM($condition),0)";
                        } else {
                            $rawSql = "COALESCE(SUM(CASE WHEN $condition THEN 1 ELSE 0 END),0)";
                        }

                        $query->selectRaw($rawSql)
                            ->from('mahasiswa_kehadiran')
                            ->join('kelas_sesi', 'mahasiswa_kehadiran.sesi_id', '=', 'kelas_sesi.id')
                            ->join('mahasiswas', 'mahasiswa_kehadiran.mahasiswa_id', '=', 'mahasiswas.id')
                            ->whereColumn('mahasiswas.user_id', 'users.id')
                            ->where('kelas_sesi.kj_id', $idJadwal);

                    }, $alias);
                }
            }

            /**
             * =========================
             * FINAL CALCULATION
             * =========================
             */
            if (Auth::user()->admin || Auth::user()->dosen) {

                $summary = $summaryQuery->get();

                $expiredCountX = count($expiredSesiIds);

                foreach ($summary as $row) {
                    $hadir = $row->mhs_hadir + $row->mhs_terlambat + $row->mhs_dispensasi;
                    $row->mhs_tidak_masuk = max(0, $expiredCountX - $hadir);
                }

                $absensi['mhs_poin_absensi'] = $summary->sum('mhs_poin_absensi');

                $maxPoint = $countMahasiswa->count() * $totalSesiKelas * 2;

                $absensi['mhs_poin_absensi_percent'] = $maxPoint > 0
                    ? round(($absensi['mhs_poin_absensi'] / $maxPoint) * 100, 2)
                    : 0;

                $absensi['mhs_masuk'] = $summary->sum('mhs_masuk');
                $absensi['mhs_hadir'] = $summary->sum('mhs_hadir');
                $absensi['mhs_terlambat'] = $summary->sum('mhs_terlambat');
                $absensi['mhs_izin'] = $summary->sum('mhs_izin');
                $absensi['mhs_sakit'] = $summary->sum('mhs_sakit');
                $absensi['mhs_dispensasi'] = $summary->sum('mhs_dispensasi');
                $absensi['mhs_absen'] = $summary->sum('mhs_absen');
                $absensi['mhs_tidak_masuk'] = $summary->sum('mhs_tidak_masuk');

            } elseif (Auth::user()->mahasiswa) {

                $myUser = $summaryQuery->where('users.id', Auth::id())->first();

                $expiredCountX = count($expiredSesiIds);
                $hadir = (
                    ($myUser?->mhs_hadir ?? 0) +
                    ($myUser?->mhs_terlambat ?? 0) +
                    ($myUser?->mhs_izin ?? 0) +
                    ($myUser?->mhs_sakit ?? 0) +
                    ($myUser?->mhs_dispensasi ?? 0)
                );

                $tidakMasuk = max(0, $expiredCountX - $hadir);

                $absensi['mahasiswa'] = [
                    'mhs_poin_absensi' => $myUser?->mhs_poin_absensi ?? 0,

                    'mhs_poin_absensi_percent' => ($totalSesiKelas * 2) > 0
                        ? round(
                            (($myUser?->mhs_poin_absensi ?? 0) / ($totalSesiKelas * 2)) * 100,
                            2
                        )
                        : 0,

                    'mhs_absensi' => $myUser?->mhs_absensi ?? 0,
                    'mhs_masuk' => $myUser?->mhs_masuk ?? 0,
                    'mhs_hadir' => $myUser?->mhs_hadir ?? 0,
                    'mhs_terlambat' => $myUser?->mhs_terlambat ?? 0,
                    'mhs_izin' => $myUser?->mhs_izin ?? 0,
                    'mhs_sakit' => $myUser?->mhs_sakit ?? 0,
                    'mhs_dispensasi' => $myUser?->mhs_dispensasi ?? 0,
                    'mhs_absen' => $myUser?->mhs_absen ?? 0,
                    'mhs_tidak_masuk' => $tidakMasuk ?? 0,
                ];
            }

            return view('livewire.all-role.kelas-management.jadwal-management.sesi-management', [
                'sesis' => $sesis,
                'users' => $users,
                'absensi' => $absensi,
                'kelas' => $this->kelas,
                'totalSesiKelas' => $totalSesiKelas,
                'totalMahasiswaKelas' => $countMahasiswa->count(),
            ]);

        } catch (\Throwable $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management.jadwal-management.sesi-management', [
                'sesis' => KelasSesi::whereRaw('1 = 0')->paginate($this->perPage),
                'users' => User::whereRaw('1 = 0')->paginate($this->perPage),
                'absensi' => [
                    'mhs_poin_absensi' => '-',
                    'mhs_poin_absensi_percent' => '-',
                    'mhs_absensi' => '-',
                    'mhs_masuk' => '-',
                    'mhs_hadir' => '-',
                    'mhs_terlambat' => '-',
                    'mhs_izin' => '-',
                    'mhs_sakit' => '-',
                    'mhs_dispensasi' => '-',
                    'mhs_absen' => '-',
                    'mhs_tidak_masuk' => '-',
                ],
                'kelas' => $this->kelas,
                'totalSesiKelas' => '-',
                'totalMahasiswaKelas' => '-',
            ]);
        }
    }
}
