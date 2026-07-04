<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Fakultas;
use Livewire\WithPagination;

trait WithFakultasFilters
{
    use WithPagination;

    public function inputFkSearch()
    {
        $queryFk = Fakultas::query()->with(['departemens', 'departemens.prodis']);

        if (! empty($this->selectedFkId)) {
            $queryFk->where('id', $this->selectedFkId);
        }

        if (! empty($this->selectedDpId)) {
            $queryFk->whereHas('departemens', function ($q) {
                $q->where('id', $this->selectedDpId);
            });
        }

        if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
            $search = $this->search;
            if (! empty($search)) {
                $queryFk->searchFakultas($search);
            }
            $this->sortFieldOrderFakultas($queryFk);
        }

        return $queryFk;
    }

    protected function addRekapFakultasFk(
        $queryFk,
        string $alias = 'rekap_fk'
    ) {
        $queryFk->addSelect('fakultas.*');

        $queryFk->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->join(
                    'departemens',
                    'prodis.dp_id',
                    '=',
                    'departemens.id'
                )
                ->whereColumn(
                    'departemens.fk_id',
                    'fakultas.id'
                )
                ->selectRaw('
                ROUND(
                    COALESCE(
                        AVG(rekap_cpl_prodi.nilai),
                        0
                    ),
                    2
                )
            ');

        }, $alias);

        return $queryFk;
    }

    protected function addIndexFakultasFk(
        $queryFk,
        string $alias = 'index_fk'
    ) {
        $queryFk->addSelect('fakultas.*');

        $queryFk->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->join(
                    'departemens',
                    'prodis.dp_id',
                    '=',
                    'departemens.id'
                )
                ->whereColumn(
                    'departemens.fk_id',
                    'fakultas.id'
                )
                ->selectRaw('
                CASE
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 86 THEN 4.00
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 80 THEN 3.70
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 75 THEN 3.30
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 70 THEN 3.00
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 65 THEN 2.70
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 60 THEN 2.30
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 56 THEN 2.00
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 40 THEN 1.00
                    ELSE 0
                END
            ');

        }, $alias);

        return $queryFk;
    }

    protected function addAkreditasFakultasFk(
        $queryFk,
        string $alias = 'akreditas_fk'
    ) {
        $queryFk->addSelect('fakultas.*');

        $queryFk->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->join(
                    'departemens',
                    'prodis.dp_id',
                    '=',
                    'departemens.id'
                )
                ->whereColumn(
                    'departemens.fk_id',
                    'fakultas.id'
                )
                ->selectRaw("
                CASE
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 86 THEN 'A'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 80 THEN 'A-'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 75 THEN 'B+'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 70 THEN 'B'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 65 THEN 'B-'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 60 THEN 'C+'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 56 THEN 'C'
                    WHEN AVG(rekap_cpl_prodi.nilai) >= 40 THEN 'D'
                    ELSE 'E'
                END
            ");

        }, $alias);

        return $queryFk;
    }

    public function sortFieldOrderFakultas($queryFk)
    {
        $queryFk->addSelect('fakultas.*');

        return match ($this->sortField) {
            'kode' => $queryFk->orderBy('kode_fk', $this->sortDirection),
            'fakultas' => $queryFk->orderBy('nama_fk', $this->sortDirection),
            'rekap_fk', 'index_fk', 'akreditas_fk' => $queryFk->orderBy('rekap_fk', $this->sortDirection),
            'created_at' => $queryFk->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryFk->orderBy('updated_at', $this->sortDirection),
            default => $queryFk->orderBy('id', $this->sortDirection),
        };
    }
}
