<?php

namespace App\Livewire\Global;

use Illuminate\Support\Facades\DB;

trait HasAkreditas
{
    protected function addRekapProdi(
        $query,
        ?int $prId = null,
        string $alias = 'rekap_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(
            DB::table($pivot)
                ->selectRaw('COALESCE(MAX(nilai), 0)')
                ->whereColumn("$pivot.$kolom", "$table.id")
                ->when(
                    $prId !== null,
                    fn ($q) => $q->where("$pivot.pr_id", $prId)
                ),
            $alias
        );

        return $query;
    }

    // protected function addIndexProdi(
    //     $query,
    //     ?int $prId = null,
    //     string $alias = 'index_cpl_pr',
    //     string $pivot = 'rekap_cpl_prodi',
    //     string $kolom = 'cpl_id',
    //     string $table = 'cpls',
    // ) {
    //     $query->addSelect("$table.*");
    //     $query->selectSub(function ($query) use ($prId, $pivot, $kolom, $table) {
    //         $query->from($pivot)
    //             ->whereColumn(
    //                 "$pivot.$kolom",
    //                 "$table.id"
    //             );

    //         if ($prId !== null) {
    //             $query->where(
    //                 "$pivot.pr_id",
    //                 $prId
    //             );
    //         }

    //         $query->selectRaw('
    //         CASE
    //             WHEN nilai >= 86 THEN 4.00
    //             WHEN nilai >= 80 THEN 3.70
    //             WHEN nilai >= 75 THEN 3.30
    //             WHEN nilai >= 70 THEN 3.00
    //             WHEN nilai >= 65 THEN 2.70
    //             WHEN nilai >= 60 THEN 2.30
    //             WHEN nilai >= 56 THEN 2.00
    //             WHEN nilai >= 40 THEN 1.00
    //             ELSE 0.00
    //         END
    //     ');

    //         $query->limit(1);

    //     }, $alias);

    //     return $query;
    // }

    protected function addIndexProdi(
        $query,
        ?int $prId = null,
        string $alias = 'index_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(function ($query) use ($prId, $pivot, $kolom, $table) {
            $query->from($pivot)
                ->whereColumn(
                    "$pivot.$kolom",
                    "$table.id"
                );

            if ($prId !== null) {
                $query->where(
                    "$pivot.pr_id",
                    $prId
                );
            }
            $query->selectRaw('ROUND((nilai / 100) * 4, 2)');

            $query->limit(1);

        }, $alias);

        return $query;
    }

    protected function addAkreditasProdi(
        $query,
        ?int $prId = null,
        string $alias = 'mutu_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(function ($query) use ($prId, $pivot, $kolom, $table) {

            $query->from($pivot)
                ->whereColumn(
                    "$pivot.$kolom",
                    "$table.id"
                );

            if ($prId !== null) {
                $query->where(
                    "$pivot.pr_id",
                    $prId
                );
            }

            $query->selectRaw("
            CASE
                WHEN nilai >= 86 THEN 'A'
                WHEN nilai >= 80 THEN 'A-'
                WHEN nilai >= 75 THEN 'B+'
                WHEN nilai >= 70 THEN 'B'
                WHEN nilai >= 65 THEN 'B-'
                WHEN nilai >= 60 THEN 'C+'
                WHEN nilai >= 56 THEN 'C'
                WHEN nilai >= 40 THEN 'D'
                ELSE 'E'
            END
        ");

            $query->limit(1);

        }, $alias);

        return $query;
    }

    protected function addRekapMahasiswa(
        $query,
        string $alias = 'rekap_mahasiswa'
    ) {
        $query->selectSub(
            DB::table('nilai_mahasiswa')
                ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                ->selectRaw('COALESCE(ROUND(AVG(nilai_mahasiswa.nilai), 2), 0)')
                ->whereColumn('mahasiswas.user_id', 'users.id'),
            $alias
        );

        return $query;
    }

    // protected function addIndexMahasiswa(
    //     $query,
    //     string $alias = 'index_mahasiswa'
    // ) {
    //     $query->selectSub(function ($sub) {

    //         $sub->from('nilai_mahasiswa')
    //             ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
    //             ->whereColumn('mahasiswas.user_id', 'users.id')
    //             ->selectRaw('
    //             CASE
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 86 THEN 4.00
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 80 THEN 3.70
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 75 THEN 3.30
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 70 THEN 3.00
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 65 THEN 2.70
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 60 THEN 2.30
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 56 THEN 2.00
    //                 WHEN AVG(nilai_mahasiswa.nilai) >= 40 THEN 1.00
    //                 ELSE 0.00
    //             END
    //         ');
    //     }, $alias);

    //     return $query;
    // }

    protected function addIndexMahasiswa(
        $query,
        string $alias = 'index_mahasiswa'
    ) {
        $query->selectSub(function ($sub) {

            $sub->from('nilai_mahasiswa')
                ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                ->whereColumn('mahasiswas.user_id', 'users.id')
                ->selectRaw('ROUND((AVG(nilai_mahasiswa.nilai) / 100) * 4, 2)');

        }, $alias);

        return $query;
    }

    protected function addMutuMahasiswa(
        $query,
        string $alias = 'mutu_mahasiswa'
    ) {
        $query->selectSub(function ($sub) {

            $sub->from('nilai_mahasiswa')
                ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                ->whereColumn('mahasiswas.user_id', 'users.id')
                ->selectRaw("
                CASE
                    WHEN AVG(nilai_mahasiswa.nilai) >= 86 THEN 'A'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 80 THEN 'A-'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 75 THEN 'B+'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 70 THEN 'B'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 65 THEN 'B-'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 60 THEN 'C+'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 56 THEN 'C'
                    WHEN AVG(nilai_mahasiswa.nilai) >= 40 THEN 'D'
                    ELSE 'E'
                END
            ");
        }, $alias);

        return $query;
    }

    protected function addCountRpsMahasiswa(
        $queryUser,
        string $alias = 'count_rps'
    ) {
        $queryUser->addSelect('users.*');

        $queryUser->selectSub(
            DB::table('nilai_mahasiswa')
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
                ->selectRaw('COUNT(DISTINCT nilai_mahasiswa.rps_id)'),
            $alias
        );

        return $queryUser;
    }

    protected function addTotalSksMahasiswa(
        $queryUser,
        string $alias = 'total_sks'
    ) {
        $queryUser->addSelect('users.*');

        $queryUser->selectSub(
            DB::table(function ($sub) {
                $sub->from('nilai_mahasiswa')
                    ->join('rps', 'nilai_mahasiswa.rps_id', '=', 'rps.id')
                    ->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                    ->selectRaw('
                    nilai_mahasiswa.mahasiswa_id,
                    nilai_mahasiswa.rps_id,
                    MAX(mata_kuliahs.sks_kuliah) as sks
                ')
                    ->groupBy(
                        'nilai_mahasiswa.mahasiswa_id',
                        'nilai_mahasiswa.rps_id'
                    );
            }, 'rps_unik')
                ->join(
                    'mahasiswas',
                    'rps_unik.mahasiswa_id',
                    '=',
                    'mahasiswas.id'
                )
                ->whereColumn(
                    'mahasiswas.user_id',
                    'users.id'
                )
                ->selectRaw('COALESCE(SUM(rps_unik.sks),0)'),
            $alias
        );

        return $queryUser;
    }
protected function addCountRpsDosen($queryUser, string $alias = 'count_rps')
{
    // Menggunakan DB::table untuk menghindari dependensi model
    $subQuery = \DB::table('rps_pivot_tim_dosen')
        ->join('tim_dosens', 'rps_pivot_tim_dosen.tim_dosen_id', '=', 'tim_dosens.id')
        ->join('tim_dosen_pivot_dosen', 'tim_dosens.id', '=', 'tim_dosen_pivot_dosen.tim_dosen_id')
        ->join('dosens', 'tim_dosen_pivot_dosen.dosen_id', '=', 'dosens.id')
        ->whereColumn('dosens.user_id', 'users.id') 
        ->selectRaw('COUNT(DISTINCT rps_pivot_tim_dosen.rps_id)');

    return $queryUser->selectSub($subQuery, $alias);
}

protected function addTotalSksDosen($queryUser, string $alias = 'total_sks')
{
    // Kita buat query yang lebih datar agar server tidak bingung
    $subQuery = \DB::table('rps_pivot_tim_dosen')
        ->join('tim_dosens', 'rps_pivot_tim_dosen.tim_dosen_id', '=', 'tim_dosens.id')
        ->join('tim_dosen_pivot_dosen', 'tim_dosens.id', '=', 'tim_dosen_pivot_dosen.tim_dosen_id')
        ->join('dosens', 'tim_dosen_pivot_dosen.dosen_id', '=', 'dosens.id')
        ->join('rps', 'rps_pivot_tim_dosen.rps_id', '=', 'rps.id')
        ->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
        ->whereColumn('dosens.user_id', 'users.id')
        // Menggunakan SUM(DISTINCT ...) untuk menghindari subquery bertingkat
        ->selectRaw('COALESCE(SUM(DISTINCT mata_kuliahs.sks_kuliah), 0)');

    return $queryUser->selectSub($subQuery, $alias);
}

    protected function addCountRpsTimDosen($queryTimDosen, string $alias = 'count_rps')
    {
        $queryTimDosen->select('tim_dosens.*');

        return $queryTimDosen->selectSub(function ($query) {
            $query->from('rps_pivot_tim_dosen')
                ->selectRaw('COUNT(DISTINCT rps_id)')
                ->whereColumn(
                    'rps_pivot_tim_dosen.tim_dosen_id',
                    'tim_dosens.id'
                );
        }, $alias);
    }
    protected function addTotalSksTimDosen($queryTimDosen, string $alias = 'total_sks')
    {
        return $queryTimDosen->selectSub(function ($query) {
            $query->from('rps_pivot_tim_dosen')
                ->join('rps', 'rps_pivot_tim_dosen.rps_id', '=', 'rps.id')
                ->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                ->selectRaw('COALESCE(SUM(DISTINCT mata_kuliahs.sks_kuliah), 0)')
                ->whereColumn('rps_pivot_tim_dosen.tim_dosen_id', 'tim_dosens.id');
        }, $alias);
    }
}
