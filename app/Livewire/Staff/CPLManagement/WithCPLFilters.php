<?php

namespace App\Livewire\Staff\CPLManagement;

use App\Models\Akademik\CPL;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

    public $filterCPL = '';

    public function inputCPLSearch()
    {
        $queryCPL = CPL::query()->with([
            // 'rps.mk_rel', 'rps.mk_rel.prodis', 'rps.mk_rel.prodis.dp_rel', 'rps.mk_rel.prodis.dp_rel.fk_rel',
            'prodis', 'prodis.dp_rel', 'prodis.dp_rel.fk_rel',
            'cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.dp_rel', 'cpmks.rps.mk_rel.prodis.dp_rel.fk_rel']);

        if ($this->switchTable === 'cpl') {
            if (! empty($this->selectedPrId)) {
                $queryCPL->where(function ($q) {
                    $q->whereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
                        ->orWhereRelation('prodis', 'prodis.id', $this->selectedPrId);
                    // $q->whereRelation('rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
                    //     ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId);
                });
            }
            // if (! empty($this->selectedDpId)) {
            //     $queryCPL->where(function ($q) {
            //         $q->whereRelation('rps.mk_rel.prodis', 'dp_id', $this->selectedDpId)
            //             ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'dp_id', $this->selectedDpId);
            //     });
            // }
            // if (! empty($this->selectedFkId)) {
            //     $queryCPL->where(function ($q) {
            //         $q->whereRelation('rps.mk_rel.prodis.dp_rel', 'fk_id', $this->selectedFkId)
            //             ->orWhereRelation('cpmks.rps.mk_rel.prodis.dp_rel', 'fk_id', $this->selectedFkId);
            //     });
            // }
            // if (! empty($this->selectedMKId)) {
            //     $queryCPL->where(function ($q) {
            //         $q->whereRelation('rps', 'mk_id', $this->selectedMKId)
            //             ->orWhereRelation('cpmks.rps', 'mk_id', $this->selectedMKId);
            //     });
            // }
            if (! empty($this->selectedRPSId)) {
                $queryCPL->where(function ($q) {
                    // $q->whereHas('rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId))
                    //     ->orWhereHas('cpmks.rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
                    $q->whereHas('cpmks.rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
                });
            }
            if (! empty($this->selectedCPMKId)) {
                $queryCPL->whereRelation('cpmks', 'cpmks.id', $this->selectedCPMKId);
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $queryCPL->searchCPL($search);
                }
                $this->sortFieldOrderCPL($queryCPL);
            }
        }

        return $queryCPL;
    }

    public function buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        if ($this->filterCPL === 'cpl-month') {
            $queryCPL->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPL === 'cpl-6-months') {
            $queryCPL->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterCPL === 'cpl-year') {
            $queryCPL->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPL === 'cpl-older-5') {
            $queryCPL->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterByCPL($cpl)
    {
        $this->filterCPL = $cpl;
        $this->resetPage();
    }

    public function sortFieldOrderCPL($queryCPL)
    {
        $queryCPL->select('cpls.*');

        return match ($this->sortField) {
            // 'kode' => $queryCPL->orderBy('kode_cpl', $this->sortDirection),
            'kode' => $this->applyCPLKodeSort($queryCPL),

            'deskripsi' => $queryCPL->orderBy('deskripsi', $this->sortDirection),
            'created_at' => $queryCPL->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryCPL->orderBy('updated_at', $this->sortDirection),

            default => $queryCPL->orderBy('id', $this->sortDirection),
        };

        return $queryCPL;
    }
}
