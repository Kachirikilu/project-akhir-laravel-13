<?php

namespace App\Livewire\Global;

use App\Models\Akademik\RPS;
use App\Livewire\Global\LogicSearch;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithRPSSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $rpsSearchQuery = '';

    public $rpsSearchResults = [];

    public $modeRPS = '';

    public $rps_id;

    public $rps_name = '';

    public $rps_items;

    public $rpsNameSearch = '';

    public $rpsResults = [];

    public $selectedRPSId = null;

    public $rps_id_array = [];

    public $rps_items_array = [];

    private function mapRPS($collection)
    {
        if ($collection instanceof AbstractPaginator) {
            $collection = $collection->getCollection();
        }

        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'mk_id' => $r->mk_id,
            'kode_mk' => $r->kode_mk,
            'kode_blok' => $r->kode_blok,
            'kode' => $r->kode,
            'rps' => $r->rps,
            'rps_with_kode' => $r->rps_with_kode,
            'mk' => $r->mk,
            'deskripsi' => $r->deskripsi,
            'akademik' => $r->akademik,
            'draf' => $r->draf,
            'draf_text' => $r->draf_text,
            'draf_full' => $r->draf_full,
            'revisi' => $r->revisi,
            'count_cpmk' => $r->count_cpmk,
            'count_scpmk' => $r->count_scpmk,
            'wajib' => $r->wajib,
            'wajib_text' => $r->wajib_text,
            'sks' => $r->sks,
            'sks_text' => $r->sks_text,
            'sks_full' => $r->sks_full,
            'bobot_uts' => $r->bobot_uts,
            'bobot_uas' => $r->bobot_uas,
            'total_bobot' => $r->total_bobot,
        ])->toArray();
    }

    private function mapRPSSearch($collection)
    {
        if ($collection instanceof AbstractPaginator) {
            $collection = $collection->getCollection();
        }

        return $collection->map(fn ($r) => [
            'id' => $r->id,
            'kode' => $r->kode,
            'rps' => $r->rps,
            'rps_with_kode' => $r->rps_with_kode,
            'draf_text' => $r->draf_text,
            'draf_full' => $r->draf_full,
            'wajib_text' => $r->wajib_text,
            'sks_full' => $r->sks_full,
        ])->toArray();
    }

    private function rpsQuery()
    {
        return RPS::query()->with(['mk_rel', 'cpmks', 'cpmks.scpmks']);
    }

    private function itemsRPS($r)
    {
        if (! $r) {
            return null;
        }

        return [
            'id' => $r->id,
            'kode' => $r->kode,
            'slot1' => $r->rps,
            'slot2' => $r->sks_full,
            'slot3' => $r->wajib_text,
            'slot4' => $r->draf_full,
        ];
    }

    public function inputRPSFilter()
    {
        $search = trim($this->rpsSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->rps_name)) {
            $this->rpsSearchResults = $this->mapRPSSearch(
                // $this->rpsQuery()->searchRPS($search)->limit(12)->get()
                $this->searchOutputRPS($this->rpsQuery(), $search, null, 12)
            );
        } elseif (empty($search) || $this->rps_name) {
            $this->rpsSearchResults = $this->getRPSbyUser('search');
        } else {
            $this->rpsSearchResults = [];
        }
    }

    public function resetRPSFilter()
    {
        $this->reset(['selectedRPSId', 'rpsSearchQuery', 'rps_name', 'rps_items']);
        $this->resetPage();
    }

    public function selectRPSForFilter($id)
    {
        $data = $this->rpsQuery()->with(['mk_rel'])->find($id);

        if ($data) {
            $this->selectedRPSId = $id;
            $this->rps_name = $data->rps;
            $this->rpsSearchQuery = $data->rps;
            $this->rps_items = $this->itemsRPS($data);
            $this->rpsSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedRPSNameSearch($value)
    {
        $this->rps_id = null;
        $this->rps_items = null;
        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);

        $query = $this->rpsQuery()->select('rps.*');

        $this->haveRPSParent($query);

        if (trim(strlen($value)) > 0) {
            // $results = $query->searchRPS($value)->limit(12)->get();
            $results = $this->searchOutputRPS($query, $value, null, 12);
            $this->rpsResults = $this->mapRPS($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($r) use ($value, $normalizedValue) {
                $normalizedRPSKode = str_replace(['-', ' '], '', strtolower($r->kode));

                return strtolower($r->rps) === strtolower($value)
                    || strtolower($r->mk) === strtolower($value)
                    || $normalizedRPSKode === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeRPS == 'single') {
                    $this->rpsNameSearch = $exactMatch->rps;
                    $this->rps_id = $exactMatch->id;
                    $this->rps_items = $this->itemsRPS($exactMatch);
                    $this->rpsResults = [];
                } else {
                    $this->rpsNameSearch = '';
                    $this->rps_id_array[] = $exactMatch->id;
                    $this->rps_items_array[] = $this->itemsRPS($exactMatch);
                }
                $this->rpsResults = $this->getRPSbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->rpsResults = $this->getRPSbyUser();
            } else {
                $this->rpsResults = $this->mapRPS(
                    $query->orderBy('rps.mk_rel.nama_mk')->limit(12)->get()
                );
            }
        }
    }

    public function getRPSbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->rpsQuery();

        if (! $prodiId) {
            $defaultRPS = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapRPSSearch($defaultRPS)
                : $this->mapRPS($defaultRPS);
        }

        $this->haveRPSParent($query);

        $mainResults = $query
            ->whereHas('mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->rpsQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapRPSSearch($mainResults)
            : $this->mapRPS($mainResults);
    }

    public function fetchRPS($query = '', $mode = 'single')
    {
        $this->modeRPS = $mode;
        if (empty($query) || $this->rps_id) {
            $this->rpsResults = $this->getRPSbyUser();
        }

    }

    public function selectRPS($id, $rpsName)
    {
        $this->rps_id = $id;
        $this->rpsNameSearch = $rpsName;
        $this->rpsResults = $this->getRPSbyUser();

        $data = $this->rpsQuery()->find($id);
        if ($data) {
            $this->rps_items = $this->itemsRPS($data);
        }

        if (method_exists($this, 'fetchRPS')) {
            $this->fetchRPS('');
        }

        $this->resetErrorBag(['rps_id', 'rpsNameSearch']);
    }

    public function selectRPSArray($id)
    {
        $data = $this->rpsQuery()->find($id);
        if ($data && ! in_array($id, $this->rps_id_array)) {
            $this->rps_id_array[] = $id;
            $this->rps_items_array[] = $this->itemsRPS($data);
        }
    }

    public function resetRPSInput()
    {
        $this->reset(['rps_id', 'rps_items', 'rpsNameSearch']);
        $this->rpsResults = $this->getRPSbyUser();
    }

    public function resetRPSArray()
    {
        $this->rps_id_array = [];
        $this->rps_items_array = [];
        $this->rpsNameSearch = '';
    }

    public function haveRPSParent($query)
    {
        if (property_exists($this, 'showKelasModal')) {
            if ($this->showKelasModal == true && filled($this->pr_id)) {
                $query->whereHas('mk_rel.prodis', function ($q) {
                    $q->where('prodis.id', $this->pr_id);
                });
            }
        }

        return $query;
    }

    public function searchOutputRPS($queryRPS, $searchRaw, $searchBobot, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchBobot = strtolower(trim($searchBobot));
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || ! empty($searchBobot) || $sortField) {

            $allRPS = (clone $queryRPS)->get();

            if (! empty($search) || ! empty($searchBobot)) {

                $mode = $this->detectSearchMode($searchLower);

                $allRPS = $allRPS->filter(function ($rps) use ($searchLower, $searchBobot, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';
                    // $numberBobot = preg_replace('/[^0-9.]/', '', $searchBobot);
                    // $isNumericBobot = is_numeric($numberBobot) && $numberBobot !== '';

                    $matchID = $this->matchID(
                        $rps->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE RPS
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $rps->kode,
                        $searchLower
                    );
                    $matchKodeMK = $this->matchKode(
                        $rps->kode_mk,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | NAMA MK
                    |--------------------------------------------------------------------------
                    */
                    $matchMK = $this->containsStrict(
                        $rps->mk,
                        $searchLower
                    );
                    $matchRPS = $this->containsStrict(
                        $rps->rps,
                        $searchLower
                    );
                    $matchAkademik = $this->matchAkademik(
                        $rps->akademik,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SEMESTER
                    |--------------------------------------------------------------------------
                    */
                    $matchSemester = $this->matchCount(
                        $rps->semester,
                        $searchLower,
                        [
                            'sem',
                            'semester',
                            'semes',
                            'sms',
                        ]
                    ) || $this->containsStrict(
                        'Semester'.$rps->semester,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKS
                    |--------------------------------------------------------------------------
                    */
                    $sks = (int) ($rps->sks ?? 0);
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
                        $sks. 'SKS',
                        $searchLower
                    );

                    $matchSKSText = $this->matchSKSText(
                        $rps->sks_text,
                        $searchLower
                    );

                    $matchNo = $this->matchNo(
                        $rps->mk_rel->digit_mk,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | GANJIL / GENAP
                    |--------------------------------------------------------------------------
                    */
                    $matchSemesterJenis = $this->matchSemesterJenis(
                        $rps->semester,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | CPMK COUNT
                    |--------------------------------------------------------------------------
                    */
                    $matchCPMK = $this->matchCount(
                        $rps->count_cpmk,
                        $searchLower,
                        ['cpmk', 'cpl']
                    );

                    $matchSCPMK = $this->matchCount(
                        $rps->count_scpmk,
                        $searchLower,
                        [
                            'per',
                            'pertem',
                            'pertemuan',
                            'scpmk',
                            'sub-cpmk',
                            'subcpmk',
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL BOBOT
                    |--------------------------------------------------------------------------
                    */
                    $matchBobot = false;
                    if ($isNumericSearch) {
                        $matchBobot = $this->compareNumber(
                            (float) $rps->total_bobot,
                            $searchLower
                        ) || $this->containsStrict(
                            $rps->total_bobot,
                            $searchLower
                        );
                    }
                    if (! empty($searchBobot)) {
                        $matchBobot = $this->compareNumber(
                            (float) $rps->total_bobot,
                            $searchBobot
                        ) || $this->containsStrict(
                            $rps->total_bobot,
                            $searchBobot
                        );
                    }

                    $matchDraf = $this->matchDraf(
                        $rps->draf_text,
                        $searchLower
                    );

                    $matchWajib = $this->matchWajib(
                        $rps->wajib_text,
                        $searchLower
                    );

                    // /*
                    // |--------------------------------------------------------------------------
                    // | Kodse Program Studi
                    // |--------------------------------------------------------------------------
                    // */
                    // $matchKodePr = $this->matchKode(
                    //     $rps->pr_rel->kode_pr,
                    //     $searchLower
                    // );
                    // $matchKodeDp = $this->matchKode(
                    //     $rps->pr_rel->kode_dp,
                    //     $searchLower
                    // );
                    // $matchKodeFk = $this->matchKode(
                    //     $rps->pr_rel->kode_fk,
                    //     $searchLower
                    // );

                    // $basePr = [
                    //     $rps->pr_rel->prodi,
                    //     $rps->pr_rel->prodi_pr,
                    //     $rps->pr_rel->prodi_strata,
                    // ];
                    // $matchPr = false;
                    // foreach ($basePr as $pr) {
                    //     $candidates = [
                    //         $pr.' '.$rps->pr_rel->kode_dp,
                    //         $pr.' ('.$rps->pr_rel->kode_dp.')',
                    //     ];
                    //     foreach ($candidates as $candidate) {
                    //         if ($this->containsStrict($candidate, $searchLower)) {
                    //             $matchPr = true;
                    //             break 2;
                    //         }
                    //     }
                    // }

                    // $baseDp = [
                    //     $rps->pr_rel->departemen,
                    //     $rps->pr_rel->departemen_dp,
                    // ];
                    // $matchDp = false;
                    // foreach ($baseDp as $dp) {
                    //     $candidates = [
                    //         $dp.' '.$rps->pr_rel->kode_dp,
                    //         $dp.' ('.$rps->pr_rel->kode_dp.')',
                    //     ];
                    //     foreach ($candidates as $candidate) {
                    //         if ($this->containsStrict($candidate, $searchLower)) {
                    //             $matchDp = true;
                    //             break 2;
                    //         }
                    //     }
                    // }

                    // $baseFk = [
                    //     $rps->pr_rel->fakultas,
                    //     $rps->pr_rel->fakultas_fk,
                    // ];
                    // $matchFk = false;
                    // foreach ($baseFk as $fk) {
                    //     $candidates = [
                    //         $fk.' '.$rps->pr_rel->kode_fk,
                    //         $fk.' ('.$rps->pr_rel->kode_fk.')',
                    //     ];
                    //     foreach ($candidates as $candidate) {
                    //         if ($this->containsStrict($candidate, $searchLower)) {
                    //             $matchFk = true;
                    //             break 2;
                    //         }
                    //     }
                    // }

                    $matchRevisi = $this->matchDateField(
                        $rps->revisi,
                        $searchLower,
                        ['revisi', 'revision']
                    );
                    $matchCreatedAt = $this->matchDateField(
                        $rps->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );
                    $matchUpdatedAt = $this->matchDateField(
                        $rps->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );



                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'semester':
                            return $matchSemester || $matchSemesterJenis;
                        case 'sks':
                            return $matchSKS || $matchSKSText;
                        case 'nomor':
                            return $matchNo;
                        case 'cpmk':
                            return $matchCPMK;
                        case 'scpmk':
                            return $matchSCPMK;
                        case 'bobot':
                            return $matchBobot;
                        case 'wajib':
                            return $matchWajib;
                        case 'status':
                            return $matchDraf;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchAkademik

                        || $matchKodeMK
                        || $matchMK
                        || $matchRPS
                        || $matchSemester
                        || $matchSemesterJenis
                        || $matchSKS
                        || $matchSKSText
                        || $matchNo

                        || $matchCPMK
                        || $matchSCPMK
                        || $matchBobot

                        || $matchDraf
                        || $matchWajib
                        || $matchRevisi

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($rps) => $rps->kode,
                'akademik' => fn ($rps) => $rps->akademik,

                'kode_mk' => fn ($rps) => $rps->kode_mk,
                'mk' => fn ($rps) => $rps->mk,
                'semester' => fn ($rps) => (int) $rps->semester,
                'sks' => fn ($rps) => (int) $rps->sks,
                'sks_text', 'pembelajaran' => fn ($rps) => $rps->sks_text,
                'is_wajib', 'wajib' => fn ($rps) => $rps->wajib_text,

                'count_cpmk' => fn ($rps) => (int) $rps->count_cpmk,
                'count_scpmk' => fn ($rps) => (int) $rps->count_scpmk,
                'total_bobot' => fn ($rps) => (float) $rps->total_bobot,

                'is_draf', 'status' => fn ($rps) => $rps->draf_text,
                'revisi' => fn ($rps) => $rps->revisi,

                'created_at' => fn ($rps) => $rps->created_at,
                'updated_at' => fn ($rps) => $rps->updated_at,

                default => fn ($rps) => $rps->id,
            };

            $allRPS = $sortDirection === 'asc'
                ? $allRPS->sortBy($sortValue)
                : $allRPS->sortByDesc($sortValue);

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allRPS->forPage($currentPage, $perPage)->values(),
                $allRPS->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }
        return $queryRPS->paginate($perPage);
    }
}
