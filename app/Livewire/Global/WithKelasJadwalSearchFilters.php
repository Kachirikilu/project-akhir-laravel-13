<?php

namespace App\Livewire\Global;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithKelasJadwalSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $jadwalSearchQuery = '';

    public $jadwalSearchResults = [];

    public $modeJadwal = [];

    public $jadwal_id = [];

    public $jadwal_name = [];

    public $jadwal_items = [];

    public $jadwalNameSearch = [];

    public $jadwalResults = [];

    public $selectedJadwalId = [];

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $jadwal_id_array = [];

    public $jadwal_items_array = [];

    private function mapJadwal($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'kode_rps' => $j->kode_rps,
            'prodi' => $j->prodi,
        ])->toArray();
    }

    private function mapJadwalSearch($collection)
    {
        return $collection->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'kode_text' => 'Kode: '.$j->kode,
            'kode_rps' => $j->kode_rps,
        ])->toArray();
    }

    private function jadwalQuery()
    {
        return Jadwal::query()->with('jadwal.kelas_rel', 'jadwal.kelas_rel.rps_rel', 'jadwal.kelas_rel.mk_rel', 'jadwal');
    }

    private function itemsJadwal($j)
    {
        if (! $j) {
            return null;
        }

        return [
            'id' => $j->id,
            'kode' => $j->kode,
            'slot1' => $j->kode_rps,
            'slot2' => $j->prodi,
        ];
    }

    public function inputJadwalFilter()
    {
        $search = trim($this->jadwalSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->jadwal_name)) {
            $this->jadwalSearchResults = $this->mapJadwalSearch(
                $this->jadwalQuery()->searchKelasJadwal($search)->limit(12)->get()
                // $this->searchOutputJadwal($this->jadwalQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->jadwal_name) {
            $this->jadwalSearchResults = $this->getJadwalbyUser('search');
        } else {
            $this->jadwalSearchResults = [];
        }
    }

    public function resetJadwalFilter()
    {
        $this->reset(['selectedJadwalId', 'jadwalSearchQuery', 'jadwal_name', 'jadwal_items']);
        $this->resetPage();
    }

    public function selectJadwalForFilter($id)
    {
        $data = $this->jadwalQuery()->find($id);

        if ($data) {
            $this->selectedJadwalId = $id;
            $this->jadwal_name = $data->kode;
            $this->jadwalSearchQuery = $data->kode;
            $this->jadwal_items = $this->itemsJadwal($data);
            $this->jadwalSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJadwalNameSearch($value)
    {
        $this->jadwal_id = null;
        $this->jadwal_items = null;
        $this->resetErrorBag(['jadwal_id', 'jadwalNameSearch']);

        $query = $this->jadwalQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchKelasJadwal($value)->limit(12)->get();
            // $results = $this->searchOutputJadwal($query, $value, null, 12);
            $this->jadwalResults = $this->mapJadwal($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($normalizedValue) {
                $normalizedJadwalKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return $normalizedJadwalKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->jadwal_id = $exactMatch->id;
                $this->jadwal_items = $this->itemsJadwal($exactMatch);
                $this->jadwalNameSearch = $exactMatch->deskripsi;
                $this->jadwalResults = [];
            }
            if ($exactMatch) {
                if ($this->modeJadwal == 'single') {
                    $this->jadwalNameSearch = $exactMatch->deskripsi;
                    $this->jadwal_id = $exactMatch->id;
                    $this->jadwal_items = $this->itemsJadwal($exactMatch);
                    $this->jadwalResults = [];
                } else {
                    $this->jadwalNameSearch = '';
                    $this->jadwal_id_array[] = $exactMatch->id;
                    $this->jadwal_items_array[] = $this->itemsJadwal($exactMatch);
                    $this->jadwal_id_array = collect($this->jadwal_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->jadwal_items_array = collect($this->jadwal_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapJadwal(collect([$exactMatch]));
                $this->pushToJadwalItems($mappedResults);
                $this->jadwalResults = $this->getJadwalbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->jadwalResults = $this->getJadwalbyUser();
            } else {
                $this->jadwalResults = $this->mapJadwal(
                    $query->orderBy('kelas_jadwals.tanggal_mulai', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getJadwalbyUser($mode = 'complex')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->jadwalQuery();

        if (! $prodiId) {
            $defaultJadwal = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapJadwalSearch($defaultJadwal)
                : $this->mapJadwal($defaultJadwal);
        }

        $mainResults = $query
            ->whereHas('kelas_jadwals.kelas.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->jadwalQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapJadwalSearch($mainResults)
            : $this->mapJadwal($mainResults);
    }

    public function fetchJadwal($query = '', $mode = 'single')
    {
        $this->modeJadwal = $mode;
        if (empty($query) || (! empty($this->jadwal_id) || ! empty($this->jadwal_id_array))) {
            $this->jadwalResults = $this->getJadwalbyUser();
        }

    }

    public function selectJadwal($id, $jadwalName)
    {
        $this->jadwal_id = $id;
        $this->jadwalNameSearch = $jadwalName;
        $this->jadwalResults = $this->getJadwalbyUser();

        $data = $this->jadwalQuery()->find($id);
        if ($data) {
            $this->jadwal_items = $this->itemsJadwal($data);
            $mappedResults = $this->mapJadwal(collect([$data]));
            $this->pushToJadwalItems($mappedResults);
        }

        if (method_exists($this, 'fetchJadwal')) {
            $this->fetchJadwal('');
        }

        $this->resetErrorBag(['jadwal_id', 'jadwalNameSearch']);
    }

    public function selectJadwalArray($id)
    {
        $data = $this->jadwalQuery()->find($id);
        if ($data && ! in_array($id, $this->jadwal_id_array)) {
            $this->jadwal_id_array[] = $id;
            $this->jadwal_items_array[] = $this->itemsJadwal($data);

            $mappedResults = $this->mapJadwal(collect([$data]));
            $this->pushToJadwalItems($mappedResults);
        }
    }

    public function resetJadwalInput()
    {
        $this->reset(['jadwal_id', 'jadwal_items', 'jadwalNameSearch']);
        $this->jadwalResults = $this->getJadwalbyUser();
    }

    public function resetJadwalArray()
    {
        $this->jadwal_id_array = [];
        $this->jadwal_items_array = [];
        $this->jadwalNameSearch = '';
    }

    public function searchOutputJadwal($queryJadwal, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allJadwal = (clone $queryJadwal)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allJadwal = $allJadwal->filter(function ($j) use ($searchLower, $mode) {
                    // $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    // $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $j->id,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $j->kode,
                        $searchLower
                    ) || $this->containsStrict(
                        $j->kode,
                        $searchLower
                    );
                    $matchKodeJadwal = $this->matchKode(
                        $j->kode_jadwal,
                        $searchLower
                    ) || $this->containsStrict(
                        $j->kode_jadwal,
                        $searchLower
                    );

                    $matchKodeRPS = $this->matchKode(
                        $j->kode_rps,
                        $searchLower
                    );
                    $matchKodeMK = $this->matchKode(
                        $j->kode_mk,
                        $searchLower
                    );

                    $matchKelas = $this->containsStrict(
                        $j->kelas_rel->kelas,
                        $searchLower
                    );
                    $matchRPS = $this->containsStrict(
                        $j->kelas_rel->rps_rel->rps,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Jadwal
                    |--------------------------------------------------------------------------
                    */
                    $matchPassword = false;
                    if (Auth::user()->admin || Auth::user()->dosen) {
                        $matchPassword = $this->containsStrict(
                            $j->password,
                            $searchLower
                        );
                    }

                    $matchLabel = $this->containsStrict(
                        $j->label_full,
                        $searchLower
                    ) || $this->containsStrict(
                        $j->label_kelas,
                        $searchLower
                    );

                    $matchKodeWly = $this->matchKode(
                        $j->kode_wilayah,
                        $searchLower
                    );
                    $matchHari = $this->containsStrict(
                        $j->hari,
                        $searchLower
                    );
                    $matchTanggal = $this->containsStrict(
                        $j->tanggal_pelaksanaan,
                        $searchLower
                    );
                    $matchJam = $this->containsStrict(
                        $j->jam_pelaksanaan,
                        $searchLower
                    );
                    $matchKapasitas = $this->containsStrict(
                        $j->kapasitas,
                        $searchLower
                    ) || $this->compareNumber(
                        (float) ($j->kapasitas ?? null),
                        $searchLower
                    ) || $this->matchOnlyCount(
                        $j->kapasitas ?? null,
                        $searchLower, ['kapasitas', 'kap', 'kapisa', 'kps']
                    );
                    $matchCountMhs = $this->containsStrict(
                        $j->count_mhs_jadwal,
                        $searchLower
                    );

                    $matchLabelExtra = $this->containsStrict(
                        $j->label_extra,
                        $searchLower
                    ) || $this->containsStrict(
                        $j->label_full,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Pencarian MK
                    |--------------------------------------------------------------------------
                    */
                    $matchMK = $this->containsStrict(
                        $j->mk,
                        $searchLower
                    );
                    /*
                    |--------------------------------------------------------------------------
                    | SEMESTER
                    |--------------------------------------------------------------------------
                    */
                    $semester = (int) ($j->semester ?? 0);
                    $matchSemester = $this->matchCount(
                        $semester,
                        $searchLower,
                        [
                            'sem',
                            'semester',
                            'semes',
                            'sms',
                            's'
                        ]
                    ) || $this->containsStrict(
                        'Semester'.$semester,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SKS
                    |--------------------------------------------------------------------------
                    */
                    $sks = (int) ($j->sks ?? 0);
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

                    $matchSKSText = $this->matchSKSText(
                        $j->sks_text,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | GANJIL / GENAP
                    |--------------------------------------------------------------------------
                    */
                    $matchSemesterJenis = $this->matchSemesterJenis(
                        $j->semester,
                        $searchLower
                    );

                    $matchWajib = $this->matchWajib(
                        $j->wajib_text,
                        $searchLower
                    );

                    $matchKodePr = $this->matchKode(
                        $j->kelas_rel->pr_rel->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $j->kelas_rel->pr_rel->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $j->kelas_rel->pr_rel->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $j->kelas_rel->pr_rel->prodi,
                        $j->kelas_rel->pr_rel->prodi_pr,
                        $j->kelas_rel->pr_rel->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$j->kelas_rel->pr_rel->kode_pr,
                            $pr.' ('.$j->kelas_rel->pr_rel->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $j->kelas_rel->pr_rel->departemen,
                        $j->kelas_rel->pr_rel->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$j->kelas_rel->pr_rel->kode_dp,
                            $dp.' ('.$j->kelas_rel->pr_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $j->kelas_rel->pr_rel->fakultas,
                        $j->kelas_rel->pr_rel->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$j->kelas_rel->pr_rel->kode_fk,
                            $fk.' ('.$j->kelas_rel->pr_rel->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchNo = $this->matchNo(
                        $j->kelas_rel->rps_rel->mk_rel->digit_mk,
                        $searchLower
                    );

                    $matchCreatedAt = $this->matchDateField(
                        $j->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $j->updated_at,
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
                        || $matchKodeJadwal
                        || $matchKodeRPS
                        || $matchKodeMK

                        || $matchLabel
                        || $matchLabelExtra
                        || $matchKodeWly
                        || $matchHari
                        || $matchTanggal
                        || $matchJam
                        || $matchKapasitas
                        || $matchCountMhs
                        || $matchPassword

                        || $matchNo

                        || $matchMK
                        || $matchSemester
                        || $matchSemesterJenis
                        || $matchSKS
                        || $matchSKSText

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
                'kode' => fn ($j) => $j->kode,
                'kode_rps' => fn ($j) => $j->kode_rps,
                'kode_mk' => fn ($j) => $j->kode_mk,

                'label_kelas' => fn ($j) => $j->label_full,
                'password' => fn ($j) => $j->password,
                'mk' => fn ($j) => $j->mk,

                'hari_pelaksanaan' => fn ($j) => $j->hari,
                'jam_pelaksanaan' => fn ($j) => $j->jam_pelaksanaan,
                'kapasitas' => fn ($j) => $j->count_mhs_jadwal,
                'tanggal_pelaksanaan' => fn ($j) => $j->tanggal_pelaksanaan,

                'kelas' => fn ($j) => $j->kelas,
                'prodi', 'program_studi' => fn ($j) => $j->prodi,

                'semester' => fn ($j) => $j->kelas_rel->semester,
                'sks' => fn ($j) => $j->kelas_rel->sks,
                'sks_text', 'pembelajaran' => fn ($j) => $j->kelas_rel->sks_text,

                // 'hari', 'hari_pelaksanaan' => fn ($j) =>
                //     optional(
                //         $j->jadwals
                //             ->sortBy(['label_kelas', 'kode_wilayah'])
                //             ->first()
                //     )->hari,
                // 'jam', 'jam_pelaksanaan' => fn ($j) =>
                //     optional(
                //         $j->jadwals
                //             ->sortBy(['label_kelas', 'kode_wilayah'])
                //             ->first()
                //     )->jam_pelaksanaan,
                // 'kapasitas' => fn ($j) =>
                //     optional(
                //         $j->jadwals
                //             ->sortBy(['label_kelas', 'kode_wilayah'])
                //             ->first()
                //     )->kapasitas,
                // 'tanggal', 'tanggal_pelaksanaan' => fn ($j) =>
                //     optional(
                //         $j->jadwals
                //             ->sortBy(['label_kelas', 'kode_wilayah'])
                //             ->first()
                //     )->tanggal_pelaksanaan,

                'is_wajib', 'wajib' => fn ($j) => $j->kelas_rel->wajib_text,

                'created_at' => fn ($j) => $j->created_at,
                'updated_at' => fn ($j) => $j->updated_at,

                default => fn ($j) => $j->id,
            };

            $allJadwal = $sortDirection === 'asc'
                ? $allJadwal->sortBy($sortValue)
                : $allJadwal->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allJadwal->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allJadwal->forPage($currentPage, $perPage)->values(),
                $allJadwal->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryJadwal;
        }

        return $queryJadwal->paginate($perPage);
    }
}
