<?php

namespace App\Livewire\Staff\CPMKManagement;

use App\Models\Akademik\SubCPMK;
use Livewire\WithPagination;

trait WithSubCPMKFilters
{
    use WithPagination;

    public $filterSCPMK = '';

    public $searchBobotSCPMK = '';

    public function updatingSearchBobotSCPMK()
    {
        $this->resetPage();
    }

    public function resetInputBobotSCPMK()
    {
        $this->reset('searchBobotSCPMK');
        $this->resetPage();
    }

    public function inputSCPMKSearch($prId = null)
    {
        $querySCPMK = SubCPMK::query()->with(['cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.dp_rel', 'cpmks.rps.mk_rel.prodis.dp_rel.fk_rel']);

        if ($this->switchTable === 'sub-cpmk') {

            if (! empty($prId)) {
                $querySCPMK->whereHas('cpmks.rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $prId));
            }
            if (! empty($this->selectedPrId)) {
                $querySCPMK->whereHas('cpmks.rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
            }
            // if (! empty($this->selectedDpId)) {
            //     $querySCPMK->whereHas('cpmks.rps.mk_rel.prodis', fn ($q) => $q->where('dp_id', $this->selectedDpId));
            // }
            // if (! empty($this->selectedFkId)) {
            //     $querySCPMK->whereHas('cpmks.rps.mk_rel.prodis.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
            // }
            // if (! empty($this->selectedMKId)) {
            //     $querySCPMK->whereHas('cpmks.rps', fn ($q) => $q->where('mk_id', $this->selectedMKId));
            // }
            if (! empty($this->selectedRPSId)) {
                $querySCPMK->whereHas('cpmks.rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
            }
            if (! empty($this->selectedCPMKId)) {
                $querySCPMK->whereHas('cpmks', fn ($q) => $q->where('cpmks.id', $this->selectedCPMKId));
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $querySCPMK->searchSCPMK($search);
                }
                if (! empty($this->searchBobotSCPMK)) {
                    $querySCPMK->searchSCPMK($this->searchBobotSCPMK, true);
                }
                $this->sortFieldOrderSCPMK($querySCPMK);
            }
        }

        return $querySCPMK;

    }

    public function buttonSCPMKFilter($querySCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        if ($this->filterSCPMK === 'scpmk-month') {
            $querySCPMK->whereMonth('created_at', $now->month)->whereYear('created_at', $currentYear);
        } elseif ($this->filterSCPMK === 'scpmk-6-months') {
            $querySCPMK->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterSCPMK === 'scpmk-year') {
            $querySCPMK->whereYear('created_at', $currentYear);
        } elseif ($this->filterSCPMK === 'scpmk-older-5') {
            $querySCPMK->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterBySCPMK($scpmk)
    {
        $this->filterSCPMK = $scpmk;
        $this->resetPage();
    }

    public function sortFieldOrderSCPMK($querySCPMK)
    {
        $querySCPMK->select('sub_cpmks.*');

        return match ($this->sortField) {
            'kode' => $querySCPMK->orderBy('kode_scpmk', $this->sortDirection),

            'deskripsi' => $querySCPMK->orderBy('deskripsi', $this->sortDirection),
            'metodologi' => $querySCPMK->orderBy('metodologi', $this->sortDirection),
            'indikator' => $querySCPMK->orderBy('indikator', $this->sortDirection),
            'metode' => $querySCPMK->orderBy('metode', $this->sortDirection),
            'bobot' => $querySCPMK->orderBy('bobot', $this->sortDirection),
            'tugas' => $querySCPMK->orderBy('deskripsi_tugas', $this->sortDirection),
            'tugas' => $querySCPMK->orderBy('waktu_tugas', $this->sortDirection),
            'mandiri' => $querySCPMK->orderBy('waktu_mandiri', $this->sortDirection),

            'rekap_scpmk_pr' => $querySCPMK->orderBy('rekap_scpmk_pr', $this->sortDirection),
            'index_scpmk_pr' => $querySCPMK->orderBy('rekap_scpmk_pr', $this->sortDirection),
            'akreditas_scpmk_pr' => $querySCPMK->orderBy('rekap_scpmk_pr', $this->sortDirection),
            
            'created_at' => $querySCPMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $querySCPMK->orderBy('updated_at', $this->sortDirection),

            default => $querySCPMK->orderBy('id', $this->sortDirection),
        };

        return $querySCPMK;
    }
}
