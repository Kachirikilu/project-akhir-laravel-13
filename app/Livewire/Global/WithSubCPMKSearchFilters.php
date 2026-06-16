<?php

namespace App\Livewire\Global;

use App\Models\Akademik\SubCPMK;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithSubCPMKSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $scpmkSearchQuery = '';

    public $scpmkSearchResults = [];

    public $modeSCPMK = '';

    public $scpmk_id;

    public $scpmk_name = '';

    public $scpmk_items = [];

    public $scpmkNameSearch = '';

    public $scpmkResults = [];

    public $selectedSCPMKId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $scpmk_id_array = [];

    public $scpmk_items_array = [];

    public $scpmk_sub_items_array = [];

    private function mapSCPMK($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->kode,
            'deskripsi' => $s->deskripsi,
            'materi' => $s->materi,
            'metodologi' => $s->metodologi,
            'indikator' => $s->indikator,
            'metode' => $s->metode,
            'tugas' => $s->tugas,
            'w_tugas' => $s->w_tugas,
            'w_mandiri' => $s->w_mandiri,
            'waktu_tugas' => $s->waktu_tugas,
            'waktu_mandiri' => $s->waktu_mandiri,
            'bobot' => $s->bobot ?? 0,
            'bobot' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.'),
            'bobot_text' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.').'% Bobot',
            'ref' => $this->mapRef($s->refs),
            'dosen' => $this->mapDosen($s->dosens),
        ])->toArray();
    }

    private function mapSCPMKSearch($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->kode,
            'deskripsi' => $s->deskripsi,
            'metode' => $s->metode,
            'bobot_text' => rtrim(rtrim(number_format($s->bobot ?? 0, 2, '.', ''), '0'), '.').'% Bobot',
        ])->toArray();
    }

    private function scpmkQuery()
    {
        return SubCPMK::query()->with('cpmks.rps', 'cpmks', 'refs');
    }

    private function itemsSCPMK($s)
    {
        if (! $s) {
            return null;
        }

        return [
            'id' => $s->id,
            'kode' => $s->kode,
            'slot1' => $s->deskripsi,
        ];
    }

    private function pushToSCPMKItems($mappedResults)
    {
        $mappedData = $mappedResults[0] ?? null;

        if ($mappedData) {
            $this->scpmk_sub_items_array[] = [
                'scpmk' => [$mappedData],
            ];
        }
    }

    public function inputSCPMKFilter()
    {
        $search = trim($this->scpmkSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->scpmk_name)) {
            $this->scpmkSearchResults = $this->mapSCPMKSearch(
                $this->scpmkQuery()->searchSCPMK($search)->limit(12)->get()
                // $this->searchOutputSCPMK($this->scpmkQuery(), $search, null, 12)
            );
        } elseif (empty($search) || $this->scpmk_name) {
            $this->scpmkSearchResults = $this->getSCPMKbyUser('search');
        } else {
            $this->scpmkSearchResults = [];
        }
    }

    public function resetSCPMKFilter()
    {
        $this->reset(['selectedSCPMKId', 'scpmkSearchQuery', 'scpmk_name', 'scpmk_items']);
        $this->resetPage();
    }

    public function selectSCPMKForFilter($id)
    {
        $data = $this->scpmkQuery()->find($id);

        if ($data) {
            $this->selectedSCPMKId = $id;
            $this->scpmk_name = $data->deskripsi;
            $this->scpmkSearchQuery = $data->deskripsi;
            $this->scpmk_items = $this->itemsSCPMK($data);
            $this->scpmkSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedSCPMKNameSearch($value)
    {
        $this->scpmk_id = null;
        $this->scpmk_items = null;
        $this->resetErrorBag(['scpmk_id', 'scpmkNameSearch']);

        $query = $this->scpmkQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchSCPMK($value)->limit(12)->get();
            // $results = $this->searchOutputSCPMK($query, $value, null, 12);
            $this->scpmkResults = $this->mapSCPMK($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($sc) use ($value, $normalizedValue) {
                $normalizedSCPMKKode = str_replace(['-', ' '], '', strtolower($sc->kode));

                return strtolower($sc->deskripsi) === strtolower($value)
                    || $normalizedSCPMKKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->scpmk_id = $exactMatch->id;
                $this->scpmk_items = $this->itemsSCPMK($exactMatch);
                $this->scpmkNameSearch = $exactMatch->deskripsi;
                $this->scpmkResults = [];
            }
            if ($exactMatch) {
                if ($this->modeSCPMK == 'single') {
                    $this->scpmkNameSearch = $exactMatch->deskripsi;
                    $this->scpmk_id = $exactMatch->id;
                    $this->scpmk_items = $this->itemsSCPMK($exactMatch);
                    $this->scpmkResults = [];
                } else {
                    $this->scpmkNameSearch = '';
                    $this->scpmk_id_array[] = $exactMatch->id;
                    $this->scpmk_items_array[] = $this->itemsSCPMK($exactMatch);
                    $mappedResults = $this->mapSCPMK(collect([$exactMatch]));
                    $this->scpmk_id_array = collect($this->scpmk_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->scpmk_items_array = collect($this->scpmk_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapSCPMK(collect([$exactMatch]));
                $this->pushToSCPMKItems($mappedResults);
                $this->scpmkResults = $this->getSCPMKbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->scpmkResults = $this->getSCPMKbyUser();
            } else {
                $this->scpmkResults = $this->mapSCPMK(
                    $query->orderBy('sub_cpmks.deskripsi', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getSCPMKbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->scpmkQuery();

        if (! $prodiId) {
            $defaultSCPMK = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapSCPMKSearch($defaultSCPMK)
                : $this->mapSCPMK($defaultSCPMK);
        }

        $mainResults = $query
            ->whereHas('cpmks.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = SubCPMK::whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapSCPMKSearch($mainResults)
            : $this->mapSCPMK($mainResults);
    }

    public function fetchSCPMK($query = '', $mode = 'single')
    {
        $this->modeSCPMK = $mode;
        if (empty($query) || $this->scpmk_id) {
            $this->scpmkResults = $this->getSCPMKbyUser();
        }

    }

    public function selectSCPMK($id, $scpmkName)
    {
        $this->scpmk_id = $id;
        $this->scpmkNameSearch = $scpmkName;
        $this->scpmkResults = $this->getSCPMKbyUser();

        $data = $this->scpmkQuery()->find($id);
        if ($data) {
            $this->scpmk_items = $this->itemsSCPMK($data);
            $mappedResults = $this->mapSCPMK(collect([$data]));
            $this->pushToSCPMKItems($mappedResults);
        }

        if (method_exists($this, 'fetchSCPMK')) {
            $this->fetchSCPMK('');
        }

        $this->resetErrorBag(['scpmk_id', 'scpmkNameSearch']);
    }

    public function selectSCPMKArray($id)
    {
        $data = $this->scpmkQuery()->find($id);
        if ($data && ! in_array($id, $this->scpmk_id_array)) {
            $this->scpmk_id_array[] = $id;
            $this->scpmk_items_array[] = $this->itemsSCPMK($data);
            $mappedResults = $this->mapSCPMK(collect([$data]));
            $this->pushToSCPMKItems($mappedResults);
        }
    }

    public function resetSCPMKInput()
    {
        $this->reset(['scpmk_id', 'scpmk_items', 'scpmkNameSearch']);
        $this->scpmkResults = $this->getSCPMKbyUser();
    }

    public function resetSCPMKArray()
    {
        $this->scpmk_id_array = [];
        $this->scpmk_items_array = [];
        $this->scpmkNameSearch = '';
    }

    public function searchOutputSCPMK($querySCPMK, $searchRaw, $searchBobot, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchBobot = strtolower(trim($searchBobot));
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || ! empty($searchBobot) || $sortField) {

            $allSCPMK = (clone $querySCPMK)->get();

            if (! empty($search) || ! empty($searchBobot)) {

                $mode = $this->detectSearchMode($searchLower);

                $allSCPMK = $allSCPMK->filter(function ($scpmk) use ($searchLower, $searchBobot, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';
                    // $numberBobot = preg_replace('/[^0-9.]/', '', $searchBobot);
                    // $isNumericBobot = is_numeric($numberBobot) && $numberBobot !== '';

                    $matchID = $this->matchID(
                        $scpmk->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE CPMK
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $scpmk->kode,
                        $searchLower
                    );

                    $matchDes = $this->containsStrict(
                        $scpmk->deskripsi,
                        $searchLower
                    );

                    $matchNilaiAkhir = $this->matchNilaiAkhir(
                        $scpmk->rekap_scpmk_pr ?? 0,
                        $searchLower
                    );

                    $matchNilaiIndex = $this->matchNilaiIndex(
                        $scpmk->index_scpmk_pr ?? 0,
                        $searchLower
                    );

                    $matchNilaiMutu = $this->matchNilaiMutu(
                        $scpmk->mutu_scpmk_pr ?? 'E',
                        $searchLower
                    );

                    $matchMetode = $this->matchMetode(
                        $scpmk->metode,
                        $searchLower
                    );
                    $matchMateri = $this->containsStrict(
                        $scpmk->materi,
                        $searchLower
                    );
                    $matchMetodologi = $this->containsStrict(
                        $scpmk->metodologi,
                        $searchLower
                    );
                    $matchIndikator = $this->containsStrict(
                        $scpmk->indikator,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL BOBOT
                    |--------------------------------------------------------------------------
                    */
                    $matchBobot = false;
                    if ($isNumericSearch) {
                        $matchBobot = $this->compareNumber(
                            (float) $scpmk->bobot,
                            $searchLower
                        ) || $this->containsStrict(
                            $scpmk->bobot,
                            $searchLower
                        );
                    }
                    if (! empty($searchBobot)) {
                        $matchBobot = $this->compareNumber(
                            (float) $scpmk->bobot,
                            $searchBobot
                        ) || $this->containsStrict(
                            $scpmk->bobot,
                            $searchBobot
                        );
                    }

                    $matchTugas = $this->containsStrict(
                        $scpmk->tugas,
                        $searchLower
                    );

                    $matchWTugas = $this->matchCount(
                        $scpmk->w_tugas,
                        $searchLower,
                        [
                            'min',
                            'menit',
                            'mnt',
                            'm/SKS',
                        ]
                    );
                    $matchWMandiri = $this->matchCount(
                        $scpmk->w_mandiri,
                        $searchLower,
                        [
                            'min',
                            'menit',
                            'mnt',
                            'm/SKS',
                        ]
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $scpmk->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $scpmk->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'metode':
                            return $matchMetode;
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

                        || $matchDes

                        | $matchNilaiAkhir
                        || $matchNilaiIndex
                        || $matchNilaiMutu

                        || $matchMetode
                        || $matchMateri
                        || $matchMetodologi
                        || $matchIndikator

                        || $matchBobot

                        || $matchTugas
                        || $matchWTugas
                        || $matchWMandiri

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($scpmk) => $scpmk->kode,
                'deskripsi' => fn ($scpmk) => $scpmk->deskripsi,

                'rekap_scpmk_pr',
                'index_scpmk_pr',
                'mutu_scpmk_pr' => fn ($scpmk) => (float) ($scpmk->rekap_scpmk_pr ?? 0),

                'metode' => fn ($scpmk) => $scpmk->metode,
                'materi' => fn ($scpmk) => $scpmk->materi,
                'metodologi' => fn ($scpmk) => $scpmk->metodologi,
                'indikator' => fn ($scpmk) => $scpmk->indikator,

                'bobot' => fn ($scpmk) => (float) $scpmk->bobot,
                'tugas' => fn ($scpmk) => $scpmk->tugas,
                'waktu_tugas', 'w_tugas' => fn ($scpmk) => $scpmk->w_tugas,
                'waktu_mandiri', 'w_mandiri' => fn ($scpmk) => $scpmk->w_mandiri,

                'created_at' => fn ($scpmk) => $scpmk->created_at,
                'updated_at' => fn ($scpmk) => $scpmk->updated_at,

                default => fn ($scpmk) => $scpmk->id,
            };

            $allSCPMK = $sortDirection === 'asc'
                ? $allSCPMK->sortBy($sortValue)
                : $allSCPMK->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allSCPMK->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allSCPMK->forPage($currentPage, $perPage)->values(),
                $allSCPMK->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $querySCPMK;
        }

        return $querySCPMK->paginate($perPage);
    }
}
