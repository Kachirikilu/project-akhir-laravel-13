<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Departemen;
use Livewire\WithPagination;

trait WithDepartemenFilters
{
    use WithPagination;

    public function inputDpSearch()
    {
        $queryDp = Departemen::query()->with(['fk_rel', 'prodis']);

        if (! empty($this->selectedFkId)) {
            $queryDp->where('departemens.fk_id', $this->selectedFkId);
        }

        if (! empty($this->selectedDpId)) {
            $queryDp->where('departemens.id', $this->selectedDpId);
        }

        if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
            $search = $this->search;
            if (! empty($search)) {
                $queryDp->searchDepartemen($search);
            }

            $this->sortFieldOrderDepartemen($queryDp);
        }

        return $queryDp;
    }

    protected function addRekapDepartemen(
        $queryDp,
        string $alias = 'rekap_dp'
    ) {
        $queryDp->addSelect('departemens.*');

        $queryDp->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->whereColumn(
                    'prodis.dp_id',
                    'departemens.id'
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

        return $queryDp;
    }

    protected function addIndexDepartemen(
        $queryDp,
        string $alias = 'index_dp'
    ) {
        $queryDp->addSelect('departemens.*');

        $queryDp->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->whereColumn(
                    'prodis.dp_id',
                    'departemens.id'
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

        return $queryDp;
    }

    protected function addAkreditasDepartemen(
        $queryDp,
        string $alias = 'akreditas_dp'
    ) {
        $queryDp->addSelect('departemens.*');

        $queryDp->selectSub(function ($query) {

            $query->from('rekap_cpl_prodi')
                ->join(
                    'prodis',
                    'rekap_cpl_prodi.pr_id',
                    '=',
                    'prodis.id'
                )
                ->whereColumn(
                    'prodis.dp_id',
                    'departemens.id'
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

        return $queryDp;
    }

    public function sortFieldOrderDepartemen($queryDp)
    {
        $queryDp->addSelect('departemens.*')->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id');

        return match ($this->sortField) {
            'kode' => $queryDp->orderBy('kode_dp', $this->sortDirection)
                ->orderBy('fakultas.kode_fk', $this->sortDirection),
            'departemen' => $queryDp->orderBy('nama_dp', $this->sortDirection),
            'fakultas' => $queryDp->orderBy('fakultas.nama_fk', $this->sortDirection),
            'rekap_dp', 'index_dp', 'akreditas_dp' => $queryDp->orderBy('rekap_dp', $this->sortDirection),
            'created_at' => $queryDp->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryDp->orderBy('updated_at', $this->sortDirection),
            default => $queryDp->orderBy('id', $this->sortDirection),
        };
    }
}
