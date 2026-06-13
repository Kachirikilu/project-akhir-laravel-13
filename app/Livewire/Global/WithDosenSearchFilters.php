<?php

namespace App\Livewire\Global;

use App\Models\Auth\Dosen;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithDosenSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $dosenSearchQuery = '';

    public $dosenSearchResults = [];

    public $modeDosen = '';

    public $dosen_id;

    public $dosen_name = '';

    public $dosen_items;

    public $dosenNameSearch = '';

    public $dosenResults = [];

    public $selectedDosenId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $dosen_id_array = [];

    public $dosen_items_array = [];

    // Properti Dosen Pengajar
    // public $is_ketua_dosen = ''; // ID Dosen yang sebagai ketua
    public $peran_dosen = [];

    public $pertemuan_dosen = [];

    private function mapDosen($collection)
    {
        return $collection->map(fn ($d) => [
            'id' => $d->id,
            'kode' => $d->nip,
            'nidn' => $d->nidn ?? null,
            'nidk' => $d->nidk ?? null,
            'name' => $d->name,
            'prodi' => $d->pr_rel?->prodi,
            'fakultas' => $d->pr_rel?->fakultasFk,
            'status' => $d->status,
        ])->toArray();
    }

    private function mapDosenSearch($collection)
    {
        return $collection->map(fn ($d) => [
            'id' => $d->id,
            'kode' => $d->nip,
            'nip_full' => 'NIP: '.$d->nip,
            'name' => $d->name,
            'prodi' => $d->pr_rel?->prodi,
            'departemen' => $d->pr_rel?->departemenDp,
            'fakultas' => $d->pr_rel?->fakultasFk,
            'kode_pr' => $d->pr_rel?->kode,
            'status' => $d->status,
        ])->toArray();
    }

    private function dosenQuery()
    {
        return Dosen::query()->with('user');
    }

    private function itemsDosen($d)
    {
        if (! $d) {
            return null;
        }

        return [
            'id' => $d->id,
            'kode' => $d->nip,
            'slot1' => $d->name,
            'slot2' => $d->nidn,
            'slot3' => $d->nidk,
            'slot4' => $d->status,
            'slot5' => $d->prodi,
            'peran' => $d->pivot->peran ?? 'Pengajar',
            'is_ketua' => (bool) ($d->pivot->is_ketua ?? false),
        ];
    }

    public function inputDosenFilter()
    {
        $search = trim($this->dosenSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->dosen_name)) {
            $this->dosenSearchResults = $this->mapDosenSearch(
                $this->dosenQuery()->searchDosen($search)->limit(12)->get()
                // $this->searchOutputDosen($this->dosenQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->dosen_name) {
            $this->dosenSearchResults = $this->getDosenbyUser('search');
        } else {
            $this->dosenSearchResults = [];
        }
    }

    public function resetDosenFilter()
    {
        $this->reset(['selectedDosenId', 'dosenSearchQuery', 'dosen_name', 'dosen_items']);
        $this->resetPage();
    }

    public function selectDosenForFilter($id)
    {
        $data = $this->dosenQuery()->find($id);

        if ($data) {
            $this->selectedDosenId = $id;
            $this->dosen_name = $data->name;
            $this->dosenSearchQuery = $data->name;
            $this->dosen_items = $this->itemsDosen($data);
            $this->dosenSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedDosenNameSearch($value)
    {
        $this->dosen_id = null;
        $this->dosen_items = null;
        $this->resetErrorBag(['dosen_id', 'dosenNameSearch']);

        $query = $this->dosenQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchDosen($value)->limit(12)->get();
            // $results = $this->searchOutputDosen($query, $value, 12);
            $this->dosenResults = $this->mapDosen($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($d) use ($value, $normalizedValue) {
                $normalizedDosenNIP = str_replace(['-', ' '], '', strtolower($d->nip));
                $normalizedDosenNIDN = str_replace(['-', ' '], '', strtolower($d->nidn));
                $normalizedDosenNIDK = str_replace(['-', ' '], '', strtolower($d->nidk));
                $normalizedDosenNIK = str_replace(['-', ' '], '', strtolower($d->nik));

                return strtolower($d->name) === strtolower($value)
                    || strtolower($d->user->email) === strtolower($value)
                    || $normalizedDosenNIP === $normalizedValue
                    || $normalizedDosenNIDN === $normalizedValue
                    || $normalizedDosenNIDK === $normalizedValue
                    || $normalizedDosenNIK === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeDosen == 'single') {
                    $this->dosenNameSearch = $exactMatch->name;
                    $this->dosen_id = $exactMatch->id;
                    $this->dosen_items = $this->itemsDosen($exactMatch);
                } else {
                    $this->dosenNameSearch = '';
                    $this->dosen_id_array[] = $exactMatch->id;
                    $this->dosen_items_array[] = $this->itemsDosen($exactMatch);

                    $this->dosen_id_array = collect($this->dosen_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->dosen_items_array = collect($this->dosen_items_array)
                        ->unique('id')
                        ->values()
                        ->all();

                    $isKetua = collect($this->dosen_items_array)
                        ->contains(fn ($item) => $item['is_ketua'] === true);
                    if (! $isKetua && count($this->dosen_items_array) > 0) {
                        $lastIndex = array_key_last($this->dosen_items_array);
                        $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
                        $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
                    }
                }
                $this->dosenResults = $this->getDosenbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->dosenResults = $this->getDosenbyUser();
            } else {
                $this->dosenResults = $this->mapDosen(
                    $query->orderBy('dosens.name')->limit(12)->get()
                );
            }
        }
    }

    public function getDosenbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->dosenQuery();

        if (! $prodiId) {
            $defaultDosen = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapDosenSearch($defaultDosen)
                : $this->mapDosen($defaultDosen);
        }

        $mainResults = $query
            ->whereHas('pr_rel', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = Dosen::whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('name', 'asc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapDosenSearch($mainResults)
            : $this->mapDosen($mainResults);
    }

    public function fetchDosen($query = '', $mode = 'single')
    {
        $this->modeDosen = $mode;
        if (empty($query) || $this->dosen_id) {
            $this->dosenResults = $this->getDosenbyUser();
        }

    }

    public function selectDosen($id, $dosenName)
    {
        $this->dosen_id = $id;
        $this->dosenNameSearch = $dosenName;
        $this->dosenResults = $this->getDosenbyUser();

        $data = $this->dosenQuery()->find($id);
        if ($data) {
            $this->dosen_items = $this->itemsDosen($data);
        }

        if (method_exists($this, 'fetchDosen')) {
            $this->fetchDosen('');
        }

        $this->resetErrorBag(['dosen_id', 'dosenNameSearch']);
    }

    public function selectDosenArray($id)
    {
        $data = $this->dosenQuery()->find($id);
        if ($data && ! in_array($id, $this->dosen_id_array)) {
            $this->dosen_id_array[] = $id;
            $this->dosen_items_array[] = $this->itemsDosen($data);
        }
    }

    public function resetDosenInput()
    {
        $this->reset(['dosen_id', 'dosen_items', 'dosenNameSearch']);
        $this->dosenResults = $this->getDosenbyUser();
    }

    public function resetDosenArray()
    {
        $this->dosen_id_array = [];
        $this->dosen_items_array = [];
        $this->dosenNameSearch = '';
    }

    public function searchOutputDosen($queryDosen, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allDosen = (clone $queryDosen)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allDosen = $allDosen->filter(function ($dosen) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $dosen->id,
                        $searchLower
                    );

                    $matchName = $this->containsStrict(
                        $dosen->name,
                        $searchLower
                    );
                    $matchEmail = $this->containsStrict(
                        $dosen->user->email,
                        $searchLower
                    );
                    $matchStatus = $this->containsStrict(
                        $dosen->status,
                        $searchLower
                    );

                    $matchNIP = $this->matchOnlyCount(
                        $dosen->nip ?? null,
                        $searchLower, ['nip', 'id1', 'identity1']
                    ) || $this->containsStrict(
                        $dosen->nip,
                        $searchLower
                    );
                    $matchNIDN = $this->matchOnlyCount(
                        $dosen->nidn ?? null,
                        $searchLower, ['nidn', 'id2', 'identity2']
                    ) || $this->containsStrict(
                        $dosen->nidn,
                        $searchLower
                    );
                    $matchNIDK = $this->matchOnlyCount(
                        $dosen->nidk ?? null,
                        $searchLower, ['nidk', 'id3', 'identity3']
                    ) || $this->containsStrict(
                        $dosen->nidk,
                        $searchLower
                    );
                    $matchNIK = $this->matchOnlyCount(
                        $dosen->nik,
                        $searchLower, ['nik']
                    ) || $this->containsStrict(
                        $dosen->nik,
                        $searchLower
                    );

                    $matchKodePr = $this->matchKode(
                        $dosen->pr_rel->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $dosen->pr_rel->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $dosen->pr_rel->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $dosen->pr_rel->prodi,
                        $dosen->pr_rel->prodi_pr,
                        $dosen->pr_rel->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$dosen->pr_rel->kode_dp,
                            $pr.' ('.$dosen->pr_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $dosen->pr_rel->departemen,
                        $dosen->pr_rel->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$dosen->pr_rel->kode_dp,
                            $dp.' ('.$dosen->pr_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $dosen->pr_rel->fakultas,
                        $dosen->pr_rel->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$dosen->pr_rel->kode_fk,
                            $fk.' ('.$dosen->pr_rel->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchCreatedAt = $this->matchDateField(
                        $dosen->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $dosen->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                    }

                    return
                        $matchID
                        || $matchName
                        || $matchEmail
                        || $matchStatus

                        || $matchNIP
                        || $matchNIDN
                        || $matchNIDK
                        || $matchNIK

                        || $matchKodePr
                        || $matchKodeDp
                        || $matchKodeFk

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'name' => fn ($dosen) => $dosen->name,
                'email' => fn ($dosen) => $dosen->email,

                'nip' => fn ($dosen) => $dosen->nip ?? null,
                'nidn' => fn ($dosen) => $dosen->dosen->nidn ?? null,
                'nidk' => fn ($dosen) => $dosen->dosen->nidk ?? null,
                'nik' => fn ($dosen) => $dosen->nik ?? null,

                'status' => fn ($dosen) => $dosen->status ?? null,
                'prodi', 'program_studi' => fn ($dosen) => $dosen->pr_rel->prodi ?? null,

                'created_at' => fn ($dosen) => $dosen->created_at,
                'updated_at' => fn ($dosen) => $dosen->updated_at,

                default => fn ($dosen) => $dosen->id,
            };

            $allDosen = $sortDirection === 'asc'
                ? $allDosen->sortBy($sortValue)
                : $allDosen->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allDosen->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allDosen->forPage($currentPage, $perPage)->values(),
                $allDosen->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryDosen;
        }

        return $queryDosen->paginate($perPage);
    }
}
