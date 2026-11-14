<?php

namespace App\Livewire\Global;

use Illuminate\Support\Facades\DB;

trait HasAkreditas
{
    protected function addRekapOBE(
        $query,
        ?int $id = null,
        string $alias = 'rekap_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
        string $idPivot = 'pr_id'
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(
            DB::table($pivot)
                ->selectRaw('COALESCE(MAX(nilai), 0)')
                ->whereColumn("$pivot.$kolom", "$table.id")
                ->when(
                    $id !== null,
                    fn ($q) => $q->where("$pivot.$idPivot", $id)
                ),
            $alias
        );

        return $query;
    }

    // protected function addIndexOBE(
    //     $query,
    //     ?int $id = null,
    //     string $alias = 'index_cpl_pr',
    //     string $pivot = 'rekap_cpl_prodi',
    //     string $kolom = 'cpl_id',
    //     string $table = 'cpls',
    // ) {
    //     $query->addSelect("$table.*");
    //     $query->selectSub(function ($query) use ($id, $pivot, $kolom, $table) {
    //         $query->from($pivot)
    //             ->whereColumn(
    //                 "$pivot.$kolom",
    //                 "$table.id"
    //             );

    //         if ($id !== null) {
    //             $query->where(
    //                 "$pivot.pr_id",
    //                 $id
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

    protected function addIndexOBE(
        $query,
        ?int $id = null,
        string $alias = 'index_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
        string $idPivot = 'pr_id'
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(function ($query) use ($id, $pivot, $kolom, $table, $idPivot) {
            $query->from($pivot)
                ->whereColumn(
                    "$pivot.$kolom",
                    "$table.id"
                );

            if ($id !== null) {
                $query->where(
                    "$pivot.$idPivot",
                    $id
                );
            }
            $query->selectRaw('ROUND((nilai / 100) * 4, 2)');

            $query->limit(1);

        }, $alias);

        return $query;
    }

    protected function addAkreditasOBE(
        $query,
        ?int $id = null,
        string $alias = 'huruf_cpl_pr',
        string $pivot = 'rekap_cpl_prodi',
        string $kolom = 'cpl_id',
        string $table = 'cpls',
        string $idPivot = 'pr_id'
    ) {
        $query->addSelect("$table.*");
        $query->selectSub(function ($query) use ($id, $pivot, $kolom, $table, $idPivot) {
            $bindings = [
                (float) (config('rps.min_a') ?: 86),
                (float) (config('rps.min_a_min') ?: 80),
                (float) (config('rps.min_b_plus') ?: 75),
                (float) (config('rps.min_b') ?: 70),
                (float) (config('rps.min_b_min') ?: 65),
                (float) (config('rps.min_c_plus') ?: 60),
                (float) (config('rps.min_c') ?: 56),
                (float) (config('rps.min_d') ?: 40),
            ];

            $query->from($pivot)
                ->whereColumn(
                    "$pivot.$kolom",
                    "$table.id"
                );

            if ($id !== null) {
                $query->where(
                    "$pivot.$idPivot",
                    $id
                );
            }

            $query->selectRaw("
                CASE
                    WHEN nilai >= ? THEN 'A'
                    WHEN nilai >= ? THEN 'A-'
                    WHEN nilai >= ? THEN 'B+'
                    WHEN nilai >= ? THEN 'B'
                    WHEN nilai >= ? THEN 'B-'
                    WHEN nilai >= ? THEN 'C+'
                    WHEN nilai >= ? THEN 'C'
                    WHEN nilai >= ? THEN 'D'
                    ELSE 'E'
                END
            ", $bindings);

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

    protected function addHurufMahasiswa(
        $query,
        string $alias = 'huruf_mahasiswa'
    ) {
        $query->selectSub(function ($sub) {
            $bindings = [
                config('rps.min_a', 86),
                config('rps.min_a_min', 80),
                config('rps.min_b_plus', 75),
                config('rps.min_b', 70),
                config('rps.min_b_min', 65),
                config('rps.min_c_plus', 60),
                config('rps.min_c', 56),
                config('rps.min_d', 40),
            ];

            $sub->from('nilai_mahasiswa')
                ->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
                ->whereColumn('mahasiswas.user_id', 'users.id')
                ->selectRaw("
                    CASE
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'A'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'A-'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'B+'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'B'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'B-'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'C+'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'C'
                        WHEN AVG(nilai_mahasiswa.nilai) >= ? THEN 'D'
                        ELSE 'E'
                    END
                ", $bindings);
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
        $queryUser->addSelect('users.*');

        $queryUser->selectSub(
            DB::table('rps_pivot_tim_dosen')
                ->join('tim_dosens', 'rps_pivot_tim_dosen.tim_dosen_id', '=', 'tim_dosens.id')
                ->join('tim_dosen_pivot_dosen', 'tim_dosens.id', '=', 'tim_dosen_pivot_dosen.tim_dosen_id')
                ->join('dosens', 'tim_dosen_pivot_dosen.dosen_id', '=', 'dosens.id')
                ->whereColumn('dosens.user_id', 'users.id')
                ->selectRaw('COUNT(DISTINCT rps_pivot_tim_dosen.rps_id)'),
            $alias
        );

        return $queryUser;
    }

    protected function addTotalSksDosen($queryUser, string $alias = 'total_sks')
    {
        $queryUser->addSelect('users.*');

        $queryUser->selectSub(
            DB::table(function ($sub) {
                $sub->from('rps_pivot_tim_dosen')
                    ->join('tim_dosens', 'rps_pivot_tim_dosen.tim_dosen_id', '=', 'tim_dosens.id')
                    ->join('tim_dosen_pivot_dosen', 'tim_dosens.id', '=', 'tim_dosen_pivot_dosen.tim_dosen_id')
                    ->join('dosens', 'tim_dosen_pivot_dosen.dosen_id', '=', 'dosens.id')
                    ->join('rps', 'rps_pivot_tim_dosen.rps_id', '=', 'rps.id')
                    ->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                    ->selectRaw('
                    dosens.user_id,
                    rps_pivot_tim_dosen.rps_id,
                    MAX(mata_kuliahs.sks_kuliah) as sks
                ')
                    ->groupBy('dosens.user_id', 'rps_pivot_tim_dosen.rps_id');
            }, 'rps_unik')
                ->whereColumn('rps_unik.user_id', 'users.id')
                ->selectRaw('COALESCE(SUM(rps_unik.sks), 0)'),
            $alias
        );

        return $queryUser;
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
