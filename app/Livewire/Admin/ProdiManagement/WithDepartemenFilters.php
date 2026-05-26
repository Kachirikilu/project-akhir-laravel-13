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
        $search = $this->search;

        if (! empty($search)) {
            $queryDp->searchDepartemen($search);
        }

        if (! empty($this->selectedFkId)) {
            $queryDp->where('departemens.fk_id', $this->selectedFkId);
        }

        if (! empty($this->selectedDpId)) {
            $queryDp->where('departemens.id', $this->selectedDpId);
        }

        $this->sortFieldOrderDepartemen($queryDp);

        return $queryDp;
    }

    public function sortFieldOrderDepartemen($queryDp)
    {
        $queryDp->select('departemens.*')->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id');

        return match ($this->sortField) {
            'kode'  => $queryDp->orderBy('kode_dp', $this->sortDirection)
                            ->orderBy('fakultas.kode_fk', $this->sortDirection),
            'departemen'  => $queryDp->orderBy('nama_dp', $this->sortDirection),
            'fakultas' => $queryDp->orderBy('fakultas.nama_fk', $this->sortDirection),
            'created_at' => $queryDp->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryDp->orderBy('updated_at', $this->sortDirection),
            default    => $queryDp->orderBy('id', 'desc'),
        };
    }

}
