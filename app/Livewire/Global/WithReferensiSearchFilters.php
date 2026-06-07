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

    public $modeRef = [];

    public $ref_id = [];

    public $ref_name = [];

    public $ref_items = [];

    public $refNameSearch = [];

    public $refResults = [];

    public $selectedRefId = [];

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $ref_id_array = [];

    public $ref_items_array = [];

    private function mapRef($collection)
    {
        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'kode' => $r->kode,
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

    public function getRefIdArrayForKey(string $key = 'default'): array
    {
        if (is_array($this->ref_id_array) && array_key_exists($key, $this->ref_id_array) && is_array($this->ref_id_array[$key])) {
            return $this->ref_id_array[$key];
        }

        return [];
    }

    public function getRefNameSearchForKey(string $key = 'default'): string
    {
        if (is_array($this->refNameSearch) && array_key_exists($key, $this->refNameSearch)) {
            return is_string($this->refNameSearch[$key]) ? $this->refNameSearch[$key] : '';
        }

        return '';
    }

    public function inputRefFilter()
    {
        $search = trim($this->refSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->ref_name)) {
            $this->refSearchResults = $this->mapRef(
                // $this->refQuery()->searchRef($search)->limit(12)->get()
                $this->searchOutputRef($this->refQuery(), $search, 12)
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
            $this->ref_name = $data->judul;
            $this->refSearchQuery = $data->judul;
            $this->ref_items = $this->itemsRef($data);
            $this->refSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRefNameSearch($value, $name = null)
    {
        $key = 'default';

        if (is_string($name) && str_contains($name, '.')) {
            [, $key] = explode('.', $name, 2);
        } elseif (is_string($name) && $name !== 'refNameSearch') {
            $key = $name;
        }

        if (is_array($value)) {
            $value = $value[$key] ?? '';
        }

        $this->ref_id[$key] = null;
        $this->ref_items[$key] = null;
        $this->resetErrorBag(['ref_id.'.$key, 'refNameSearch.'.$key]);

        $query = $this->refQuery();

        if (trim(strlen((string) $value)) > 0) {
            // $results = $query->searchRef($value)->limit(12)->get();
            $results = $this->searchOutputRef($query, $value, 12);
            $this->refResults[$key] = $this->mapRef($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($ref) use ($value, $normalizedValue) {
                $normalizedRefKode = str_replace(['-', ' '], '', strtolower($ref->kode));

                return strtolower($ref->judul) === strtolower($value)
                    || $normalizedRefKode === $normalizedValue;
            });

            if ($exactMatch) {
                $currentMode = $this->modeRef[$key] ?? 'array';
                if ($currentMode == 'single') {
                    $this->refNameSearch[$key] = $exactMatch->judul;
                    $this->ref_id[$key] = $exactMatch->id;
                    $this->ref_items[$key] = $this->itemsRef($exactMatch);
                    $this->refResults[$key] = [];
                } else {
                    $this->refNameSearch[$key] = '';
                    if (! isset($this->ref_id_array[$key])) {
                        $this->ref_id_array[$key] = [];
                    }
                    if (! isset($this->ref_items_array[$key])) {
                        $this->ref_items_array[$key] = [];
                    }
                    if (! in_array($exactMatch->id, $this->ref_id_array[$key])) {
                        $this->ref_id_array[$key][] = $exactMatch->id;
                        $this->ref_items_array[$key][] = $this->itemsRef($exactMatch);
                    }
                }
                $this->refResults[$key] = $this->getRefbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->refResults[$key] = $this->getRefbyUser();
            } else {
                $this->refResults[$key] = $this->mapRef(
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

    public function fetchRef($query = '', $mode = 'single', $key = 'default')
    {
        $this->modeRef[$key] = $mode;
        if (empty($query) || (! empty($this->ref_id[$key]) || ! empty($this->ref_id_array[$key]))) {
            $this->refResults[$key] = $this->getRefbyUser();
        }

    }

    public function selectRef($id, $refName, $key = 'default')
    {
        $this->ref_id[$key] = $id;
        $this->refNameSearch[$key] = $refName;
        $this->refResults[$key] = $this->getRefbyUser();

        $data = $this->refQuery()->find($id);
        if ($data) {
            $this->ref_items[$key] = $this->itemsRef($data);
        }

        if (method_exists($this, 'fetchRef')) {
            $this->fetchRef('', $this->modeRef[$key] ?? 'single', $key);
        }

        $this->resetErrorBag(['ref_id.'.$key, 'refNameSearch.'.$key]);
    }

    public function selectRefArray($id, $key = 'default')
    {
        $data = $this->refQuery()->find($id);
        if ($data) {
            if (! isset($this->ref_id_array[$key])) {
                $this->ref_id_array[$key] = [];
            }
            if (! in_array($id, $this->ref_id_array[$key])) {
                $this->ref_id_array[$key][] = $id;
                $this->ref_items_array[$key][] = $this->itemsRef($data);
            }
        }
    }

    public function resetRefInput($key = 'default')
    {
        $this->reset(['ref_id', 'ref_items', 'refNameSearch']);
        $this->refResults[$key] = $this->getRefbyUser();
    }

    public function resetRefArray($key = 'default')
    {
        $this->ref_id_array[$key] = [];
        $this->ref_items_array[$key] = [];
        $this->refNameSearch[$key] = '';
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
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

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

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allRef->forPage($currentPage, $perPage)->values(),
                $allRef->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return $queryRef->paginate($perPage);
    }
}
