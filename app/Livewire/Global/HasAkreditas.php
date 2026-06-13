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

            $query->selectRaw('
            CASE
                WHEN nilai >= 86 THEN 4.00
                WHEN nilai >= 80 THEN 3.70
                WHEN nilai >= 75 THEN 3.30
                WHEN nilai >= 70 THEN 3.00
                WHEN nilai >= 65 THEN 2.70
                WHEN nilai >= 60 THEN 2.30
                WHEN nilai >= 56 THEN 2.00
                WHEN nilai >= 40 THEN 1.00
                ELSE 0.00
            END
        ');

            $query->limit(1);

        }, $alias);

        return $query;
    }

    protected function addAkreditasProdi(
        $query,
        ?int $prId = null,
        string $alias = 'akreditas_cpl_pr',
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
}
