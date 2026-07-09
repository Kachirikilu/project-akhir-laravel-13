<?php

namespace App\Livewire\Global;

use App\Models\Akademik\TimDosen;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithTimDosenSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $timDosenSearchQuery = '';

    public $timDosenSearchResults = [];

    public $modeTimDosen = '';

    public $timDosen_id;

    public $tim_dosen_name = '';

    public $tim_dosen_items = [];

    public $timDosenNameSearch = '';

    public $timDosenResults = [];

    public $selectedTimDosenId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $tim_dosen_id_array = [];

    public $tim_dosen_items_array = [];

    public $tim_dosen_sub_items_array = [];

    private function mapTimDosen($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'tim' => $c->tim,
            'ketua' => $c->ketua,
            'peran' => $c->peran,
            'anggota' => $c->anggota,
            'prodi' => $c->prodi,
            'dosen' => $this->mapDosen($c->dosens),
            'pr_id' => $c->pr_id,
        ])->toArray();
    }

    private function mapTimDosenSearch($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'tim' => $c->tim,
            'ketua' => $c->ketua,
            'peran' => $c->peran,
            'anggota' => $c->anggota,
            'prodi' => $c->prodi,
            'pr_id' => $c->pr_id,
        ])->toArray();
    }

    private function timDosenQuery()
    {
        return TimDosen::query()->with('rps', 'dosens', 'pr_rel');
    }

    private function itemsTimDosen($c, ?string $customKode = null)
    {
        if (! $c) {
            return null;
        }

        return [
            'id' => $c->id,
            'kode' => $customKode ?: $c->kode,
            'slot1' => $c->tim,
            'slot2' => $c->ketua,
            'slot3' => $c->prodi,
            'slot4' => $c->anggota,
            'validation' => $c->pr_id,
        ];
    }

    private function pushToTimDosenItems($mappedResults)
    {
        $mappedData = $mappedResults[0] ?? null;

        if ($mappedData) {
            $this->tim_dosen_sub_items_array[] = [
                'dosen' => $mappedData['dosen'] ?? [],
            ];
        }
    }

 
    // public function inputTimDosenFilter()
    // {
    //     $search = trim($this->timDosenSearchQuery);

    //     // Jika ada input search
    //     if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->tim_dosen_name)) {
    //         $this->timDosenSearchResults = $this->mapTimDosenSearch(
    //             $this->timDosenQuery()->searchTimDosen($search)->limit(12)->get()
    //         );
    //     } elseif (empty($search) || $this->tim_dosen_name) {
    //         $this->timDosenSearchResults = $this->getTimDosenbyUser('search');
    //     } else {
    //         $this->timDosenSearchResults = [];
    //     }
    // }

    public function inputTimDosenFilter()
    {
        $search = trim($this->timDosenSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->tim_dosen_name)) {
            $this->timDosenSearchResults = $this->mapTimDosenSearch(
                $this->timDosenQuery()->searchTimDosen($search)->limit(12)->get()
            );
        } elseif (empty($search) || $this->tim_dosen_name) {
            $this->timDosenSearchResults = $this->getTimDosenbyUser('search');
        } else {
            $this->timDosenSearchResults = [];
        }
    }

    public function resetTimDosenFilter()
    {
        $this->reset(['selectedTimDosenId', 'timDosenSearchQuery', 'tim_dosen_name', 'tim_dosen_items']);
        $this->resetPage();
    }

    public function selectTimDosenForFilter($id)
    {
        $data = $this->timDosenQuery()->find($id);

        if ($data) {
            $this->selectedTimDosenId = $id;
            $this->tim_dosen_name = $data->tim;
            $this->timDosenSearchQuery = $data->tim;
            $this->tim_dosen_items = $this->itemsTimDosen($data);
            $this->timDosenSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedTimDosenNameSearch($value)
    {
        $this->timDosen_id = null;
        $this->tim_dosen_items = null;
        $this->resetErrorBag(['timDosen_id', 'timDosenNameSearch']);

        $query = $this->timDosenQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchTimDosen($value)->limit(12)->get();
            // $results = $this->searchOutputTimDosen($query, $value, null, 12);
            $this->timDosenResults = $this->mapTimDosen($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($value, $normalizedValue) {
                $normalizedTimDosenKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return strtolower($c->tim) === strtolower($value)
                    || $normalizedTimDosenKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->timDosen_id = $exactMatch->id;
                $this->tim_dosen_items = $this->itemsTimDosen($exactMatch);
                $this->timDosenNameSearch = $exactMatch->tim;
                $this->timDosenResults = [];
            }
            if ($exactMatch) {
                if ($this->modeTimDosen == 'single') {
                    $this->timDosenNameSearch = $exactMatch->tim;
                    $this->timDosen_id = $exactMatch->id;
                    $this->tim_dosen_items = $this->itemsTimDosen($exactMatch);
                    $mappedResults = $this->mapTimDosen(collect([$exactMatch]));
                    $mappedData = $mappedResults[0] ?? null;
                    if ($mappedData) {
                        $this->tim_dosen_sub_items_array[] = [
                            'dosen' => $mappedData['dosen'] ?? [],
                        ];
                    }
                    $this->timDosenResults = [];
                } else {
                    $this->timDosenNameSearch = '';
                    $this->tim_dosen_id_array[] = $exactMatch->id;
                    $this->tim_dosen_items_array[] = $this->itemsTimDosen($exactMatch);
                    $this->tim_dosen_id_array = collect($this->tim_dosen_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->tim_dosen_items_array = collect($this->tim_dosen_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapTimDosen(collect([$exactMatch]));
                $this->pushToTimDosenItems($mappedResults);
                $this->timDosenResults = $this->getTimDosenbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->timDosenResults = $this->getTimDosenbyUser();
            } else {
                $this->timDosenResults = $this->mapTimDosen(
                    $query->orderBy('tim_dosens.nama_tim', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getTimDosenbyUser($mode = 'complex')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->timDosenQuery();

        if (! $prodiId) {
            $defaultTimDosen = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapTimDosenSearch($defaultTimDosen)
                : $this->mapTimDosen($defaultTimDosen);
        }

        $mainResults = $query
            ->whereHas('pr_rel', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->timDosenQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapTimDosenSearch($mainResults)
            : $this->mapTimDosen($mainResults);
    }

    public function fetchTimDosen($query = '', $mode = 'single')
    {
        $this->modeTimDosen = $mode;
        if (empty($query) || (! empty($this->timDosen_id) || ! empty($this->tim_dosen_id_array))) {
            $this->timDosenResults = $this->getTimDosenbyUser();
        }

    }

    public function selectTimDosen($id, $timDosenName)
    {
        $this->timDosen_id = $id;
        $this->timDosenNameSearch = $timDosenName;
        $this->timDosenResults = $this->getTimDosenbyUser();

        $data = $this->timDosenQuery()->find($id);
        if ($data) {
            $this->tim_dosen_items = $this->itemsTimDosen($data);
            $mappedResults = $this->mapTimDosen(collect([$data]));
            $this->pushToTimDosenItems($mappedResults);
        }

        if (method_exists($this, 'fetchTimDosen')) {
            $this->fetchTimDosen('');
        }

        $this->resetErrorBag(['timDosen_id', 'timDosenNameSearch']);
    }

    public function selectTimDosenArray($id)
    {
        $data = $this->timDosenQuery()->find($id);
        if ($data && ! in_array($id, $this->tim_dosen_id_array)) {
            $this->tim_dosen_id_array[] = $id;
            $this->tim_dosen_items_array[] = $this->itemsTimDosen($data);

            $mappedResults = $this->mapTimDosen(collect([$data]));
            $this->pushToTimDosenItems($mappedResults);
        }
    }

    public function resetTimDosenInput()
    {
        $this->reset(['timDosen_id', 'tim_dosen_items', 'timDosenNameSearch']);
        $this->timDosenResults = $this->getTimDosenbyUser();
    }

    public function resetTimDosenArray()
    {
        $this->tim_dosen_id_array = [];
        $this->tim_dosen_items_array = [];
        $this->timDosenNameSearch = '';
    }

    public function searchOutputTimDosen($queryTimDosen, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allTimDosen = (clone $queryTimDosen)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allTimDosen = $allTimDosen->filter(function ($timDosen) use ($searchLower, $mode) {
                    // $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    // $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $timDosen->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE TimDosen
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $timDosen->kode,
                        $searchLower
                    );

                    $matchTim = $this->containsStrict(
                        $timDosen->tim,
                        $searchLower
                    );

                    $matchKetua = $this->containsStrict(
                        $timDosen->ketua,
                        $searchLower
                    ) || $this->containsStrict(
                        'Ketua '.$timDosen->ketua,
                        $searchLower
                    );

                    $matchNIP = $this->matchOnlyCount(
                        $timDosen->nip,
                        $searchLower, ['nip', 'ketua nip', 'id1', 'identity1', 'ketua']
                    ) || $this->containsStrict(
                        $timDosen->identity1,
                        $searchLower
                    );

                    $matchDosen = $this->matchCount(
                        $timDosen->count_dosen,
                        $searchLower, ['pengajar', 'dosen']
                    ) || $this->containsStrict(
                        $timDosen->count_dosen.' Dosen',
                        $searchLower
                    );

                    $matchKoordinator = $this->matchCount(
                        $timDosen->count_koordinator,
                        $searchLower, ['koor', 'koordinator']
                    ) || $this->containsStrict(
                        $timDosen->count_koordinator.' Koordinator',
                        $searchLower
                    );

                   $matchPengajar = $this->matchCount(
                        $timDosen->count_pengajar,
                        $searchLower, ['pembimbing', 'pengajar']
                    ) || $this->containsStrict(
                        $timDosen->count_pengajar.' Pengajar',
                        $searchLower
                    );

                   $matchAsisten = $this->matchCount(
                        $timDosen->count_asisten,
                        $searchLower, ['aslab', 'asisten lab', 'asisten', 'asdos', 'asisten dosen']
                    ) || $this->containsStrict(
                        $timDosen->count_asisten.' Asisten',
                        $searchLower
                    );


                        $rps = (int) ($timDosen->count_rps ?? 0);
                        $matchRPS = false;
                        if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                            $targetRPS = (int) max(
                                $matches[1] ?? 0,
                                $matches[2] ?? 0
                            );
                            $matchRPS = $rps === $targetRPS;
                        }
                        $matchRPS = $this->matchCount(
                            $rps,
                            $searchLower, ['rps']
                        ) || $this->containsStrict(
                            $rps.' RPS',
                            $searchLower
                        );

                        $sks = (int) ($timDosen->total_sks ?? 0);
                        $matchSKS = false;
                        if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                            $targetSKS = (int) max(
                                $matches[1] ?? 0,
                                $matches[2] ?? 0
                            );
                            $matchSKS = $sks === $targetSKS;
                        }
                        $matchSKS = $this->matchCount(
                            $sks,
                            $searchLower, ['sks']
                        ) || $this->containsStrict(
                            $sks.' SKS',
                            $searchLower
                        );

                    $matchKodePr = $this->matchKode(
                        $timDosen->pr_rel->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $timDosen->pr_rel->dp_rel->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $timDosen->pr_rel->dp_rel->fk_rel->kode_fk,
                        $searchLower
                    );


                    $basePr = [
                        $timDosen->pr_rel->prodi,
                        $timDosen->pr_rel->prodi_pr,
                        $timDosen->pr_rel->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$timDosen->pr_rel->dp_rel->kode_pr,
                            $pr.' ('.$timDosen->pr_rel->dp_rel->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $timDosen->pr_rel->dp_rel->departemen,
                        $timDosen->pr_rel->dp_rel->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$timDosen->pr_rel->dp_rel->kode_dp,
                            $dp.' ('.$timDosen->pr_rel->dp_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $timDosen->pr_rel->dp_rel->fk_rel->fakultas,
                        $timDosen->pr_rel->dp_rel->fk_rel->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$timDosen->pr_rel->dp_rel->fk_rel->kode_fk,
                            $fk.' ('.$timDosen->pr_rel->dp_rel->fk_rel->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }


                    $matchCreatedAt = $this->matchDateField(
                        $timDosen->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $timDosen->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'bobot':
                            return $matchBobot;
                        case 'nilai':
                            return $matchNilaiAkhir || $matchNilaiMutu;
                        case 'index':
                            return $matchNilaiIndex;
                        case 'mutu':
                            return $matchNilaiMutu;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchTim
                        || $matchKetua
                        || $matchNIP

                        || $matchDosen
                        || $matchKoordinator
                        || $matchPengajar
                        || $matchAsisten

                        || $matchRPS
                        || $matchSKS

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($timDosen) => $timDosen->kode,
                'nama_tim' => fn ($timDosen) => $timDosen->nama_tim,

                'ketua_tim' => fn ($timDosen) => $timDosen->ketua,
                'nip_tim' => fn ($timDosen) => $timDosen->nip,

                'count_dosen' => fn ($timDosen) => $timDosen->count_dosen,
                'count_koordinator' => fn ($timDosen) => $timDosen->count_koordinator,
                'count_pengajar' => fn ($timDosen) => $timDosen->count_pengajar,
                'count_asisten' => fn ($timDosen) => $timDosen->count_asisten,

                'count_rps' => fn ($timDosen) => $timDosen->count_rps ?? 0,
                'total_sks' => fn ($timDosen) => $timDosen->total_sks ?? 0,

                'created_at' => fn ($timDosen) => $timDosen->created_at,
                'updated_at' => fn ($timDosen) => $timDosen->updated_at,

                default => fn ($timDosen) => $timDosen->id,
            };

            $allTimDosen = $sortDirection === 'asc'
                ? $allTimDosen->sortBy($sortValue)
                : $allTimDosen->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allTimDosen->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allTimDosen->forPage($currentPage, $perPage)->values(),
                $allTimDosen->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryTimDosen;

        }

        return $queryTimDosen->paginate($perPage);
    }
}
