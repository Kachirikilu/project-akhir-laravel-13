<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithUserFilters
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $searchAngkatan = '';

    public function updatingSearchAngkatan()
    {
        $this->resetPage();
    }

    public function resetInputAngkatan()
    {
        $this->reset('searchAngkatan');
        $this->resetPage();
    }

    public function inputUserSearch($role = null, $jadwal_id = null)
    {
        if (! $role) {
            $queryUser = User::query()->with([
                'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
                'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
                'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
            ]);
        } elseif ($role == 'admin') {
            $queryUser = User::query()->with(['admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel']);
        } elseif ($role == 'dosen') {
            $queryUser = User::query()->with([
                'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
            ]);
        } elseif ($role == 'mahasiswa') {
            $queryUser = User::query()->with(['mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel']);

            if ($jadwal_id) {
                $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($jadwal_id) {
                    $q->where('kj_id', $jadwal_id);
                });
            }
        }

        if ($this->filterStatus !== '') {
            if ($this->selectedPrId) {
                $queryUser->inLocationUser('prodi', $this->selectedPrId);
            }
            if ($this->selectedDpId) {
                $queryUser->inLocationUser('departemen', $this->selectedDpId);
            }
            if ($this->selectedFkId) {
                $queryUser->inLocationUser('fakultas', $this->selectedFkId);
            }
        }

        if (! empty($this->selectedRPSId) && $this->switchTable === 'dosen') {
            $queryUser->whereHas('dosen.rps', function ($q) {
                $q->where('rps.id', $this->selectedRPSId);
            });
        }

        if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
            $search = trim($this->search);
            if (! empty($search)) {
                if (! str_contains($search, '%')) {
                    $queryUser->where(function ($q) use ($search) {
                        $q->searchUser($search);
                    });
                }
            }
            if (! empty($this->searchAngkatan) && $this->switchTable == 'mahasiswa') {
                $queryUser->searchUser($this->searchAngkatan, true);
            }
            $this->sortFieldOrderUser($queryUser);
        }

        return $queryUser;
    }

    private function applySubqueriesToUsers($queryUser, $idJadwal, $expiredCount)
    {
        $statuses = [
            'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Izin','Sakit','Dispensasi')",
            'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')",
            'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",
            'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",
            'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",
            'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",
            'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",
            'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",
            'mhs_poin_absensi' => "CASE WHEN mahasiswa_kehadiran.status IN ('Hadir','Dispensasi') THEN 2 WHEN mahasiswa_kehadiran.status IN ('Terlambat','Izin','Sakit') THEN 1 ELSE 0 END",
        ];

        $queryUser->selectRaw("GREATEST(0, ? - ((SELECT COALESCE(SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Terlambat' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Dispensasi' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?))) as mhs_tidak_masuk", [$expiredCount, $idJadwal, $idJadwal, $idJadwal, $idJadwal, $idJadwal]);
    }

    public function buttonUserFilter($queryUser)
    {
        $queryUser->when(in_array($this->switchTable, ['admin', 'dosen', 'mahasiswa']), function ($q) {
            $q->whereHas($this->switchTable);
        });

        if ($this->switchTable === 'dosen') {
            if (! empty($this->filterDosen)) {
                if ($this->filterDosen == 'dosen-rps') {
                    $queryUser->whereHas('dosen.rps');
                } elseif ($this->filterDosen == 'dosen-non-rps') {
                    $queryUser->whereDoesntHave('dosen.rps');
                }
            }
        }

        // Filter by status
        if ($this->filterStatus === 'dosen-prodi') {
            $queryUser->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', Auth::user()->pr_id));
        } elseif ($this->filterStatus === 'dosen-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('dosen', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'dosen-non-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('dosen', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'user-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'user-non-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        } elseif ($this->filterStatus === '') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('dosen.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('mahasiswa.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id));
            });
        }

        return $queryUser;
    }

    public function filterByUser($role)
    {
        $this->switchTable = $role;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    protected function addMahasiswaNilaiAkhir(
        $queryUser,
        int $idJadwal,
        string $alias = 'mhs_nilai_akhir'
    ) {
        $queryUser->selectSub(function ($query) use ($idJadwal) {

            $query->from('nilai_mahasiswa')
                ->join(
                    'mahasiswas',
                    'nilai_mahasiswa.mahasiswa_id',
                    '=',
                    'mahasiswas.id'
                )
                ->whereColumn(
                    'mahasiswas.user_id',
                    'users.id'
                )
                ->where(
                    'nilai_mahasiswa.kj_id',
                    $idJadwal
                )
                ->selectRaw(
                    'COALESCE(nilai_mahasiswa.nilai, 0)'
                )
                ->limit(1);

        }, $alias);

        return $queryUser;
    }

    protected function addMahasiswaNilaiIndex(
        $queryUser,
        int $idJadwal,
        string $alias = 'mhs_nilai_index'
    ) {
        $queryUser->selectSub(function ($query) use ($idJadwal) {

            $query->from('nilai_mahasiswa')
                ->join(
                    'mahasiswas',
                    'nilai_mahasiswa.mahasiswa_id',
                    '=',
                    'mahasiswas.id'
                )
                ->whereColumn(
                    'mahasiswas.user_id',
                    'users.id'
                )
                ->where(
                    'nilai_mahasiswa.kj_id',
                    $idJadwal
                )
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

        }, $alias);

        return $queryUser;
    }

    protected function addMahasiswaNilaiHuruf(
        $queryUser,
        int $idJadwal,
        string $alias = 'mhs_nilai_huruf'
    ) {
        $queryUser->selectSub(function ($query) use ($idJadwal) {

            $query->from('nilai_mahasiswa')
                ->join(
                    'mahasiswas',
                    'nilai_mahasiswa.mahasiswa_id',
                    '=',
                    'mahasiswas.id'
                )
                ->whereColumn(
                    'mahasiswas.user_id',
                    'users.id'
                )
                ->where(
                    'nilai_mahasiswa.kj_id',
                    $idJadwal
                )
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

        }, $alias);

        return $queryUser;
    }

    protected function addMahasiswaAttendanceStats(
        $queryUser,
        int $idJadwal
    ) {
        $statuses = [
            'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Izin','Sakit','Dispensasi')",

            'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')",

            'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",

            'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",

            'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",

            'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",

            'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",

            'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",

            'mhs_poin_absensi' => "
            CASE
                WHEN mahasiswa_kehadiran.status IN ('Hadir','Dispensasi') THEN 2
                WHEN mahasiswa_kehadiran.status IN ('Terlambat','Izin','Sakit') THEN 1
                ELSE 0
            END
        ",
        ];

        foreach ($statuses as $alias => $condition) {

            $queryUser->selectSub(function ($query) use (
                $idJadwal,
                $alias,
                $condition
            ) {

                $rawSql = $alias === 'mhs_poin_absensi'
                    ? "COALESCE(SUM($condition),0)"
                    : "COALESCE(SUM(CASE WHEN $condition THEN 1 ELSE 0 END),0)";

                $query->selectRaw($rawSql)
                    ->from('mahasiswa_kehadiran')
                    ->join(
                        'kelas_sesi',
                        'mahasiswa_kehadiran.sesi_id',
                        '=',
                        'kelas_sesi.id'
                    )
                    ->join(
                        'mahasiswas',
                        'mahasiswa_kehadiran.mahasiswa_id',
                        '=',
                        'mahasiswas.id'
                    )
                    ->whereColumn(
                        'mahasiswas.user_id',
                        'users.id'
                    )
                    ->where(
                        'kelas_sesi.kj_id',
                        $idJadwal
                    );

            }, $alias);
        }

        return $queryUser;
    }

    protected function addMahasiswaTidakMasuk(
        $queryUser,
        int $idJadwal,
        int $expiredCount,
        string $alias = 'mhs_tidak_masuk'
    ) {
        $queryUser->selectRaw("
        GREATEST(
            0,
            ? - (
                SELECT COUNT(*)
                FROM mahasiswa_kehadiran
                JOIN kelas_sesi
                    ON mahasiswa_kehadiran.sesi_id = kelas_sesi.id
                JOIN mahasiswas
                    ON mahasiswa_kehadiran.mahasiswa_id = mahasiswas.id
                WHERE mahasiswas.user_id = users.id
                AND kelas_sesi.kj_id = ?
                AND mahasiswa_kehadiran.status IN (
                    'Hadir',
                    'Terlambat',
                    'Dispensasi'
                )
            )
        ) AS {$alias}
    ", [
            $expiredCount,
            $idJadwal,
        ]);

        return $queryUser;
    }

    protected function addCountRpsDosen($queryUser, string $alias = 'count_rps')
    {
        return $queryUser->selectSub(function ($query) {

            $query->from('rps_pivot_dosen')
                ->join(
                    'dosens',
                    'rps_pivot_dosen.dosen_id',
                    '=',
                    'dosens.id'
                )
                ->whereColumn(
                    'dosens.user_id',
                    'users.id'
                )
                ->selectRaw('COUNT(DISTINCT rps_pivot_dosen.rps_id)');

        }, $alias);
    }

    protected function addTotalSKs($queryUser, string $alias = 'total_sks')
    {
        return $queryUser->selectSub(function ($query) {

            $query->fromSub(function ($sub) {

                $sub->from('rps_pivot_dosen')
                    ->join('dosens', 'rps_pivot_dosen.dosen_id', '=', 'dosens.id')
                    ->join('rps', 'rps_pivot_dosen.rps_id', '=', 'rps.id')
                    ->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                    ->whereColumn('dosens.user_id', 'users.id')
                    ->selectRaw('DISTINCT rps.id, mata_kuliahs.sks_kuliah');

            }, 'rps_sks')
                ->selectRaw('COALESCE(SUM(sks_kuliah), 0)');

        }, $alias);
    }

    public function sortFieldOrderUser($queryUser)
    {
        $profileFields = [
            'role', 'admin_id', 'dosen_id', 'mahasiswa_id',
            'name', 'identity1', 'identity2', 'identity3', 'nik', 'kampus',
            'count_rps', 'total_sks',
            'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_huruf',
            'program_studi', 'status', 'angkatan', 'kode', 'pertemuan_ke',
            'nip', 'nitk', 'nidn', 'nidk', 'nim',
        ];

        if (in_array($this->sortField, $profileFields)) {
            return $this->applyUserCombinedSort($queryUser);
        }

        $field = ($this->sortField === 'id') ? 'users.id' : $this->sortField;

        return $queryUser->orderBy($field, $this->sortDirection);
    }

    private function applyUserCombinedSort($queryUser)
    {
        $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
            ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
            ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
            ->select('users.*');

        if ($this->sortField === 'program_studi') {
            return $this->applyProdiSort(
                $queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
                    ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
                    ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
                'COALESCE(ap.strata, dp.strata, mp.strata)',
                'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)'
            );
        }

        $aliasSort = match ($this->sortField) {
            'mhs_nilai_akhir',
            'mhs_nilai_index',
            'mhs_nilai_huruf' => 'mhs_nilai_akhir',

            'count_rps',
            'total_sks' => $this->sortField,

            default => null,
        };

        if ($aliasSort) {
            return $queryUser->orderBy(
                $aliasSort,
                $this->sortDirection
            );
        }

        $orderByRaw = match ($this->sortField) {
            'admin_id' => 'admins.id',
            'dosen_id' => 'dosens.id',
            'mahasiswa_id' => 'mahasiswas.id',

            'role' => 'CASE
                        WHEN admins.id IS NOT NULL THEN 1
                        WHEN dosens.id IS NOT NULL THEN 2
                        WHEN mahasiswas.id IS NOT NULL THEN 3
                        ELSE 4
                    END',

            'name' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
            'kode' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',

            'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
            'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
            'identity3' => 'dosens.nidk',

            'nip' => 'COALESCE(admins.nip, dosens.nip)',
            'nitk' => 'admins.nitk',
            'nidn' => 'dosens.nidn',
            'nidk' => 'dosens.nidk',
            'nim' => 'mahasiswas.nim',

            'pertemuan_ke' => 'mahasiswas.nim',

            'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',

            'kampus' => 'COALESCE(
                            admins.kode_wilayah,
                            mahasiswas.kode_wilayah
                        )',

            'status' => 'COALESCE(
                            admins.status,
                            dosens.status,
                            mahasiswas.status
                        )',

            'angkatan' => 'mahasiswas.angkatan',

            'created_at' => 'users.created_at',
            'updated_at' => 'users.updated_at',

            default => 'users.id',
        };

        return $queryUser->orderByRaw(
            "$orderByRaw {$this->sortDirection}"
        );
    }
    // private function applyUserCombinedSort($queryUser)
    // {
    //     $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
    //         ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
    //         ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
    //         ->select('users.*');

    //     if ($this->sortField === 'program_studi') {
    //         return $this->applyProdiSort($queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
    //             ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
    //             ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
    //             'COALESCE(ap.strata, dp.strata, mp.strata)',
    //             'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)');
    //     }

    //     $orderByRaw = match ($this->sortField) {
    //         'admin_id' => 'admins.id',
    //         'dosen_id' => 'dosens.id',
    //         'mahasiswa_id' => 'mahasiswas.id',
    //         'role' => 'CASE
    //                         WHEN admins.id IS NOT NULL THEN 1
    //                         WHEN dosens.id IS NOT NULL THEN 2
    //                         WHEN mahasiswas.id IS NOT NULL THEN 3
    //                         ELSE 4
    //                     END',
    //         'name' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
    //         'kode' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
    //         'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
    //         'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
    //         'identity3' => 'dosens.nidk',
    //         'nip' => 'COALESCE(admins.nip, dosens.nip)',
    //         'nitk' => 'admins.nitk',
    //         'nidn' => 'dosens.nidn',
    //         'nidk' => 'dosens.nidk',
    //         'nim' => 'mahasiswas.nim',
    //         'count_rps' => '(SELECT COUNT(DISTINCT rpd.rps_id)
    //                         FROM rps_pivot_dosen rpd
    //                         WHERE rpd.dosen_id = dosens.id)',
    //         'total_sks' => '(SELECT COALESCE(SUM(mk.sks_kuliah), 0)
    //             FROM rps_pivot_dosen rpd
    //             JOIN rps r ON r.id = rpd.rps_id
    //             JOIN mata_kuliahs mk ON mk.id = r.mk_id
    //             WHERE rpd.dosen_id = dosens.id)',
    //         'pertemuan_ke' => 'mahasiswas.nim',
    //         'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',
    //         'kampus' => 'COALESCE(admins.kode_wilayah, mahasiswas.kode_wilayah)',
    //         'status' => 'COALESCE(admins.status, dosens.status, mahasiswas.status)',
    //         'angkatan' => 'mahasiswas.angkatan',
    //         'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_huruf' => 'mahasiswas.mhs_nilai_akhir',
    //         'created_at' => 'users.created_at',
    //         'updated_at' => 'users.updated_at',
    //         default => 'users.id'
    //     };

    //     return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    // }
}
