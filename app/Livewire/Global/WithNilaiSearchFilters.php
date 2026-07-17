<?php

namespace App\Livewire\Global;

use App\Models\Akademik\Nilai;
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

    public $modeNilai = '';

    public $nilai_id;

    public $nilai_name = '';

    public $nilai_items = [];

    public $nilaiNameSearch = '';

    public $nilaiResults = [];

    public $selectedNilaiId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $nilai_id_array = [];

    public $nilai_items_array = [];

    private function mapNilai($collection)
    {
        return $collection->map(fn ($n) => [
            'id' => $n->id,
            'kode' => $n->rps_rel?->kode,
            'rps' => $n->rps_rel?->rps,
            'mk' => $n->rps_rel?->mk_rel?->mk,
        ])->toArray();
    }

    private function nilaiQuery()
    {
        return Nilai::query()->with('rps_rel', 'rps_rel.mk_rel');
    }
    
    private function itemsNilai($n, ?string $customKode = null)
    {
        if (! $n) {
            return null;
        }
        return [
            'id' => $n->id,
            'kode' => $customKode ?: $n->kode,
            'slot1' => $n->rps,
            'slot2' => $n->mk,
        ];
    }


    public function inputNilaiFilter()
    {
        $search = trim($this->nilaiSearchQuery);

        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->nilai_name)) {
            $this->nilaiSearchResults = $this->mapNilai(
                $this->nilaiQuery()->searchNilai($search)->limit(12)->get()
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
            $this->nilai_name = $data->rps;
            $this->ynilaiSearchQuer = $data->rps;
            $this->nilai_items = $this->itemsNilai($data);
            $this->nilaiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedNilaiNameSearch($value)
    {
        $this->nilai_id = null;
        $this->nilai_items = null;
        $this->resetErrorBag(['nilai_id', 'nilaiNameSearch']);

        $query = $this->nilaiQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchNilai($value)->limit(12)->get();
            // $results = $this->searchOutputNilai($query, $value, null, 12);
            $this->nilaiResults = $this->mapNilai($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($value, $normalizedValue) {
                $normalizedNilaiKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return strtolower($c->rps) === strtolower($value)
                    || $normalizedNilaiKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->nilai_id = $exactMatch->id;
                $this->nilai_items = $this->itemsNilai($exactMatch);
                $this->nilaiNameSearch = $exactMatch->rps;
                $this->nilaiResults = [];
            }
            if ($exactMatch) {
                if ($this->modeNilai == 'single') {
                    $this->nilaiNameSearch = $exactMatch->rps;
                    $this->nilai_id = $exactMatch->id;
                    $this->nilai_items = $this->itemsNilai($exactMatch);
                    $this->nilaiResults = [];
                } else {
                    $this->nilaiNameSearch = '';
                    $this->nilai_id_array[] = $exactMatch->id;
                    $this->nilai_items_array[] = $this->itemsNilai($exactMatch);
                    $this->nilai_id_array = collect($this->nilai_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->nilai_items_array = collect($this->nilai_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapNilai(collect([$exactMatch]));
                $this->pushToNilaiItems($mappedResults);
                $this->nilaiResults = $this->getNilaibyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->nilaiResults = $this->getNilaibyUser();
            } else {
                $this->nilaiResults = $this->mapNilai(
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
                $this->nilaiNameSearch = $nilai->rps;
                $this->nilai_items = $this->itemsNilai($nilai);
            }
            $this->nilaiResults = $this->getNilaibyUser();
            return;
        }
    }

    public function selectNilai($id, $nilaiName)
    {
        $this->nilai_id = $id;
        $this->nilaiNameSearch = $nilaiName;
        $this->nilaiResults = $this->getNilaibyUser();

        $data = $this->nilaiQuery()->find($id);
        if ($data) {
            $this->nilai_items = $this->itemsNilai($data);
            $mappedResults = $this->mapNilai(collect([$data]));
            $this->pushToNilaiItems($mappedResults);
        }

        if (method_exists($this, 'fetchNilai')) {
            $this->fetchNilai();
        }

        $this->resetErrorBag(['nilai_id', 'nilaiNameSearch']);
    }

    public function selectNilaiArray($id)
    {
        $data = $this->nilaiQuery()->find($id);
        if ($data && ! in_array($id, $this->nilai_id_array)) {
            $this->nilai_id_array[] = $id;
            $this->nilai_items_array[] = $this->itemsNilai($data);

            $mappedResults = $this->mapNilai(collect([$data]));
            $this->pushToNilaiItems($mappedResults);
        }
    }

    public function resetNilaiInput()
    {
        $this->reset(['nilai_id', 'nilai_items', 'nilaiNameSearch']);
        $this->nilaiResults = $this->getNilaibyUser();
    }

    public function resetNilaiArray()
    {
        $this->nilai_id_array = [];
        $this->nilai_items_array = [];
        $this->nilaiNameSearch = '';
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
                'akademik', 'akademik' => fn ($n) => $n->akademik,
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
