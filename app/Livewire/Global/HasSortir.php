<?php

namespace App\Livewire\Global;

trait HasSortir
{
    public function sortField($table, $sortField, $columns, $aliases)
    {
        if (! isset($columns[$table])) {
            return;
        }

        $targetCols = $columns[$table];
        $matchedField = null;

        $activeGroup = [];
        foreach ($aliases as $master => $related) {
            if ($sortField === $master || in_array($sortField, $related)) {
                $activeGroup = array_unique(array_merge([$master], $related));
                break;
            }
        }
        if (empty($activeGroup)) {
            $activeGroup = [$sortField];
        }

        foreach ($activeGroup as $fieldInGroup) {
            if (in_array($fieldInGroup, $targetCols)) {
                $matchedField = $fieldInGroup;
                break;
            }
        }

        if (! $matchedField) {
            $currentPos = 1;
            foreach ($columns as $tableName => $cols) {
                $pos = array_search($sortField, $cols);
                if ($pos !== false) {
                    $currentPos = $pos;
                    break;
                }
            }

            $maxPosInTarget = max(array_keys($targetCols));
            $finalPos = min($currentPos, $maxPosInTarget);
            $matchedField = $targetCols[$finalPos];
        }

        $this->sortField = $matchedField;
    }

    public function applyProdiSort($query, $strata = 'strata', $nama = 'nama_pr')
    {
        return $query->orderByRaw("
            CASE 
                WHEN $strata = 'Sarjana' THEN 1 
                WHEN $strata = 'Magister' THEN 2 
                WHEN $strata = 'Doktor' THEN 3 
                ELSE 4 
            END {$this->sortDirection}
        ")->orderByRaw("$nama {$this->sortDirection}");
    }

    public function applyMKKodeSort($queryMK, $sortir = 'mata_kuliahs.id')
    {
        return $queryMK->orderByRaw("
            (
                SELECT CONCAT(
                    MIN(
                        CASE 
                            WHEN mk.level_mk = 1 THEN COALESCE(p.kode_pr, j.kode_dp, f.kode_fk, 'UNI')
                            WHEN mk.level_mk = 2 THEN COALESCE(j.kode_dp, f.kode_fk, 'UNI')
                            WHEN mk.level_mk = 3 THEN COALESCE(f.kode_fk, 'UNI')
                            WHEN mk.level_mk = 4 THEN 'UNI'
                            ELSE mk.kode_mk
                        END
                    ),
                    LPAD(mk.digit_semester, 2, '0'),
                    LPAD(mk.digit_mk, 2, '0')
                )
                FROM mata_kuliahs mk
                LEFT JOIN prodi_pivot_mk ppm ON mk.id = ppm.mk_id
                LEFT JOIN prodis p ON ppm.pr_id = p.id
                LEFT JOIN departemens j ON p.dp_id = j.id
                LEFT JOIN fakultas f ON j.fk_id = f.id
                WHERE mk.id = {$sortir}
            ) {$this->sortDirection}
        ");
    }

    public function applyRPSKodeSort($queryRPS, $sortir = 'rps')
    {
        return $queryRPS->orderByRaw("
        {$sortir}.akademik {$this->sortDirection},
        (
            SELECT mk.digit_semester % 2 
            FROM mata_kuliahs mk 
            WHERE mk.id = {$sortir}.mk_id
        ) {$this->sortDirection},
        (
            SELECT CONCAT(
                MIN(
                    CASE 
                        WHEN mk.level_mk = 1 THEN COALESCE(p.kode_pr, j.kode_dp, f.kode_fk, 'UNI')
                        WHEN mk.level_mk = 2 THEN COALESCE(j.kode_dp, f.kode_fk, 'UNI')
                        WHEN mk.level_mk = 3 THEN COALESCE(f.kode_fk, 'UNI')
                        WHEN mk.level_mk = 4 THEN 'UNI'
                        ELSE mk.kode_mk
                    END
                ),
                LPAD(mk.digit_semester, 2, '0'),
                LPAD(mk.digit_mk, 2, '0')
            )
            FROM mata_kuliahs mk
            LEFT JOIN prodi_pivot_mk ppm ON mk.id = ppm.mk_id
            LEFT JOIN prodis p ON ppm.pr_id = p.id
            LEFT JOIN departemens j ON p.dp_id = j.id
            LEFT JOIN fakultas f ON j.fk_id = f.id
            WHERE mk.id = {$sortir}.mk_id
        ) {$this->sortDirection}
    ");
    }

    // public function applyJadwalKodeSort($queryJadwal, $sortir = 'kelas_jadwals')
    // {
    //     return $queryJadwal->orderByRaw(
    //         "(
    //             SELECT CONCAT_WS('-',
    //                 k.kode_kelas,
    //                 {$sortir}.label_kelas,
    //                 {$sortir}.kode_wilayah,
    //                 mk.kode_semester,
    //                 (
    //                     CASE
    //                         WHEN CAST({$sortir}.tahun AS UNSIGNED) >= 3000 THEN CAST({$sortir}.tahun AS CHAR)
    //                         WHEN CAST({$sortir}.tahun AS UNSIGNED) >= 2100 THEN RIGHT(CAST({$sortir}.tahun AS CHAR), 3)
    //                         WHEN CAST({$sortir}.tahun AS UNSIGNED) >= 2000 THEN RIGHT(CAST({$sortir}.tahun AS CHAR), 2)
    //                         ELSE CAST({$sortir}.tahun AS CHAR)
    //                     END
    //                 )
    //             )
    //             FROM kelas k
    //             LEFT JOIN rps r ON k.rps_id = r.id
    //             LEFT JOIN mata_kuliahs mk ON r.mk_id = mk.id
    //             WHERE k.id = {$sortir}.kelas_id
    //             LIMIT 1
    //         ) {$this->sortDirection}
    //     ");
    // }
}
