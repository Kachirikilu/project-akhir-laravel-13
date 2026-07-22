<?php

namespace App\Livewire\Global;

use App\Models\ProgramStudi\Departemen;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithDepartemenSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $dpSearchQuery = '';

    public $dpSearchResults = [];

    public $dp_id;

    public $dp_name = '';

    public $dp_items = [];

    public $dpNameSearch = '';

    public $dpResults = [];

    public $selectedDpId = null;

    public $dpLevel = 1;

    private function mapDp($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'departemen' => $j->departemenDp,
            'fakultas' => $j->fakultasFk,
        ])->toArray();
    }

    private function mapDpSearch($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'kode_text' => 'Kode: '.$j->kode,
            'departemen' => $j->departemenDp,
            'fakultas' => $j->fakultasFk,
        ])->toArray();
    }

    private function dpQuery()
    {
        return Departemen::query()->with('fk_rel');
    }

    private function itemsDp($j)
    {
        if (! $j) {
            return null;
        }

        return [
            'id' => $j->id,
            'kode' => $j->kode,
            'slot1' => $j->departemenDp,
            'slot2' => $j->fakultasFk,
        ];
    }

    public function inputDpFilter()
    {
        $search = trim($this->dpSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->dp_name)) {
            $this->dpSearchResults = $this->mapDpSearch(
                $this->dpQuery()->searchDepartemen($search)->limit(12)->get()
                // $this->searchOutputPr($this->dpQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->dp_name) {
            $this->dpSearchResults = $this->getDpbyUser('search');
        } else {
            $this->dpSearchResults = [];
        }
    }

    // public function inputDpFilter3()
    // {
    //     $search = trim($this->dpSearchQuery);

    //     // 1. Jika teks query yang diketik BERBEDA dengan nama yang sedang terselect,
    //     //    artinya user sedang mengetik pencarian baru. Hancurkan status select lama!
    //     if ($this->dp_name && $search !== $this->dp_name) {
    //         $this->dp_name = null;
    //         $this->selectedDpId = null;
    //         $this->dp_items = null;
    //     }

    //     // 2. Sekarang filter pencarian dapat dievaluasi dengan bersih tanpa intervensi dp_name lama
    //     if (strlen($search) > 1 || is_numeric($search)) {
    //         $this->dpSearchResults = $this->mapDpSearch(
    //             $this->searchOutputPr($this->dpQuery(), $search, 12)
    //         );
    //     } elseif (empty($search) || $this->dp_name) {
    //         $this->dpSearchResults = $this->getDpbyUser('search');
    //     } else {
    //         $this->dpSearchResults = [];
    //     }
    // }

    public function resetDpFilter()
    {
        $this->reset(['selectedDpId', 'dpSearchQuery', 'dp_name', 'dp_items']);
        $this->resetPage();
    }

    public function selectDpForFilter($id)
    {
        $data = $this->dpQuery()->find($id);

        if ($data) {
            $this->selectedDpId = $id;
            $this->dp_name = $data->departemenDp;
            $this->dpSearchQuery = $data->departemenDp;
            $this->dp_items = $this->itemsDp($data);
            $this->dpSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedDpNameSearch($value)
    {
        $this->dp_id = null;
        $this->dp_items = null;
        $this->resetErrorBag(['dp_id', 'dpNameSearch']);

        $query = $this->dpQuery()->select('departemens.*');

        $this->haveDpParent($query);

        if (trim(strlen($value)) > 0) {
            $results = $query->searchDepartemen($value)->limit(12)->get();
            // $results = $this->searchOutputPr($query, $value, 12);

            $this->dpResults = $this->mapDp($results);

            $exactMatch = $results->first(function ($departemen) use ($value) {
                $input = str($value)->lower()->trim();
                $nama = str($departemen->departemen)->lower();
                $kode = str($departemen->kode)->lower();

                return $input->is([
                    $nama,
                    "departemen $nama",
                    $kode,
                ]);
            });

            if ($exactMatch) {
                $this->dp_id = $exactMatch->id;
                $this->dp_items = $this->itemsDp($exactMatch);
                $this->dpNameSearch = $exactMatch->departemenDp;
                $this->dpResults = [];
            }

        } else {
            if (Auth::user()->dp_id) {
                $this->dpResults = $this->getDpbyUser();
            } else {
                $this->dpResults = $this->mapDp(
                    $query->orderBy('departemens.nama_dp')->limit(12)->get()
                );
            }
        }
    }

    public function getDpbyUser($type = 'complex')
    {
        $user = Auth::user();
        $departemenId = $user->dp_id ?? null;
        $fakultasId = $user->fk_id ?? null;

        $query = $this->dpQuery();

        $this->haveDpParent($query);

        if (! $departemenId) {
            $defaultDepartemens = $this->dpQuery()
                ->orderBy('nama_dp', 'asc')
                ->limit(12)
                ->get();

            return $type === 'search'
                ? $this->mapDpSearch($defaultDepartemens)
                : $this->mapDp($defaultDepartemens);
        }

        $mainResults = $query
            ->where('fk_id', $fakultasId)
            ->get()
            ->sortBy(fn ($j) => $j->id === $departemenId ? 0 : 1)
            ->take(12);

        if ($this->dpLevel <= 1 || empty($this->dpLevel)) {
            if ($mainResults->count() < 12) {
                $extra = $this->dpQuery()
                    ->whereHas('fk_rel', fn ($q) => $q->where('id', '!=', $fakultasId))
                    ->whereNotIn('id', $mainResults->pluck('id'))
                    ->limit(12 - $mainResults->count())
                    ->get();
                $mainResults = $mainResults->concat($extra);
            }
        }

        return $type === 'search'
            ? $this->mapDpSearch($mainResults)
            : $this->mapDp($mainResults);
    }

    public function fetchDp($mode = 'single')
    {
        // $this->modeDp = $mode;
        if ($this->dp_id) {
            $dp = Departemen::find($this->dp_id);
            if ($dp) {
                $this->dpNameSearch = $dp->departemen_dp;
                $this->dp_items = $this->itemsDp($dp);
            }
            $this->dpResults = $this->getDpbyUser();

            return;
        }
    }

    public function selectDp($id, $departemenName)
    {
        $this->dp_id = $id;
        $this->dpNameSearch = $departemenName;
        $this->dpResults = $this->getDpbyUser();

        $data = $this->dpQuery()->find($id);
        if ($data) {
            $this->dp_items = $this->itemsDp($data);
        }

        $this->haveDpChild();

        if (method_exists($this, 'fetchDp')) {
            $this->fetchDp('');
        }

        $this->resetErrorBag(['dp_id', 'dpNameSearch']);
    }

    public function resetDpInput()
    {
        $this->dp_id = null;
        $this->dp_items = null;
        $this->dpNameSearch = '';

        $this->haveDpChild();

        $this->updatedDpNameSearch('');
        $this->resetErrorBag(['dp_id', 'dpNameSearch']);
    }

    public function haveDpChild()
    {
        if (property_exists($this, 'prLevel') && $this->prLevel == 2) {
            if (method_exists($this, 'resetPrArray')) {
                $this->resetPrArray();
            }
        }
    }

    public function haveDpParent($query)
    {
        if ($this->dpLevel == 2 && filled($this->fk_id)) {
            $query->where('fk_id', $this->fk_id);
        }
        return $query;
    }


    // public function searchOutputDp($queryDp, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    // {
    //     $search = trim($searchRaw);
    //     $searchLower = strtolower($search);
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     if (! empty($search) || $sortField) {

    //         $allDp = (clone $queryDp)->get();

    //         if (! empty($search)) {

    //             $mode = $this->detectSearchMode($searchLower);

    //             $allDp = $allDp->filter(function ($dp) use ($searchLower, $mode) {
    //                 $number = preg_replace('/[^0-9.]/', '', $searchLower);
    //                 $isNumericSearch = is_numeric($number) && $number !== '';

    //                 $matchID = $this->matchID(
    //                     $dp->id,
    //                     $searchLower
    //                 );

    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | KODE RPS
    //                 |--------------------------------------------------------------------------
    //                 */
    //                 $matchKode = $this->matchKode(
    //                     $dp->kode,
    //                     $searchLower
    //                 );
    //                 $matchKodeFk = $this->matchKode(
    //                     $dp->kode_fk,
    //                     $searchLower
    //                 );

    //                 $baseDp = [
    //                     $dp->departemen,
    //                     $dp->departemen_dp,
    //                 ];

    //                 $matchDp = false;
    //                 foreach ($baseDp as $fak) {
    //                     $candidates = [
    //                         $fak.' '.$dp->kode_dp,
    //                         $fak.' ('.$dp->kode_dp.')',
    //                     ];
    //                     foreach ($candidates as $candidate) {
    //                         if ($this->containsStrict($candidate, $searchLower)) {
    //                             $matchDp = true;
    //                             break 2;
    //                         }
    //                     }
    //                 }

    //                 $baseFk = [
    //                     $dp->fakultas,
    //                     $dp->fakultas_fk,
    //                 ];
    //                 $matchFk = false;
    //                 foreach ($baseFk as $fak) {
    //                     $candidates = [
    //                         $fak.' '.$dp->kode_fk,
    //                         $fak.' ('.$dp->kode_fk.')',
    //                     ];
    //                     foreach ($candidates as $candidate) {
    //                         if ($this->containsStrict($candidate, $searchLower)) {
    //                             $matchFk = true;
    //                             break 2;
    //                         }
    //                     }
    //                 }

    //                 $matchCreatedAt = $this->matchDateField(
    //                     $dp->created_at,
    //                     $searchLower,
    //                     ['created', 'dibuat', 'create']
    //                 );

    //                 $matchUpdatedAt = $this->matchDateField(
    //                     $dp->updated_at,
    //                     $searchLower,
    //                     ['updated', 'diubah', 'update']
    //                 );

    //                 switch ($mode) {
    //                     case 'id':
    //                         return $matchID;
    //                 }

    //                 return
    //                     $matchID
    //                     || $matchKode
    //                     || $matchKodeFk

    //                     || $matchDp
    //                     || $matchFk

    //                     || $matchCreatedAt
    //                     || $matchUpdatedAt;
    //             });
    //         }

    //         $sortValue = match ($sortField) {
    //             'kode' => fn ($dp) => $dp->kode,

    //             'departemen' => fn ($dp) => $dp->departemen,
    //             'fakultas' => fn ($dp) => $dp->fakultas,

    //             'created_at' => fn ($dp) => $dp->created_at,
    //             'updated_at' => fn ($dp) => $dp->updated_at,

    //             default => fn ($dp) => $dp->id,
    //         };

    //         $allDp = $sortDirection === 'asc'
    //             ? $allDp->sortBy($sortValue)
    //             : $allDp->sortByDesc($sortValue);

    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;

    //         return new LengthAwarePaginator(
    //             $allDp->forPage($currentPage, $perPage)->values(),
    //             $allDp->count(),
    //             $perPage,
    //             $currentPage,
    //             ['path' => Paginator::resolveCurrentPath()]
    //         );
    //     }

    //     return $queryDp->paginate($perPage);
    // }
}
