<?php

namespace App\Livewire\Global;

use App\Models\Akademik\Referensi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithReferensiSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $refSearchQuery = '';

    public $refSearchResults = [];

    public $modeRef = 'single';

    public $ref_id;

    public $ref_name = '';

    public $ref_items = [];

    public $refNameSearch = '';

    public $refResults = [];

    public $selectedRefId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $ref_id_array = [];

    public $ref_items_array = [];

    private function mapRef($collection)
    {
        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'kode' => $r->kode,
            'citation' => $r->citation,
            'judul' => $r->judul,
            'penulis' => $r->penulis,
            'penulis_tahun' => $r->penulis_tahun,
            'penerbit' => $r->penerbit,
            'tahun' => $r->tahun,
            'link' => $r->link,
        ])->toArray();
    }

    private function refQuery()
    {
        return Referensi::query()->with('rps', 'cpmks.rps', 'scpmks.cpmks.rps',
            'cpmks', 'scpmks.cpmks',
            'scpmks');
    }

    private function itemsRef($r)
    {
        if (! $r) {
            return null;
        }

        return [
            'id' => $r->id,
            'kode' => $r->kode,
            'slot1' => $r->judul,
            'slot2' => $r->penulis_tahun,
            'slot3' => $r->penerbit,
            'link' => $r->link,
        ];
    }


    public function inputRefFilter()
    {
        $search = trim($this->refSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->ref_name)) {
            $this->refSearchResults = $this->mapRef(
                $this->refQuery()->searchRef($search)->limit(12)->get()
                // $this->searchOutputRef($this->refQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->ref_name) {
            $this->refSearchResults = $this->getRefbyUser();
        } else {
            $this->refSearchResults = [];
        }
    }

    public function resetRefFilter()
    {
        $this->reset(['selectedRefId', 'refSearchQuery', 'ref_name', 'ref_items']);
        $this->resetPage();
    }

    public function selectRefForFilter($id)
    {
        $data = $this->refQuery()->find($id);

        if ($data) {
            $this->selectedRefId = $id;
            $this->ref_name = $data->sitasi;
            $this->refSearchQuery = $data->judul;
            $this->ref_items = $this->itemsRef($data);
            $this->refSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRefNameSearch($value)
    {
        $this->ref_id = null;
        $this->ref_items = null;
        $this->resetErrorBag(['ref_id', 'refNameSearch']);

        $query = $this->refQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchRef($value)->limit(12)->get();
            // $results = $this->searchOutputRef($query, $value, null, 12);
            $this->refResults = $this->mapRef($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($r) use ($value, $normalizedValue) {
                $normalizedRefKode = str_replace(['-', ' '], '', strtolower($r->kode));

                return strtolower($r->citation) === strtolower($value)
                    || strtolower($r->judul) === strtolower($value)
                    || $normalizedRefKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->ref_id = $exactMatch->id;
                $this->ref_items = $this->itemsRef($exactMatch);
                $this->refNameSearch = $exactMatch->citation;
                $this->refResults = [];
            }
            if ($exactMatch) {
                if ($this->modeRef == 'single') {
                    $this->refNameSearch = $exactMatch->citation;
                    $this->ref_id = $exactMatch->id;
                    $this->ref_items = $this->itemsRef($exactMatch);
                    $this->refResults = [];
                } else {
                    $this->refNameSearch = '';
                    $this->ref_id_array[] = $exactMatch->id;
                    $this->ref_items_array[] = $this->itemsRef($exactMatch);
                    $this->ref_id_array = collect($this->ref_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->ref_items_array = collect($this->ref_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapRef(collect([$exactMatch]));
                $this->pushToRefItems($mappedResults);
                $this->refResults = $this->getRefbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->refResults = $this->getRefbyUser();
            } else {
                $this->refResults = $this->mapRef(
                    $query->orderBy('referensis.judul')->limit(12)->get()
                );
            }
        }
    }

    public function getRefbyUser()
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->refQuery();

        if (! $prodiId) {
            $defaultRef = $query
                ->latest()
                ->limit(12)
                ->get();

            return $this->mapRef($defaultRef);
        }

        $mainResults = $query->where(function ($q) use ($prodiId) {
            $q->whereRelation('scpmks.cpmks.rps.mk_rel.prodis', 'prodis.id', $prodiId)
                ->orWhereRelation('cpmks.rps.mk_rel.prodis', 'prodis.id', $prodiId)
                ->orWhereRelation('rps.mk_rel.prodis', 'prodis.id', $prodiId);
        })->limit(12)->get();

        if ($mainResults->count() < 12) {
            $extra = $this->refQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $this->mapRef($mainResults);
    }

    public function fetchRef($mode = 'single')
    {
        $this->modeRef = $mode;
        if ($this->ref_id) {
            $ref = Referensi::find($this->ref_id);
            if ($ref) {
                $this->refNameSearch = $ref->citation;
                $this->ref_items = $this->itemsRef($ref);
            }
            $this->refResults = $this->getRefbyUser();
            return;
        }
    }

    public function selectRef($id, $refName)
    {
        $this->ref_id = $id;
        $this->refNameSearch = $refName;
        $this->refResults = $this->getRefbyUser();

        $data = $this->refQuery()->find($id);
        if ($data) {
            $this->ref_items = $this->itemsRef($data);
            $mappedResults = $this->mapRef(collect([$data]));
            $this->pushToRefItems($mappedResults);
        }

        if (method_exists($this, 'fetchRef')) {
            $this->fetchRef();
        }

        $this->resetErrorBag(['ref_id', 'refNameSearch']);
    }

    public function selectRefArray($id)
    {
        $data = $this->refQuery()->find($id);
        if ($data && ! in_array($id, $this->ref_id_array)) {
            $this->ref_id_array[] = $id;
            $this->ref_items_array[] = $this->itemsRef($data);

            $mappedResults = $this->mapRef(collect([$data]));
            $this->pushToRefItems($mappedResults);
        }
    }

    public function resetRefInput()
    {
        $this->reset(['ref_id', 'ref_items', 'refNameSearch']);
        $this->refResults = $this->getRefbyUser();
    }

    public function resetRefArray()
    {
        $this->ref_id_array = [];
        $this->ref_items_array = [];
        $this->refNameSearch = '';
    }

    public function searchOutputRef($queryRef, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allRef = (clone $queryRef)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allRef = $allRef->filter(function ($ref) use ($searchLower, $mode) {
                    // $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    // $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $ref->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE CPMK
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $ref->kode,
                        $searchLower
                    );

                    $matchSitasi = $this->containsStrict(
                        $ref->citation,
                        $searchLower
                    );

                    $matchJudul = $this->containsStrict(
                        $ref->judul,
                        $searchLower
                    );

                    $matchPenulis = $this->containsStrict(
                        $ref->penulis,
                        $searchLower
                    );
                    $matchPenerbit = $this->containsStrict(
                        $ref->penerbit,
                        $searchLower
                    );

                    $matchTahun = $this->matchCount(
                        $ref->tahun,
                        $searchLower,
                        [
                            'tahun', 'thn', 'th', 'year', 'yr',
                        ]
                    );

                    $matchLink = $this->containsStrict(
                        $ref->link,
                        $searchLower
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $ref->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $ref->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchSitasi
                        || $matchJudul

                        || $matchPenulis
                        || $matchPenerbit
                        || $matchTahun
                        || $matchLink

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($ref) => $ref->kode,
                'citation' => fn ($ref) => $ref->citation,
                'judul' => fn ($ref) => $ref->judul,
                'penulis' => fn ($ref) => $ref->penulis,
                'penerbit' => fn ($ref) => $ref->penerbit,
                'tahun' => fn ($ref) => $ref->tahun,
                'link' => fn ($ref) => $ref->link,

                'created_at' => fn ($ref) => $ref->created_at,
                'updated_at' => fn ($ref) => $ref->updated_at,

                default => fn ($ref) => $ref->id,
            };

            $allRef = $sortDirection === 'asc'
                ? $allRef->sortBy($sortValue)
                : $allRef->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allRef->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allRef->forPage($currentPage, $perPage)->values(),
                $allRef->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryRef;
        }

        return $queryRef->paginate($perPage);
    }
}
