<?php

namespace App\Livewire\Staff\CPLManagement;

use App\Models\Akademik\CPL;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

trait WithCPLFilters
{
    use WithPagination;

    public $filterCPL = '';

    public function inputCPLSearch($prId = null)
    {
        $queryCPL = CPL::query()->with([
            'prodis', 'prodis.dp_rel', 'prodis.dp_rel.fk_rel',
            'cpmks.rps.mk_rel', 'cpmks.rps.mk_rel.prodis', 'cpmks.rps.mk_rel.prodis.dp_rel', 'cpmks.rps.mk_rel.prodis.dp_rel.fk_rel']);

        if ($this->switchTable === 'cpl') {

            if (! empty($prId)) {
                $queryCPL->where(function ($q) use ($prId) {
                    $q->whereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $prId)
                        ->orWhereRelation('prodis', 'prodis.id', $prId);
                });
            }
            if (! empty($this->selectedPrId)) {
                $queryCPL->where(function ($q) {
                    $q->whereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $this->selectedPrId)
                        ->orWhereRelation('prodis', 'prodis.id', $this->selectedPrId);
                });
            }
            if (! empty($this->selectedRPSId)) {
                $queryCPL->where(function ($q) {
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

    protected function addRekapCplProdi(
        $queryCPL,
        ?int $prId = null,
        string $alias = 'rekap_cpl_pr'
    ) {
        $queryCPL->selectSub(
            DB::table('rekap_cpl_prodi')
                ->selectRaw('COALESCE(MAX(nilai), 0)')
                ->whereColumn('rekap_cpl_prodi.cpl_id', 'cpls.id')
                ->when(
                    $prId !== null,
                    fn ($q) => $q->where('rekap_cpl_prodi.pr_id', $prId)
                ),
            $alias
        );

        return $queryCPL;
    }

    protected function addIndexCplProdi(
        $queryCPL,
        ?int $prId = null,
        string $alias = 'index_cpl_pr'
    ) {
        $queryCPL->selectSub(function ($query) use ($prId) {

            $query->from('rekap_cpl_prodi')
                ->whereColumn(
                    'rekap_cpl_prodi.cpl_id',
                    'cpls.id'
                );

            if ($prId !== null) {
                $query->where(
                    'rekap_cpl_prodi.pr_id',
                    $prId
                );
            }

            $query->selectRaw('
            CASE
                WHEN nilai >= 86 THEN 4.00
                WHEN nilai >= 80 THEN 3.70
                WHEN nilai >= 75 THEN 3.30
                WHEN nilai >= 70 THEN 3.00
                WHEN nilai >= 65 THEN 2.70
                WHEN nilai >= 60 THEN 2.30
                WHEN nilai >= 56 THEN 2.00
                WHEN nilai >= 40 THEN 1.00
                ELSE 0.00
            END
        ');

            $query->limit(1);

        }, $alias);

        return $queryCPL;
    }

    protected function addAkreditasCplProdi(
        $queryCPL,
        ?int $prId = null,
        string $alias = 'akreditas_cpl_pr'
    ) {
        $queryCPL->selectSub(function ($query) use ($prId) {

            $query->from('rekap_cpl_prodi')
                ->whereColumn(
                    'rekap_cpl_prodi.cpl_id',
                    'cpls.id'
                );

            if ($prId !== null) {
                $query->where(
                    'rekap_cpl_prodi.pr_id',
                    $prId
                );
            }

            $query->selectRaw("
            CASE
                WHEN nilai >= 86 THEN 'A'
                WHEN nilai >= 80 THEN 'A-'
                WHEN nilai >= 75 THEN 'B+'
                WHEN nilai >= 70 THEN 'B'
                WHEN nilai >= 65 THEN 'B-'
                WHEN nilai >= 60 THEN 'C+'
                WHEN nilai >= 56 THEN 'C'
                WHEN nilai >= 40 THEN 'D'
                ELSE 'E'
            END
        ");

            $query->limit(1);

        }, $alias);

        return $queryCPL;
    }

    protected function addCountRpsCpl($queryCPL, ?int $prId = null, string $alias = 'count_rps')
    {
        return $queryCPL->selectSub(function ($query) use ($prId) {
            $query->from('rps')
                ->join(
                    'rps_pivot_cpmk',
                    'rps.id',
                    '=',
                    'rps_pivot_cpmk.rps_id'
                )
                ->join(
                    'cpmk_pivot_cpl',
                    'rps_pivot_cpmk.cpmk_id',
                    '=',
                    'cpmk_pivot_cpl.cpmk_id'
                )
                ->whereColumn(
                    'cpmk_pivot_cpl.cpl_id',
                    'cpls.id'
                );

            if ($prId) {
                $query
                    ->join(
                        'mata_kuliahs',
                        'rps.mk_id',
                        '=',
                        'mata_kuliahs.id'
                    )
                    ->join(
                        'prodi_pivot_mk',
                        'mata_kuliahs.id',
                        '=',
                        'prodi_pivot_mk.mk_id'
                    )
                    ->where(
                        'prodi_pivot_mk.pr_id',
                        $prId
                    );
            }

            $query->selectRaw('COUNT(DISTINCT rps.id)');
        }, $alias);
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
            'count_rps' => $queryCPL->orderBy('count_rps', $this->sortDirection),
            'count_rps_pr' => $queryCPL->orderBy('count_rps_pr', $this->sortDirection),
            'rekap_cpl_pr' => $queryCPL->orderBy('rekap_cpl_pr', $this->sortDirection),
            'index_cpl_pr' => $queryCPL->orderBy('rekap_cpl_pr', $this->sortDirection),
            'akreditas_cpl_pr' => $queryCPL->orderBy('rekap_cpl_pr', $this->sortDirection),
            'created_at' => $queryCPL->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryCPL->orderBy('updated_at', $this->sortDirection),

            default => $queryCPL->orderBy('id', $this->sortDirection),
        };

        return $queryCPL;
    }
}
