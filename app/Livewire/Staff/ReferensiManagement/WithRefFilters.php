<?php

namespace App\Livewire\Staff\ReferensiManagement;

use App\Models\Akademik\Referensi;
use Livewire\WithPagination;

trait WithRefFilters
{
    use WithPagination;

    public $filterRef = '';

    public function inputRefSearch()
    {
        $queryRef = Referensi::query()->with([
            'rps.mk_rel', 'rps.mk_rel.prodis', 'rps.mk_rel.prodis.dp_rel', 'rps.mk_rel.prodis.dp_rel.fk_rel',
            'cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.dp_rel', 'cpmks.rps.mk_rel.prodis.dp_rel.fk_rel',
            'scpmks.cpmks.rps.mk_rel', 'scpmks.cpmks.rps.mk_rel.prodis', 'scpmks.cpmks.rps.mk_rel.prodis.dp_rel', 'scpmks.cpmks.rps.mk_rel.prodis.dp_rel.fk_rel',

        ]);

        if ($this->switchTable === 'referensi') {
            // if (! empty($this->selectedPrId)) {
            //     $queryRef->where(function ($q) {
            //         $q->whereRelation('rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
            //         ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
            //         ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId);
            //     });
            // }
            // if (! empty($this->selectedDpId)) {
            //     $queryRef->where(function ($q) {
            //         $q->whereRelation('rps.mk_rel.prodis', 'dp_id', $this->selectedDpId)
            //         ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'dp_id', $this->selectedDpId)
            //         ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'dp_id', $this->selectedDpId);
            //     });
            // }
            // if (! empty($this->selectedFkId)) {
            //     $queryRef->where(function ($q) {
            //         $q->whereRelation('rps.mk_rel.prodis.dp_rel', 'fk_id', $this->selectedFkId)
            //         ->orWhereRelation('cpmks.rps.mk_rel.prodis.dp_rel', 'fk_id', $this->selectedFkId)
            //         ->orWhereRelation('scpmks.cpmks.rps.mk_rel.prodis.dp_rel', 'fk_id', $this->selectedFkId);
            //     });
            // }
            // if (! empty($this->selectedMKId)) {
            //     $queryRef->where(function ($q) {
            //         $q->whereRelation('rps', 'mk_id', $this->selectedMKId)
            //         ->orWhereRelation('cpmks.rps', 'mk_id', $this->selectedMKId)
            //         ->orWhereRelation('scpmks.cpmks.rps', 'mk_id', $this->selectedMKId);
            //     });
            // }
            if (! empty($this->selectedRPSId)) {
                $queryRef->where(function ($q) {
                    $q->whereRelation('rps', 'rps.id', $this->selectedRPSId)
                        ->orWhereRelation('cpmks.rps', 'rps.id', $this->selectedRPSId)
                        ->orWhereRelation('scpmks.cpmks.rps', 'rps.id', $this->selectedRPSId);
                });
            }
            if (! empty($this->selectedCPMKId)) {
                $queryRef->where(function ($q) {
                    $q->whereRelation('cpmks', 'cpmks.id', $this->selectedCPMKId)
                        ->orWhereRelation('scpmks.cpmks', 'cpmks.id', $this->selectedCPMKId);
                });
            }
            if (! empty($this->selectedSCPMKId)) {
                $queryRef->whereHas('scpmks', fn ($q) => $q->where('sub_cpmks.id', $this->selectedSCPMKId));
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $queryRef->searchRef($search);
                }
                $this->sortFieldOrderRef($queryRef);
            }
        }

        return $queryRef;

    }

    public function buttonRefFilter($queryRef, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo, $fiveYearsAgo, $tenYearsAgo)
    {
        if ($this->filterRef === 'ref-year') {
            $queryRef->where('tahun', $currentYear);
        } elseif ($this->filterRef === 'ref-2-3-years') {
            $queryRef->whereBetween('tahun', [$currentYear - 3, $currentYear - 2]);
        } elseif ($this->filterRef === 'ref-4-5-years') {
            $queryRef->whereBetween('tahun', [$currentYear - 5, $currentYear - 4]);
        } elseif ($this->filterRef === 'ref-6-10-years') {
            $queryRef->whereBetween('tahun', [$currentYear - 10, $currentYear - 6]);
        } elseif ($this->filterRef === 'ref-older-10') {
            $queryRef->where('tahun', '<', $currentYear - 10);
        }
    }

    public function filterByRef($ref)
    {
        $this->filterRef = $ref;
        $this->resetPage();
    }

    public function sortFieldOrderRef($queryRef)
    {
        $queryRef->select('referensis.*');

        return match ($this->sortField) {
            'kode' => $queryRef->orderBy('kode_ref', $this->sortDirection),

            'judul' => $queryRef->orderBy('judul', $this->sortDirection),
            'penulis' => $queryRef->orderBy('penulis', $this->sortDirection),
            'penerbit' => $queryRef->orderBy('penerbit', $this->sortDirection),
            'tahun' => $queryRef->orderBy('tahun', $this->sortDirection),
            'link' => $queryRef->orderBy('link', $this->sortDirection),

            'created_at' => $queryRef->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryRef->orderBy('updated_at', $this->sortDirection),

            default => $queryRef->orderBy('id', $this->sortDirection),
        };

        return $queryRef;
    }
}
