<?php

namespace App\Livewire\Global;

use App\Models\Kelas\KelasJadwal;
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
            'kode_rps' => $j->rps_rel?->kode,
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

    public function getJadwalIdArrayForKey(string $key = 'default'): array
    {
        if (is_array($this->jadwal_id_array) && array_key_exists($key, $this->jadwal_id_array) && is_array($this->jadwal_id_array[$key])) {
            return $this->jadwal_id_array[$key];
        }

        return [];
    }

    public function getJadwalNameSearchForKey(string $key = 'default'): string
    {
        if (is_array($this->jadwalNameSearch) && array_key_exists($key, $this->jadwalNameSearch)) {
            return is_string($this->jadwalNameSearch[$key]) ? $this->jadwalNameSearch[$key] : '';
        }

        return '';
    }

    public function inputJadwalFilter()
    {
        $search = trim($this->jadwalSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->jadwal_name) {
            $this->jadwalSearchResults = $this->mapJadwalSearch(
                // $this->jadwalQuery()->searchJadwal($search)->limit(12)->get()
                $this->searchOutputJadwal($this->jadwalQuery(), $search, 12)
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
            $this->jadwal_name = $data->deskripsi;
            $this->jadwalSearchQuery = $data->deskripsi;
            $this->jadwal_items = $this->itemsJadwal($data);
            $this->jadwalSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedJadwalNameSearch($value, $name = null)
    {
        $key = 'default';

        if (is_string($name) && str_contains($name, '.')) {
            [, $key] = explode('.', $name, 2);
        } elseif (is_string($name) && $name !== 'jadwalNameSearch') {
            $key = $name;
        }

        if (is_array($value)) {
            $value = $value[$key] ?? '';
        }

        $this->jadwal_id[$key] = null;
        $this->jadwal_items[$key] = null;
        $this->resetErrorBag(['jadwal_id.'.$key, 'jadwalNameSearch.'.$key]);

        $query = $this->jadwalQuery();

        if (trim(strlen((string) $value)) > 0) {
            // $results = $query->searchJadwal($value)->limit(12)->get();
            $results = $this->searchOutputJadwal($query, $value, 12);
            $this->jadwalResults[$key] = $this->mapJadwal($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($jadwal) use ($value, $normalizedValue) {
                $normalizedJadwalKode = str_replace(['-', ' '], '', strtolower($jadwal->kode));

                return strtolower($jadwal->deskripsi) === strtolower($value)
                    || $normalizedJadwalKode === $normalizedValue;
            });

            if ($exactMatch) {
                $currentMode = $this->modeJadwal[$key] ?? 'array';
                if ($currentMode == 'single') {
                    $this->jadwalNameSearch[$key] = $exactMatch->deskripsi;
                    $this->jadwal_id[$key] = $exactMatch->id;
                    $this->jadwal_items[$key] = $this->itemsJadwal($exactMatch);
                    $this->jadwalResults[$key] = [];
                } else {
                    $this->jadwalNameSearch[$key] = '';
                    if (! isset($this->jadwal_id_array[$key])) {
                        $this->jadwal_id_array[$key] = [];
                    }
                    if (! isset($this->jadwal_items_array[$key])) {
                        $this->jadwal_items_array[$key] = [];
                    }
                    if (! in_array($exactMatch->id, $this->jadwal_id_array[$key])) {
                        $this->jadwal_id_array[$key][] = $exactMatch->id;
                        $this->jadwal_items_array[$key][] = $this->itemsJadwal($exactMatch);
                    }
                }
                $this->jadwalResults[$key] = $this->getJadwalbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->jadwalResults[$key] = $this->getJadwalbyUser();
            } else {
                $this->jadwalResults[$key] = $this->mapJadwal(
                    $query->orderBy('jadwals.id', 'desc')->limit(12)->get()
                );
            }
        }
    }

    // public function updatedJadwalNameSearch($value, $key = 'default')
    // {
    //     // Pastikan index tersedia
    //     $this->jadwal_id[$key] = null;
    //     $this->jadwal_items[$key] = null;
    //     $this->resetErrorBag(['jadwal_id.' . $key, 'jadwalNameSearch.' . $key]);

    //     $query = $this->jadwalQuery();

    //     if (trim(strlen($value)) > 0) {
    //         $results = $query->searchJadwal($value)->limit(12)->get();
    //         $this->jadwalResults[$key] = $this->mapJadwal($results);

    //         // Cek Exact Match (Opsional)
    //         $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
    //         $exactMatch = $results->first(function ($jadwal) use ($value, $normalizedValue) {
    //             $normalizedMkKode = str_replace(['-', ' '], '', strtolower($jadwal->kode));
    //             return strtolower($jadwal->deskripsi) === strtolower($value)
    //                 || $normalizedMkKode === $normalizedValue;
    //         });

    //         if ($exactMatch) {
    //             $currentMode = $this->modeJadwal[$key] ?? 'array';
    //             if ($currentMode == 'single') {
    //                 $this->selectJadwal($exactMatch->id, $exactMatch->deskripsi, $key);
    //             } else {
    //                 $this->selectJadwalArray($exactMatch->id, $key);
    //                 $this->jadwalNameSearch[$key] = ''; // Kosongkan search setelah add
    //             }
    //             $this->jadwalResults[$key] = $this->getJadwalbyUser();
    //         }
    //     } else {
    //         $this->jadwalResults[$key] = $this->getJadwalbyUser();
    //     }
    // }

    public function getJadwalbyUser($mode = 'full')
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
            ->whereHas('jadwal.rps.mk_rel.prodis', function ($q) use ($prodiId) {
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

    public function fetchJadwal($query = '', $mode = 'single', $key = 'default')
    {
        $this->modeJadwal[$key] = $mode;
        if (empty($query) || (! empty($this->jadwal_id[$key]) || ! empty($this->jadwal_id_array[$key]))) {
            $this->jadwalResults[$key] = $this->getJadwalbyUser();
        }

    }

    public function selectJadwal($id, $jadwalName, $key = 'default')
    {
        $this->jadwal_id[$key] = $id;
        $this->jadwalNameSearch[$key] = $jadwalName;
        $this->jadwalResults[$key] = $this->getJadwalbyUser();

        $data = $this->jadwalQuery()->find($id);
        if ($data) {
            $this->jadwal_items[$key] = $this->itemsJadwal($data);

            // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
            //     $this->deskripsi_cpmk = $data->deskripsi;
            // }
        }

        if (method_exists($this, 'fetchJadwal')) {
            $this->fetchJadwal('', $this->modeJadwal[$key] ?? 'single', $key);
        }

        $this->resetErrorBag(['jadwal_id.'.$key, 'jadwalNameSearch.'.$key]);
    }

    public function selectJadwalArray($id, $key = 'default')
    {
        $data = $this->jadwalQuery()->find($id);
        if ($data) {
            if (! isset($this->jadwal_id_array[$key])) {
                $this->jadwal_id_array[$key] = [];
            }

            if (! in_array($id, $this->jadwal_id_array[$key])) {
                $this->jadwal_id_array[$key][] = $id;
                $this->jadwal_items_array[$key][] = $this->itemsJadwal($data);
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

    public function resetJadwalInput($key = 'default')
    {
        $this->reset(['jadwal_id', 'jadwal_items', 'jadwalNameSearch']);
        $this->jadwalResults[$key] = $this->getJadwalbyUser();

        // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
        //     $this->deskripsi_cpmk = '';
        // }
    }

    public function resetJadwalArray($key = 'default')
    {
        $this->jadwal_id_array[$key] = [];
        $this->jadwal_items_array[$key] = [];
        $this->jadwalNameSearch[$key] = '';

        // if (property_exists($this, 'deskripsi_cpmk') && $key == 'cpmk') {
        //     $this->deskripsi_cpmk = '';
        // }
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
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

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
                    );
                    $matchKodeJadwal = $this->matchKode(
                        $j->kode_jadwal,
                        $searchLower
                    );
                    $matchKodeKelas = $this->matchKode(
                        $j->kode_kelas,
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
                    $matchSemester = false;
                    if ($isNumericSearch
                        && (
                            str_contains($searchLower, 'sem')
                            || str_contains($searchLower, 'semester')
                        )
                    ) {
                        $matchSemester = $semester === (int) $number;
                    }

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
                            $pr.' '.$j->kelas_rel->pr_rel->kode_dp,
                            $pr.' ('.$j->kelas_rel->pr_rel->kode_dp.')',
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
                        || $matchKodeKelas
                        || $matchKodeJadwal
                        || $matchKodeRPS
                        || $matchKodeMK

                        || $matchLabel
                        || $matchKodeWly
                        || $matchHari
                        || $matchTanggal
                        || $matchJam
                        || $matchKapasitas
                        || $matchCountMhs

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

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allJadwal->forPage($currentPage, $perPage)->values(),
                $allJadwal->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return $queryJadwal->paginate($perPage);
    }
}
