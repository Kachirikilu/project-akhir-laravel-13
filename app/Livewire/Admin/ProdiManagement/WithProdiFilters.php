<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Models\ProgramStudi\Prodi;
// use App\Livewire\Global\HasSortir;
use Livewire\WithPagination;

trait WithProdiFilters
{
    // use HasSortir;
    use WithPagination;

    public $filterPr = '';

    public function inputPrSearch()
    {
        $queryPr = Prodi::query()->with(['dp_rel', 'dp_rel.fk_rel']);

        if ($this->switchTable == '') {

            if (! empty($this->selectedDpId)) {
                $queryPr->where('dp_id', $this->selectedDpId);
            }
            if (! empty($this->selectedFkId)) {
                $queryPr->whereHas('dp_rel', function ($q) {
                    $q->where('fk_id', $this->selectedFkId);
                });
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $queryPr->searchProdi($search);
                }
                $this->sortFieldOrderProdi($queryPr);
            }
        }

        return $queryPr;
    }

    protected function addRekapProdi(
        $queryPr,
        string $alias = 'rekap_pr'
    ) {
        $queryPr->addSelect('prodis.*');

        $queryPr->selectSub(function ($query) {
            $query->from('rekap_cpl_prodi')
                ->whereColumn(
                    'rekap_cpl_prodi.pr_id',
                    'prodis.id'
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

        return $queryPr;
    }

    protected function addIndexProdi(
        $queryPr,
        string $alias = 'index_pr'
    ) {
        $queryPr->addSelect('prodis.*');

        $queryPr->selectSub(function ($query) {
            $query->from('rekap_cpl_prodi')
                ->whereColumn(
                    'rekap_cpl_prodi.pr_id',
                    'prodis.id'
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

        return $queryPr;
    }

    protected function addAkreditasProdi(
        $queryPr,
        string $alias = 'akreditas_pr'
    ) {
        $queryPr->addSelect('prodis.*');

        $queryPr->selectSub(function ($query) {
            $query->from('rekap_cpl_prodi')
                ->whereColumn(
                    'rekap_cpl_prodi.pr_id',
                    'prodis.id'
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

        return $queryPr;

    }

    public function filterByStrata($strata)
    {
        $this->filterPr = $strata;
        $this->resetPage();
    }

    public function sortFieldOrderProdi($queryPr)
    {
        // $primaryTable = 'prodis';
        // if ($this->switchTable === 'departemen') {
        //     $queryPr->whereHas('dp_rel');
        //     $primaryTable = 'departemens';
        // } elseif ($this->switchTable === 'fakultas') {
        //     $queryPr->whereHas('dp_rel.fakultas');
        //     $primaryTable = 'fakultas';
        // }

        $queryPr->addSelect('prodis.*');

        return match ($this->sortField) {
            'program_studi' => $this->applyProdiSort($queryPr),
            'departemen' => $queryPr->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                ->orderBy('departemens.nama_dp', $this->sortDirection),
            'fakultas' => $queryPr->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                ->orderBy('fakultas.nama_fk', $this->sortDirection),
            'strata' => $queryPr->orderBy('prodis.strata', $this->sortDirection),
            'rekap_pr', 'index_pr', 'akreditas_pr' => $queryPr->orderBy('rekap_pr', $this->sortDirection),
            'kode' => $this->applyProdiKodeSort($queryPr),
            'created_at' => $queryPr->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryPr->orderBy('updated_at', $this->sortDirection),
            default => $queryPr->orderBy('prodis.id', $this->sortDirection),
        };
    }

    private function applyProdiKodeSort($queryPr)
    {
        $queryPr->addSelect('prodis.*')
            ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
            ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id');

        return match ($this->sortField) {
            'kode' => $queryPr
                ->orderByRaw("
                COALESCE(
                    NULLIF(prodis.kode_pr, ''),
                    NULLIF(departemens.kode_dp, ''),
                    NULLIF(fakultas.kode_fk, ''),
                    'UNI'
                ) {$this->sortDirection}
            ")
                ->orderByRaw("
                CASE prodis.strata
                    WHEN 'Sarjana' THEN 1
                    WHEN 'Magister' THEN 2
                    WHEN 'Doktor' THEN 3
                    ELSE 99
                END {$this->sortDirection}
            "),
            default => $queryPr->orderBy('prodis.id', 'desc'),
        };
    }
}
