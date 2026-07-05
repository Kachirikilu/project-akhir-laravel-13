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

    protected function addMataKuliahProdiPr(
        $queryPr,
        string $aliasMk = 'count_mk',
        string $aliasRps = 'count_rps',
        string $aliasRpsAktif = 'count_rps_aktif',
        string $aliasRpsDraf = 'count_rps_draf',
    ) {
        $queryPr->selectSub(function ($query) {
            $query->from('prodi_pivot_mk')
                ->whereColumn('prodi_pivot_mk.pr_id', 'prodis.id')
                ->selectRaw('COUNT(*)');
        }, $aliasMk);

        $queryPr->selectSub(function ($query) {
            $query->from('rps')
                ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->join('prodi_pivot_mk', 'prodi_pivot_mk.mk_id', '=', 'mata_kuliahs.id')
                ->whereColumn('prodi_pivot_mk.pr_id', 'prodis.id')
                ->selectRaw('COUNT(rps.id)');
        }, $aliasRps);

        $queryPr->selectSub(function ($query) {
            $query->from('rps')
                ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->join('prodi_pivot_mk', 'prodi_pivot_mk.mk_id', '=', 'mata_kuliahs.id')
                ->whereColumn('prodi_pivot_mk.pr_id', 'prodis.id')
                ->where('rps.is_draf', 0)
                ->selectRaw('COUNT(rps.id)');
        }, $aliasRpsAktif);

        $queryPr->selectSub(function ($query) {
            $query->from('rps')
                ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->join('prodi_pivot_mk', 'prodi_pivot_mk.mk_id', '=', 'mata_kuliahs.id')
                ->whereColumn('prodi_pivot_mk.pr_id', 'prodis.id')
                ->where('rps.is_draf', 1)
                ->selectRaw('COUNT(rps.id)');
        }, $aliasRpsDraf);

        return $queryPr;
    }

    // protected function addRekapProdiPr(
    //     $queryPr,
    //     string $alias = 'rekap_pr'
    // ) {
    //     $queryPr->addSelect('prodis.*');

    //     $queryPr->selectSub(function ($query) {
    //         $query->from('rekap_cpl_prodi')
    //             ->whereColumn(
    //                 'rekap_cpl_prodi.pr_id',
    //                 'prodis.id'
    //             )
    //             ->selectRaw('
    //             ROUND(
    //                 COALESCE(
    //                     AVG(rekap_cpl_prodi.nilai),
    //                     0
    //                 ),
    //                 2
    //             )
    //         ');
    //     }, $alias);

    //     return $queryPr;
    // }

    // protected function addIndexProdiPr(
    //     $queryPr,
    //     string $alias = 'index_pr'
    // ) {
    //     $queryPr->addSelect('prodis.*');

    //     $queryPr->selectSub(function ($query) {
    //         $query->from('rekap_cpl_prodi')
    //             ->whereColumn(
    //                 'rekap_cpl_prodi.pr_id',
    //                 'prodis.id'
    //             )
    //             ->selectRaw('
    //             CASE
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 86 THEN 4.00
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 80 THEN 3.70
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 75 THEN 3.30
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 70 THEN 3.00
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 65 THEN 2.70
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 60 THEN 2.30
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 56 THEN 2.00
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 40 THEN 1.00
    //                 ELSE 0
    //             END
    //         ');
    //     }, $alias);

    //     return $queryPr;
    // }

    // protected function addAkreditasProdiPr(
    //     $queryPr,
    //     string $alias = 'akreditas_pr'
    // ) {
    //     $queryPr->addSelect('prodis.*');

    //     $queryPr->selectSub(function ($query) {
    //         $query->from('rekap_cpl_prodi')
    //             ->whereColumn(
    //                 'rekap_cpl_prodi.pr_id',
    //                 'prodis.id'
    //             )
    //             ->selectRaw("
    //             CASE
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 86 THEN 'A'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 80 THEN 'A-'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 75 THEN 'B+'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 70 THEN 'B'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 65 THEN 'B-'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 60 THEN 'C+'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 56 THEN 'C'
    //                 WHEN AVG(rekap_cpl_prodi.nilai) >= 40 THEN 'D'
    //                 ELSE 'E'
    //             END

    //         ");

    //     }, $alias);

    //     return $queryPr;

    // }

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
            'target_sks' => $queryPr->orderBy('prodis.target_sks', $this->sortDirection),
            'strata' => $queryPr->orderBy('prodis.strata', $this->sortDirection),

            'nilai_pr', 'rekap_pr', 'index_pr', 'akreditas_pr' => $queryPr->orderBy('nilai_pr', $this->sortDirection),
            
            'count_mk' => $queryPr->orderBy('count_mk', $this->sortDirection),
            'count_rps' => $queryPr->orderBy('count_rps', $this->sortDirection),
            'count_rps_aktif' => $queryPr->orderBy('count_rps_aktif', $this->sortDirection),
            'count_rps_draf' => $queryPr->orderBy('count_rps_draf', $this->sortDirection),

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
