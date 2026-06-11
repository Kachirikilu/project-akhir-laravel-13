<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPL;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithCPLSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $cplSearchQuery = '';

    public $cplSearchResults = [];

    public $modeCPL = '';

    public $cpl_id;

    public $cpl_name = '';

    public $cpl_items;

    public $cplNameSearch = '';

    public $cplResults = [];

    public $selectedCPLId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $cpl_id_array = [];

    public $cpl_items_array = [];



    private function mapCPL($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'deskripsi' => $c->deskripsi,
        ])->toArray();
    }

    private function mapCPLSearch($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'kode_text' => 'Kode: '.$c->kode,
            'deskripsi' => $c->deskripsi,
        ])->toArray();
    }

    private function cplQuery()
    {
        return CPL::query()->with('cpmks.rps', 'cpmks', 'prodis');
    }

    private function itemsCPL($c)
    {
        if (! $c) {
            return null;
        }

        return [
            'id' => $c->id,
            'kode' => $c->kode,
            'slot1' => $c->deskripsi,
        ];
    }

    // public function getCPLIdArrayForKey(string $key = 'default'): array
    // {
    //     if (is_array($this->cpl_id_array) && array_key_exists($key, $this->cpl_id_array) && is_array($this->cpl_id_array[$key])) {
    //         return $this->cpl_id_array[$key];
    //     }

    //     return [];
    // }

    // public function getCPLNameSearchForKey(string $key = 'default'): string
    // {
    //     if (is_array($this->cplNameSearch) && array_key_exists($key, $this->cplNameSearch)) {
    //         return is_string($this->cplNameSearch[$key]) ? $this->cplNameSearch[$key] : '';
    //     }

    //     return '';
    // }

    public function inputCPLFilter()
    {
        $search = trim($this->cplSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->cpl_name)) {
            $this->cplSearchResults = $this->mapCPLSearch(
                $this->cplQuery()->searchCPL($search)->limit(12)->get()
                // $this->searchOutputCPL($this->cplQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->cpl_name) {
            $this->cplSearchResults = $this->getCPLbyUser('search');
        } else {
            $this->cplSearchResults = [];
        }
    }

    public function resetCPLFilter()
    {
        $this->reset(['selectedCPLId', 'cplSearchQuery', 'cpl_name', 'cpl_items']);
        $this->resetPage();
    }

    public function selectCPLForFilter($id)
    {
        $data = $this->cplQuery()->find($id);

        if ($data) {
            $this->selectedCPLId = $id;
            $this->cpl_name = $data->deskripsi;
            $this->cplSearchQuery = $data->deskripsi;
            $this->cpl_items = $this->itemsCPL($data);
            $this->cplSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedCPLNameSearch($value)
    {
        $this->cpl_id = null;
        $this->cpl_items = null;
        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);

        $query = $this->cplQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchCPL($value)->limit(12)->get();
            // $results = $this->searchOutputCPL($query, $value, null, 12);
            $this->cplResults = $this->mapCPL($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($value, $normalizedValue) {
                $normalizedCPLKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return strtolower($c->deskripsi) === strtolower($value)
                    || $normalizedCPLKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->cpl_id = $exactMatch->id;
                $this->cpl_items = $this->itemsCPL($exactMatch);
                $this->cplNameSearch = $exactMatch->deskripsi;
                $this->cplResults = [];
            }
            if ($exactMatch) {
                if ($this->modeCPL == 'single') {
                    $this->cplNameSearch = $exactMatch->deskripsi;
                    $this->cpl_id = $exactMatch->id;
                    $this->cpl_items = $this->itemsCPL($exactMatch);
                    $this->cplResults = [];
                } else {
                    $this->cplNameSearch = '';
                    $this->cpl_id_array[] = $exactMatch->id;
                    $this->cpl_items_array[] = $this->itemsCPL($exactMatch);
                    $this->cpl_id_array = collect($this->cpl_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->cpl_items_array = collect($this->cpl_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapCPL(collect([$exactMatch]));
                $this->pushToCPLItems($mappedResults);
                $this->cplResults = $this->getCPLbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->cplResults = $this->getCPLbyUser();
            } else {
                $this->cplResults = $this->mapCPL(
                    $query->orderBy('cpls.deskripsi', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getCPLbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->cplQuery();

        if (! $prodiId) {
            $defaultCPL = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapCPLSearch($defaultCPL)
                : $this->mapCPL($defaultCPL);
        }

        $mainResults = $query
            ->whereHas('prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->cplQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapCPLSearch($mainResults)
            : $this->mapCPL($mainResults);
    }

    public function fetchCPL($query = '', $mode = 'single')
    {
        $this->modeCPL = $mode;
        if (empty($query) || (! empty($this->cpl_id) || ! empty($this->cpl_id_array))) {
            $this->cplResults = $this->getCPLbyUser();
        }

    }

    public function selectCPL($id, $cplName)
    {
        $this->cpl_id = $id;
        $this->cplNameSearch = $cplName;
        $this->cplResults = $this->getCPLbyUser();

        $data = $this->cplQuery()->find($id);
        if ($data) {
            $this->cpl_items = $this->itemsCPL($data);
            $mappedResults = $this->mapCPL(collect([$data]));
            $this->pushToCPLItems($mappedResults);
        }

        if (method_exists($this, 'fetchCPL')) {
            $this->fetchCPL('');
        }

        $this->resetErrorBag(['cpl_id', 'cplNameSearch']);
    }

    public function selectCPLArray($id)
    {
        $data = $this->cplQuery()->find($id);
        if ($data && ! in_array($id, $this->cpl_id_array)) {
            $this->cpl_id_array[] = $id;
            $this->cpl_items_array[] = $this->itemsCPL($data);

            $mappedResults = $this->mapCPL(collect([$data]));
            $this->pushToCPLItems($mappedResults);
        }
    }

    public function resetCPLInput()
    {
        $this->reset(['cpl_id', 'cpl_items', 'cplNameSearch']);
        $this->cplResults = $this->getCPLbyUser();
    }

    public function resetCPLArray()
    {
        $this->cpl_id_array = [];
        $this->cpl_items_array = [];
        $this->cplNameSearch = '';
    }

    public function searchOutputCPL($queryCPL, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allCPL = (clone $queryCPL)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allCPL = $allCPL->filter(function ($cpl) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $cpl->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE CPL
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $cpl->kode,
                        $searchLower
                    );

                    $matchDes = $this->containsStrict(
                        $cpl->deskripsi,
                        $searchLower
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $cpl->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $cpl->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'bobot':
                            return $matchBobot;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchDes

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($cpl) => $cpl->kode,
                'deskripsi' => fn ($cpl) => $cpl->deskripsi,

                'created_at' => fn ($cpl) => $cpl->created_at,
                'updated_at' => fn ($cpl) => $cpl->updated_at,

                default => fn ($cpl) => $cpl->id,
            };

            $allCPL = $sortDirection === 'asc'
                ? $allCPL->sortBy($sortValue)
                : $allCPL->sortByDesc($sortValue);

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allCPL->forPage($currentPage, $perPage)->values(),
                $allCPL->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return $queryCPL->paginate($perPage);
    }
}
