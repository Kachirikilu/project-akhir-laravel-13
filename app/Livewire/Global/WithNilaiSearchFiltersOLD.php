<?php

namespace App\Livewire\Global;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithNilaiSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $nilaiSearchQuery = '';

    public $nilaiSearchResults = [];

    public $modeNilai = [];

    public $nilai_id = [];

    public $nilai_name = [];

    public $nilai_items = [];

    public $nilaiNameSearch = [];

    public $nilaiResults = [];

    public $selectedNilaiId = [];

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $nilai_id_array = [];

    public $nilai_items_array = [];

    private function mapNilai($collection)
    {
        return $collection->map(fn ($n) => [
            'id' => $n->id,
            'rps' => $n->rps_rel?->rps,
        ])->toArray();
    }

    private function mapNilaiSearch($nollection)
    {
        return $nollection->map(fn ($n) => [
            'id' => $n->id,
            'rps' => $n->rps_rel?->rps,
        ])->toArray();
    }

    private function nilaiQuery()
    {
        return Nilai::query()->with('nilai.pr_rel', 'nilai.rps', 'nilai');
    }

    private function itemsNilai($n)
    {
        if (! $n) {
            return null;
        }

        return [
            'id' => $n->id,
            'kode' => $n->rps,
        ];
    }

    public function getNilaiIdArrayForKey(string $key = 'default'): array
    {
        if (is_array($this->nilai_id_array) && array_key_exists($key, $this->nilai_id_array) && is_array($this->nilai_id_array[$key])) {
            return $this->nilai_id_array[$key];
        }

        return [];
    }

    public function getNilaiNameSearchForKey(string $key = 'default'): string
    {
        if (is_array($this->nilaiNameSearch) && array_key_exists($key, $this->nilaiNameSearch)) {
            return is_string($this->nilaiNameSearch[$key]) ? $this->nilaiNameSearch[$key] : '';
        }

        return '';
    }

    public function inputNilaiFilter()
    {
        $search = trim($this->nilaiSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->nilai_name)) {
            $this->nilaiSearchResults = $this->mapNilaiSearch(
                // $this->nilaiQuery()->searchNilai($search)->limit(12)->get()
                $this->searchOutputNilai($this->nilaiQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->nilai_name) {
            $this->nilaiSearchResults = $this->getNilaibyUser('search');
        } else {
            $this->nilaiSearchResults = [];
        }
    }

    public function resetNilaiFilter()
    {
        $this->reset(['selectedNilaiId', 'nilaiSearchQuery', 'nilai_name', 'nilai_items']);
        $this->resetPage();
    }

    public function selectNilaiForFilter($id)
    {
        $data = $this->nilaiQuery()->find($id);

        if ($data) {
            $this->selectedNilaiId = $id;
            $this->nilai_name = $data->deskripsi;
            $this->nilaiSearchQuery = $data->deskripsi;
            $this->nilai_items = $this->itemsNilai($data);
            $this->nilaiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedNilaiNameSearch($value, $name = null)
    {
        $key = 'default';

        if (is_string($name) && str_contains($name, '.')) {
            [, $key] = explode('.', $name, 2);
        } elseif (is_string($name) && $name !== 'nilaiNameSearch') {
            $key = $name;
        }

        if (is_array($value)) {
            $value = $value[$key] ?? '';
        }

        $this->nilai_id[$key] = null;
        $this->nilai_items[$key] = null;
        $this->resetErrorBag(['nilai_id.'.$key, 'nilaiNameSearch.'.$key]);

        $query = $this->nilaiQuery();

        if (trim(strlen((string) $value)) > 0) {
            // $results = $query->searchNilai($value)->limit(12)->get();
            $results = $this->searchOutputNilai($query, $value, 12);
            $this->nilaiResults[$key] = $this->mapNilai($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($nilai) use ($value, $normalizedValue) {
                $normalizedNilaiKode = str_replace(['-', ' '], '', strtolower($nilai->kode));

                return strtolower($nilai->deskripsi) === strtolower($value)
                    || $normalizedNilaiKode === $normalizedValue;
            });

            if ($exactMatch) {
                $currentMode = $this->modeNilai[$key] ?? 'array';
                if ($currentMode == 'single') {
                    $this->nilaiNameSearch[$key] = $exactMatch->deskripsi;
                    $this->nilai_id[$key] = $exactMatch->id;
                    $this->nilai_items[$key] = $this->itemsNilai($exactMatch);
                    $this->nilaiResults[$key] = [];
                } else {
                    $this->nilaiNameSearch[$key] = '';
                    if (! isset($this->nilai_id_array[$key])) {
                        $this->nilai_id_array[$key] = [];
                    }
                    if (! isset($this->nilai_items_array[$key])) {
                        $this->nilai_items_array[$key] = [];
                    }
                    if (! in_array($exactMatch->id, $this->nilai_id_array[$key])) {
                        $this->nilai_id_array[$key][] = $exactMatch->id;
                        $this->nilai_items_array[$key][] = $this->itemsNilai($exactMatch);
                    }
                }
                $this->nilaiResults[$key] = $this->getNilaibyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->nilaiResults[$key] = $this->getNilaibyUser();
            } else {
                $this->nilaiResults[$key] = $this->mapNilai(
                    $query->orderBy('nilais.id', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getNilaibyUser($mode = 'complex')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->nilaiQuery();

        if (! $prodiId) {
            $defaultNilai = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapNilaiSearch($defaultNilai)
                : $this->mapNilai($defaultNilai);
        }

        $mainResults = $query
            ->whereHas('nilai.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->nilaiQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapNilaiSearch($mainResults)
            : $this->mapNilai($mainResults);
    }


    public function fetchNilai($mode = 'single')
    {
        $this->modeNilai = $mode;
        if ($this->nilai_id) {
            $nilai = Nilai::find($this->nilai_id);
            if ($nilai) {
                $this->nilaiNameSearch = $nilai->nilai;
                $this->nilai_items = $this->itemsNilai($nilai);
            }
            $this->nilaiResults = $this->getNilaibyUser();
            return;
        }
    }


    public function selectNilai($id, $nilaiName, $key = 'default')
    {
        $this->nilai_id[$key] = $id;
        $this->nilaiNameSearch[$key] = $nilaiName;
        $this->nilaiResults[$key] = $this->getNilaibyUser();

        $data = $this->nilaiQuery()->find($id);
        if ($data) {
            $this->nilai_items[$key] = $this->itemsNilai($data);

            // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
            //     $this->deskripsi_cpmk = $data->deskripsi;
            // }
        }

        if (method_exists($this, 'fetchNilai')) {
            $this->fetchNilai($this->modeNilai[$key] ?? 'single', $key);
        }

        $this->resetErrorBag(['nilai_id.'.$key, 'nilaiNameSearch.'.$key]);
    }

    public function selectNilaiArray($id, $key = 'default')
    {
        $data = $this->nilaiQuery()->find($id);
        if ($data) {
            if (! isset($this->nilai_id_array[$key])) {
                $this->nilai_id_array[$key] = [];
            }

            if (! in_array($id, $this->nilai_id_array[$key])) {
                $this->nilai_id_array[$key][] = $id;
                $this->nilai_items_array[$key][] = $this->itemsNilai($data);
            }

            // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
            //     $newDesc = trim($data->deskripsi);
            //     if (!str_ends_with($newDesc, '.')) {
            //         $newDesc .= '.';
            //     }

            //     if (!empty($this->deskripsi_cpmk)) {
            //         $this->deskripsi_cpmk = rtrim($this->deskripsi_cpmk) . ' ' . $newDesc;
            //     } else {
            //         $this->deskripsi_cpmk = $newDesc;
            //     }
            // }
        }
    }

    public function resetNilaiInput($key = 'default')
    {
        $this->reset(['nilai_id', 'nilai_items', 'nilaiNameSearch']);
        $this->nilaiResults[$key] = $this->getNilaibyUser();

        // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
        //     $this->deskripsi_cpmk = '';
        // }
    }

    public function resetNilaiArray($key = 'default')
    {
        $this->nilai_id_array[$key] = [];
        $this->nilai_items_array[$key] = [];
        $this->nilaiNameSearch[$key] = '';

        // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
        //     $this->deskripsi_cpmk = '';
        // }
    }

    public function searchOutputNilaixx($queryNilai, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allNilai = (clone $queryNilai)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allNilai = $allNilai->filter(function ($n) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchIndex = false;
                    if ($isNumericSearch) {
                        $matchIndex = $this->compareNumber(
                            (float) $n->ip_semester,
                            $searchLower
                        ) || $this->containsStrict(
                            $n->ip_semester,
                            $searchLower
                        );
                    }
                    $matchIndex = $this->matchNilaiIndex(
                        $n->ip_semester,
                        $searchLower
                    );
                    /*
                    |--------------------------------------------------------------------------
                    | SEMESTER
                    |--------------------------------------------------------------------------
                    */
                    $semester = (int) ($n->semester ?? 0);
                    $matchSemester = $this->matchCount(
                        $n->semester,
                        $searchLower,
                        [
                            'sem',
                            'semester',
                            'semes',
                            'sms',
                            's',
                        ]
                    ) || $this->containsStrict(
                        'Semester'.$n->semester,
                        $searchLower
                    );
                    $matchSemesterJenis = $this->matchSemesterJenis(
                        $semester,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKS
                    |--------------------------------------------------------------------------
                    */
                    $sks = (int) ($n->total_sks ?? 0);
                    $matchSKS = false;
                    if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                        $targetSKS = (int) max(
                            $matches[1] ?? 0,
                            $matches[2] ?? 0
                        );
                        $matchSKS = $sks === $targetSKS;
                    }

                    // $matchCreatedAt = $this->matchDateField(
                    //     $n->created_at,
                    //     $searchLower,
                    //     ['created', 'dibuat', 'create']
                    // );

                    // $matchUpdatedAt = $this->matchDateField(
                    //     $n->updated_at,
                    //     $searchLower,
                    //     ['updated', 'diubah', 'update']
                    // );

                    switch ($mode) {
                        case 'index':
                            return $matchNilaiIndex;
                        case 'semester':
                            return $matchSemester || $matchSemesterJenis;
                        case 'sks':
                            return $matchSKS || $matchSKSText;
                    }

                    return
                        $matchIndex
                        || $matchSemester
                        || $matchSemesterJenis
                        || $matchSKS;

                    // || $matchCreatedAt
                    // || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'nilai_semester', 'ip_semester', 'mutu_semester' => fn ($n) => $n->nilai_semester,
                'semester' => fn ($n) => $n->semester,
                'sks' => fn ($n) => $n->total_sks,
                // 'created_at' => fn ($n) => $n->created_at,
                // 'updated_at' => fn ($n) => $n->updated_at,

                default => fn ($n) => $n->id,
            };

            $allNilai = $sortDirection === 'asc'
                ? $allNilai->sortBy($sortValue)
                : $allNilai->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allNilai->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allNilai->forPage($currentPage, $perPage)->values(),
                $allNilai->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryNilai;
        }

        return $queryNilai->paginate($perPage);
    }

    public function searchOutputNilai($calculatedPeriode, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);

        // Salin data collection utama agar aman dari efek referensi objek
        $allNilai = collect($calculatedPeriode);

        if (! empty($search)) {
            $mode = method_exists($this, 'detectSearchMode') ? $this->detectSearchMode($searchLower) : 'all';

            $allNilai = $allNilai->filter(function ($n) use ($searchLower, $mode) {
                $number = preg_replace('/[^0-9.]/', '', $searchLower);
                $isNumericSearch = is_numeric($number) && $number !== '';

                $matchNilaiAkhir = $this->matchNilaiAkhir(
                    $n->nilai_semester ?? 0,
                    $searchLower
                );

                $matchNilaiIndex = $this->matchNilaiIndex(
                    $n->ip_semester ?? 0,
                    $searchLower
                );

                $matchNilaiMutu = $this->matchNilaiMutu(
                    $n->mutu_semester ?? 'E',
                    $searchLower
                );

                /*
                |--------------------------------------------------------------------------
                | SKS
                |--------------------------------------------------------------------------
                */
                $sks = (int) ($n->total_sks ?? 0);
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

                $semester = (int) ($n->semester ?? 0);
                $matchSemester = $this->matchCount(
                    $n->semester,
                    $searchLower,
                    [
                        'sem',
                        'semester',
                        'semes',
                        'sms',
                        's',
                    ]
                ) || $this->containsStrict(
                    'Semester'.$n->semester,
                    $searchLower
                );
                $matchSemesterJenis = $this->matchSemesterJenis(
                    $semester,
                    $searchLower
                );

                $matchAkademik = $this->matchAkademik(
                    $n->akademik,
                    $searchLower
                );

                // $matchCreatedAt = $this->matchDateField(
                //     $n->created_at,
                //     $searchLower,
                //     ['created', 'dibuat', 'create']
                // );

                // $matchUpdatedAt = $this->matchDateField(
                //     $n->updated_at,
                //     $searchLower,
                //     ['updated', 'diubah', 'update']
                // );

                switch ($mode) {
                    case 'nilai':
                        return $matchNilaiAkhir || $matchNilaiMutu;
                    case 'akademik':
                        return $matchAkademik;
                    case 'index':
                        return $matchNilaiIndex;
                    case 'mutu':
                        return $matchNilaiMutu;
                    case 'semester':
                        return $matchSemester || $matchSemesterJenis;
                    case 'sks':
                        return $matchSKS;
                }

                return
                    $matchNilaiAkhir
                    || $matchNilaiIndex
                    || $matchNilaiMutu
                    || $matchSemester
                    || $matchSemesterJenis
                    || $matchSKS
                    || $matchAkademik;

            });
        }

        // --- LOGIKA SORTIR DATA COLLECTION ---
        if ($sortField) {
            $sortValue = match ($sortField) {
                'nilai_semester', 'ip_semester', 'nilai_index', 'mutu_semester' => fn ($n) => $n->nilai_semester,
                'semester' => fn ($n) => $n->semester,
                'sks' => fn ($n) => $n->total_sks,
                'sks', 'total_sks' => fn ($n) => $n->total_sks,
                'akademik', 'tahun_akademik' => fn ($n) => $n->akademik,
                default => fn ($n) => $n->akademik.$n->ganjil_genap,
            };

            $allNilai = $sortDirection === 'asc'
                ? $allNilai->sortBy($sortValue)
                : $allNilai->sortByDesc($sortValue);
        }

        if (empty($perPage)) {
            return $allNilai->values();
        }
        $currentPage = Paginator::resolveCurrentPage() ?: 1;

        return new LengthAwarePaginator(
            $allNilai->forPage($currentPage, $perPage)->values(),
            $allNilai->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => method_exists($this, 'paginatorPageName') ? $this->paginatorPageName() : 'page',
            ]
        );
    }
}
