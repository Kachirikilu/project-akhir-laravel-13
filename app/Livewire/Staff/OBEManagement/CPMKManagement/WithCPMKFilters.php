<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Models\Akademik\CPMK;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

trait WithCPMKFilters
{
    use WithPagination;

    public $filterCPMK = '';

    public $searchBobotCPMK = '';

    public function updatingSearchBobotCPMK()
    {
        $this->resetPage();
    }

    public function resetInputBobotCPMK()
    {
        $this->reset('searchBobotCPMK');
        $this->resetPage();
    }

    public function inputCPMKSearch($prId = null)
    {
        $queryCPMK = CPMK::query()->with(['rps.mk_rel.prodis.dp_rel', 'rps.mk_rel.prodis', 'rps.mk_rel']);

        if ($this->switchTable === 'cpmk') {

            if (! empty($prId)) {
                $queryCPMK->whereHas('rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $prId));
            }
            if (! empty($this->selectedPrId)) {
                $queryCPMK->whereHas('rps.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
            }
            // if (! empty($this->selectedDpId)) {
            //     $queryCPMK->whereHas('rps.mk_rel.prodis', fn ($q) => $q->where('dp_id', $this->selectedDpId));
            // }
            // if (! empty($this->selectedFkId)) {
            //     $queryCPMK->whereHas('rps.mk_rel.prodis.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
            // }
            // if (! empty($this->selectedMKId)) {
            //     $queryCPMK->whereHas('rps', fn ($q) => $q->where('mk_id', $this->selectedMKId));
            // }
            if (! empty($this->selectedRPSId)) {
                $queryCPMK->whereHas('rps', fn ($q) => $q->where('rps.id', $this->selectedRPSId));
            }
            if (! empty($this->selectedCPLId)) {
                $queryCPMK->whereHas('cpls', fn ($q) => $q->where('cpls.id', $this->selectedCPLId));
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $queryCPMK->searchCPMK($search);
                }
                if (! empty($this->searchBobotCPMK)) {
                    $queryCPMK->searchCPMK($this->searchBobotCPMK, true);
                }
                $this->sortFieldOrderCPMK($queryCPMK);
            }

        }

        return $queryCPMK;

    }

    public function buttonCPMKFilter($queryCPMK, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo)
    {
        // dd($this->filterCPMK);
        if ($this->filterCPMK === 'cpmk-month') {
            $queryCPMK->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPMK === 'cpmk-6-months') {
            $queryCPMK->where('created_at', '>=', $sixMonthsAgo);
        } elseif ($this->filterCPMK === 'cpmk-year') {
            $queryCPMK->whereYear('created_at', $currentYear);
        } elseif ($this->filterCPMK === 'cpmk-older-5') {
            $queryCPMK->where('created_at', '<', $fiveYearsAgo);
        }
    }

    public function filterByCPMK($cpmk)
    {
        $this->filterCPMK = $cpmk;
        $this->resetPage();
    }

    public function sortFieldOrderCPMK($queryCPMK)
    {
        $queryCPMK->select('cpmks.*');

        return match ($this->sortField) {
            'kode' => $queryCPMK->orderBy('kode_cpmk', $this->sortDirection),
            'deskripsi' => $queryCPMK->orderBy(
                DB::table('cpls')
                    ->selectRaw("COALESCE(cpmks.deskripsi, GROUP_CONCAT(cpls.deskripsi SEPARATOR ' '))")
                    ->join('cpmk_pivot_cpl', 'cpls.id', '=', 'cpmk_pivot_cpl.cpl_id')
                    ->whereColumn('cpmk_pivot_cpl.cpmk_id', 'cpmks.id'),
                $this->sortDirection
            ),

            'count_cpl' => $queryCPMK->orderBy(
                DB::table('cpmk_pivot_cpl')
                    ->selectRaw('count(*)')
                    ->whereColumn('cpmk_pivot_cpl.cpmk_id', 'cpmks.id'),
                $this->sortDirection
            ),

            'count_scpmk' => $queryCPMK->orderBy(
                DB::table('cpmk_pivot_scpmk')
                    ->selectRaw('count(*)')
                    ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id'),
                $this->sortDirection
            ),

            'rekap_cpmk_pr' => $queryCPMK->orderBy('rekap_cpmk_pr', $this->sortDirection),
            'index_cpmk_pr' => $queryCPMK->orderBy('rekap_cpmk_pr', $this->sortDirection),
            'mutu_cpmk_pr' => $queryCPMK->orderBy('rekap_cpmk_pr', $this->sortDirection),

            'total_bobot' => $queryCPMK->orderBy(
                DB::table('cpmk_pivot_scpmk')
                    ->join('sub_cpmks', 'cpmk_pivot_scpmk.scpmk_id', '=', 'sub_cpmks.id')
                    ->selectRaw('sum(sub_cpmks.bobot)')
                    ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id'),
                $this->sortDirection
            ),

            'created_at' => $queryCPMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryCPMK->orderBy('updated_at', $this->sortDirection),

            default => $queryCPMK->orderBy('id', $this->sortDirection),
        };

        return $queryCPMK;
    }
}
