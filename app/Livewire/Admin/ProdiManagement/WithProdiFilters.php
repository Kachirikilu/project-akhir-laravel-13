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

        if ($this->switchTable == 'prodi') {
            // $search = $this->search;
            // if (! empty($search)) {
            //     $queryPr->searchProdi($search);
            // }

            if (! empty($this->selectedDpId)) {
                $queryPr->where('dp_id', $this->selectedDpId);
            }
            if (! empty($this->selectedFkId)) {
                $queryPr->whereHas('dp_rel', function ($q) {
                    $q->where('fk_id', $this->selectedFkId);
                });
            }

            // $this->sortFieldOrderProdi($queryPr);
        }

        return $queryPr;
    }

    public function filterByStrata($strata)
    {
        $this->filterPr = $strata;
        $this->resetPage();
    }

    // public function sortFieldOrderProdi($queryPr)
    // {
    //     $primaryTable = 'prodis';
    //     if ($this->filterPr === 'departemen') {
    //         $queryPr->whereHas('dp_rel');
    //         $primaryTable = 'departemens';
    //     } elseif ($this->filterPr === 'fakultas') {
    //         $queryPr->whereHas('dp_rel.fakultas');
    //         $primaryTable = 'fakultas';
    //     }

    //     $queryPr->select("prodis.*");

    //     return match ($this->sortField) {
    //         'prodi' => $this->applyProdiSort($queryPr),
    //         'departemen' => $queryPr->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //             ->orderBy('departemens.nama_dp', $this->sortDirection),
    //         'fakultas' => $queryPr->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //             ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
    //             ->orderBy('fakultas.nama_fk', $this->sortDirection),
    //         'strata' => $queryPr->orderBy('prodis.strata', $this->sortDirection),
    //         'kode' => $this->applyProdiKodeSort($queryPr),
    //         default => $queryPr->orderBy("$primaryTable.id", $this->sortDirection),
    //     };
    // }

    // private function applyProdiKodeSort($queryPr)
    // {
    //     $queryPr->select('prodis.*')
    //         ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //         ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id');

    //     return match ($this->sortField) {
    //         'kode' => $queryPr->orderBy('kode_pr', $this->sortDirection)
    //             ->orderBy('departemens.kode_dp', $this->sortDirection)
    //             ->orderBy('fakultas.kode_fk', $this->sortDirection),
    //         'prodis' => $queryPr->orderBy('nama_pr', $this->sortDirection),
    //         'departemen' => $queryPr->orderBy('departemens.nama_dp', $this->sortDirection),
    //         'fakultas' => $queryPr->orderBy('fakultas.nama_fk', $this->sortDirection),
    //         'created_at' => $queryPr->orderBy('created_at', $this->sortDirection),
    //         'updated_at' => $queryPr->orderBy('updated_at', $this->sortDirection),
    //         default => $queryPr->orderBy('id', 'desc'),
    //     };
    // }
}
