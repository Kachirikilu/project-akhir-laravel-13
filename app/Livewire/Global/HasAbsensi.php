<?php

namespace App\Livewire\Global;

trait HasAbsensi
{
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

    protected function addMahasiswaNilaiMutu(
        $queryUser,
        int $idJadwal,
        string $alias = 'mhs_nilai_mutu'
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
}
