<?php

namespace App\Livewire\Global;

use App\Models\Kelas\Kelas;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithKelasSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $kelasSearchQuery = '';

    public $kelasSearchResults = [];

    public $modeKelas = [];

    public $kelas_id = [];

    public $kelas_name = [];

    public $kelas_items = [];

    public $kelasNameSearch = [];

    public $kelasResults = [];

    public $selectedKelasId = [];

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $kelas_id_array = [];

    public $kelas_items_array = [];

    private function mapKelas($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'deskripsi' => $c->deskripsi,
            'rps' => $c->rps_rel?->rps,
            'kode_rps' => $c->rps_rel?->kode,
            'prodi' => $c->pr_rel?->prodi,
            'fakultas' => $c->pr_rel?->fakultasFk,
        ])->toArray();
    }

    private function mapKelasSearch($collection)
    {
        return $collection->map(fn ($c) => [
            'id' => $c->id,
            'kode' => $c->kode,
            'kode_text' => 'Kode: '.$c->kode,
            'rps' => $c->rps_rel?->rps,
            'kode_rps' => $c->rps_rel?->kode,
            'prodi' => $c->pr_rel?->prodi,
            'departemen' => $c->pr_rel?->departemenDp,
            'fakultas' => $c->pr_rel?->fakultasFk,
            'kode_pr' => $c->pr_rel?->kode,
            'deskripsi' => $c->deskripsi,
        ])->toArray();
    }

    private function kelasQuery()
    {
        return Kelas::query()->with('kelas.pr_rel', 'kelas.rps', 'kelas');
    }

    private function itemsKelas($c)
    {
        if (! $c) {
            return null;
        }

        return [
            'id' => $c->id,
            'kode' => $c->kode,
            'slot1' => $c->deskripsi,
            'slot2' => $c->kode_rps,
            'slot3' => $c->prodi,
        ];
    }

    public function inputKelasFilter()
    {
        $search = trim($this->kelasSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->kelas_name)) {
            $this->kelasSearchResults = $this->mapKelasSearch(
                $this->kelasQuery()->searchKelas($search)->limit(12)->get()
                // $this->searchOutputKelas($this->kelasQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->kelas_name) {
            $this->kelasSearchResults = $this->getKelasbyUser('search');
        } else {
            $this->kelasSearchResults = [];
        }
    }

    public function resetKelasFilter()
    {
        $this->reset(['selectedKelasId', 'kelasSearchQuery', 'kelas_name', 'kelas_items']);
        $this->resetPage();
    }

    public function selectKelasForFilter($id)
    {
        $data = $this->kelasQuery()->find($id);

        if ($data) {
            $this->selectedKelasId = $id;
            $this->kelas_name = $data->deskripsi;
            $this->kelasSearchQuery = $data->deskripsi;
            $this->kelas_items = $this->itemsKelas($data);
            $this->kelasSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedKelasNameSearch($value)
    {
        $this->kelas_id = null;
        $this->kelas_items = null;
        $this->resetErrorBag(['kelas_id', 'kelasNameSearch']);

        $query = $this->kelasQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchKelas($value)->limit(12)->get();
            // $results = $this->searchOutputKelas($query, $value, null, 12);
            $this->kelasResults = $this->mapKelas($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($value, $normalizedValue) {
                $normalizedKelasKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return strtolower($c->nama_kelas) === strtolower($value)
                    || $normalizedKelasKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->kelas_id = $exactMatch->id;
                $this->kelas_items = $this->itemsKelas($exactMatch);
                $this->kelasNameSearch = $exactMatch->deskripsi;
                $this->kelasResults = [];
            }
            if ($exactMatch) {
                if ($this->modeKelas == 'single') {
                    $this->kelasNameSearch = $exactMatch->deskripsi;
                    $this->kelas_id = $exactMatch->id;
                    $this->kelas_items = $this->itemsKelas($exactMatch);
                    $this->kelasResults = [];
                } else {
                    $this->kelasNameSearch = '';
                    $this->kelas_id_array[] = $exactMatch->id;
                    $this->kelas_items_array[] = $this->itemsKelas($exactMatch);
                    $this->kelas_id_array = collect($this->kelas_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->kelas_items_array = collect($this->kelas_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapKelas(collect([$exactMatch]));
                $this->pushToKelasItems($mappedResults);
                $this->kelasResults = $this->getKelasbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->kelasResults = $this->getKelasbyUser();
            } else {
                $this->kelasResults = $this->mapKelas(
                    $query->orderBy('kelas.nama_kelas', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getKelasbyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->kelasQuery();

        if (! $prodiId) {
            $defaultKelas = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapKelasSearch($defaultKelas)
                : $this->mapKelas($defaultKelas);
        }

        $mainResults = $query
            ->whereHas('kelas.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->kelasQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapKelasSearch($mainResults)
            : $this->mapKelas($mainResults);
    }

    public function fetchKelas($query = '', $mode = 'single')
    {
        $this->modeKelas = $mode;
        if (empty($query) || (! empty($this->kelas_id) || ! empty($this->kelas_id_array))) {
            $this->kelasResults = $this->getKelasbyUser();
        }

    }

    public function selectKelas($id, $kelasName)
    {
        $this->kelas_id = $id;
        $this->kelasNameSearch = $kelasName;
        $this->kelasResults = $this->getKelasbyUser();

        $data = $this->kelasQuery()->find($id);
        if ($data) {
            $this->kelas_items = $this->itemsKelas($data);
            $mappedResults = $this->mapKelas(collect([$data]));
            $this->pushToKelasItems($mappedResults);
        }

        if (method_exists($this, 'fetchKelas')) {
            $this->fetchKelas('');
        }

        $this->resetErrorBag(['kelas_id', 'kelasNameSearch']);
    }

    public function selectKelasArray($id)
    {
        $data = $this->kelasQuery()->find($id);
        if ($data && ! in_array($id, $this->kelas_id_array)) {
            $this->kelas_id_array[] = $id;
            $this->kelas_items_array[] = $this->itemsKelas($data);

            $mappedResults = $this->mapKelas(collect([$data]));
            $this->pushToKelasItems($mappedResults);
        }
    }

    public function resetKelasInput()
    {
        $this->reset(['kelas_id', 'kelas_items', 'kelasNameSearch']);
        $this->kelasResults = $this->getKelasbyUser();
    }

    public function resetKelasArray()
    {
        $this->kelas_id_array = [];
        $this->kelas_items_array = [];
        $this->kelasNameSearch = '';
    }

    public function searchOutputKelas($queryKelas, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allKelas = (clone $queryKelas)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allKelas = $allKelas->filter(function ($k) use ($searchLower, $mode) {
                    // $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    // $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $k->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $k->kode,
                        $searchLower
                    );
                    $matchKodeRPS = $this->matchKode(
                        $k->kode_rps,
                        $searchLower
                    );
                    $matchKodeMK = $this->matchKode(
                        $k->kode_mk,
                        $searchLower
                    );

                    $matchKelas = $this->containsStrict(
                        $k->kelas,
                        $searchLower
                    );
                    $matchRPS = $this->containsStrict(
                        $k->rps_rel->rps,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Jadwal
                    |--------------------------------------------------------------------------
                    */
                    $matchJadwals = $k->jadwals->contains(function ($j) use ($searchLower) {
                        return
                            $this->matchKode(
                                $j->kode,
                                $searchLower
                            )
                            ||
                            $this->containsStrict(
                                $j->kode,
                                $searchLower
                            )
                            ||
                            $this->containsStrict(
                                $j->label_full,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->label_kelas,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->kode_wilayah,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->hari,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->tanggal_pelaksanaan,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->jam_pelaksanaan,
                                $searchLower
                            )
                            || $this->containsStrict(
                                $j->kapasitas,
                                $searchLower
                            )
                            || $this->compareNumber(
                                (float) ($j->kapasitas ?? null),
                                $searchLower
                            )
                            || $this->matchOnlyCount(
                                $j->kapasitas ?? null,
                                $searchLower, ['kapasitas', 'kap', 'kapisa', 'kps']
                            ) || $this->containsStrict(
                                $j->count_mhs_jadwal,
                                $searchLower
                            );
                    });

                    /*
                    |--------------------------------------------------------------------------
                    | Pencarian MK
                    |--------------------------------------------------------------------------
                    */
                    $matchMK = $this->containsStrict(
                        $k->mk,
                        $searchLower
                    );
                    /*
                    |--------------------------------------------------------------------------
                    | SEMESTER
                    |--------------------------------------------------------------------------
                    */
                    $matchSemester = $this->matchCount(
                        $k->semester,
                        $searchLower,
                        [
                            'sem',
                            'semester',
                            'semes',
                            'sms',
                            's'
                        ]
                    ) || $this->containsStrict(
                        'Semester'.$k->semester,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKS
                    |--------------------------------------------------------------------------
                    */
                    $sks = (int) ($k->sks ?? 0);
                    $matchSKS = false;
                    if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                        $targetSKS = (int) max(
                            $matches[1] ?? 0,
                            $matches[2] ?? 0
                        );
                        $matchSKS = $sks === $targetSKS;
                    }
                    $matchSKS = $this->matchCount(
                        $k->sks,
                        $searchLower, ['sks']
                    ) || $this->containsStrict(
                        $k->sks.'SKS',
                        $searchLower
                    );

                    $matchSKSText = $this->matchSKSText(
                        $k->sks_text,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | GANJIL / GENAP
                    |--------------------------------------------------------------------------
                    */
                    $matchSemesterJenis = $this->matchSemesterJenis(
                        $k->semester,
                        $searchLower
                    );

                    $matchWajib = $this->matchWajib(
                        $k->wajib_text,
                        $searchLower
                    );

                    $matchKodePr = $this->matchKode(
                        $k->pr_rel->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $k->pr_rel->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $k->pr_rel->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $k->pr_rel->prodi,
                        $k->pr_rel->prodi_pr,
                        $k->pr_rel->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$k->pr_rel->kode_pr,
                            $pr.' ('.$k->pr_rel->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $k->pr_rel->departemen,
                        $k->pr_rel->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$k->pr_rel->kode_dp,
                            $dp.' ('.$k->pr_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $k->pr_rel->fakultas,
                        $k->pr_rel->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$k->pr_rel->kode_fk,
                            $fk.' ('.$k->pr_rel->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchNo = $this->matchNo(
                        $k->rps_rel->mk_rel->digit_mk,
                        $searchLower
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $k->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $k->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'nomor':
                            return $matchNo;
                        case 'semester':
                            return $matchSemester || $matchSemesterJenis;
                        case 'sks':
                            return $matchSKS || $matchSKSText;
                        case 'wajib':
                            return $matchWajib;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchKodeRPS
                        || $matchKodeMK

                        || $matchNo

                        || $matchMK
                        || $matchSemester
                        || $matchSemesterJenis
                        || $matchSKS
                        || $matchSKSText

                        || $matchJadwals
                        || $matchWajib

                        || $matchKodePr
                        || $matchKodeDp
                        || $matchKodeFk

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchKelas

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($k) => $k->kode,
                'kode_rps' => fn ($k) => $k->kode_rps,
                'kode_mk' => fn ($k) => $k->kode_mk,

                'kelas' => fn ($k) => $k->kelas,
                'prodi', 'program_studi' => fn ($k) => $k->prodi,

                'mk' => fn ($k) => $k->mk,
                'semester' => fn ($k) => $k->semester,
                'sks' => fn ($k) => $k->sks,
                'sks_text', 'pembelajaran' => fn ($k) => $k->sks_text,

                'hari', 'hari_pelaksanaan' => fn ($k) => optional(
                    $k->jadwals
                        ->sortBy(['label_kelas', 'kode_wilayah'])
                        ->first()
                )->hari,
                'jam', 'jam_pelaksanaan' => fn ($k) => optional(
                    $k->jadwals
                        ->sortBy(['label_kelas', 'kode_wilayah'])
                        ->first()
                )->jam_pelaksanaan,
                'kapasitas' => fn ($k) => optional(
                    $k->jadwals
                        ->sortBy(['label_kelas', 'kode_wilayah'])
                        ->first()
                )->count_mhs_jadwal,
                'tanggal', 'tanggal_pelaksanaan' => fn ($k) => optional(
                    $k->jadwals
                        ->sortBy(['label_kelas', 'kode_wilayah'])
                        ->first()
                )->tanggal_pelaksanaan,

                'is_wajib', 'wajib' => fn ($k) => $k->wajib_text,

                'created_at' => fn ($k) => $k->created_at,
                'updated_at' => fn ($k) => $k->updated_at,

                default => fn ($k) => $k->id,
            };

            $allKelas = $sortDirection === 'asc'
                ? $allKelas->sortBy($sortValue)
                : $allKelas->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allKelas->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allKelas->forPage($currentPage, $perPage)->values(),
                $allKelas->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryKelas;

        }

        return $queryKelas->paginate($perPage);
    }
}
