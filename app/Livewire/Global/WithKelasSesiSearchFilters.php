<?php

namespace App\Livewire\Global;

use App\Models\Kelas\KelasSesi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithKelasSesiSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $sesiSearchQuery = '';

    public $sesiSearchResults = [];

    public $modeSesi = [];

    public $sesi_id = [];

    public $sesi_name = [];

    public $sesi_items = [];

    public $sesiNameSearch = [];

    public $sesiResults = [];

    public $selectedSesiId = [];

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $sesi_id_array = [];

    public $sesi_items_array = [];

    private function mapSesi($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->jadwal_rel->kode,
            'kode_rps' => $s->jadwal_rel->kode_rps,
            'prodi' => $s->jadwal_rel->prodi,
        ])->toArray();
    }

    private function mapSesiSearch($collection)
    {
        return $collection->map(fn ($s) => [
            'id' => $s->id,
            'kode' => $s->jadwal_rel->kode,
            'kode_text' => 'Kode: '.$s->jadwal_rel->kode,
            'kode_rps' => $s->jadwal_rel->rps_rel?->kode,
        ])->toArray();
    }

    private function sesiQuery()
    {
        return Sesi::query()->with('sesi.jadwal_rel.kelas_rel', 'sesi.jadwal_rel.kelas_rel.rps_rel', 'sesi.jadwal_rel.kelas_rel.mk_rel', 'sesi.jadwal_rel');
    }

    private function itemsSesi($s)
    {
        if (! $s) {
            return null;
        }

        return [
            'id' => $s->id,
            'kode' => $s->kode,
            'slot1' => $s->kode_rps,
            'slot2' => $s->prodi,
        ];
    }

    public function getSesiIdArrayForKey(string $key = 'default'): array
    {
        if (is_array($this->sesi_id_array) && array_key_exists($key, $this->sesi_id_array) && is_array($this->sesi_id_array[$key])) {
            return $this->sesi_id_array[$key];
        }

        return [];
    }

    public function getSesiNameSearchForKey(string $key = 'default'): string
    {
        if (is_array($this->sesiNameSearch) && array_key_exists($key, $this->sesiNameSearch)) {
            return is_string($this->sesiNameSearch[$key]) ? $this->sesiNameSearch[$key] : '';
        }

        return '';
    }

    public function inputSesiFilter()
    {
        $search = trim($this->sesiSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->sesi_name)) {
            $this->sesiSearchResults = $this->mapSesiSearch(
                // $this->sesiQuery()->searchSesi($search)->limit(12)->get()
                $this->searchOutputSesi($this->sesiQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->sesi_name) {
            $this->sesiSearchResults = $this->getSesibyUser('search');
        } else {
            $this->sesiSearchResults = [];
        }
    }

    public function resetSesiFilter()
    {
        $this->reset(['selectedSesiId', 'sesiSearchQuery', 'sesi_name', 'sesi_items']);
        $this->resetPage();
    }

    public function selectSesiForFilter($id)
    {
        $data = $this->sesiQuery()->find($id);

        if ($data) {
            $this->selectedSesiId = $id;
            $this->sesi_name = $data->kode;
            $this->sesiSearchQuery = $data->kode;
            $this->sesi_items = $this->itemsSesi($data);
            $this->sesiSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedSesiNameSearch($value, $name = null)
    {
        $key = 'default';

        if (is_string($name) && str_contains($name, '.')) {
            [, $key] = explode('.', $name, 2);
        } elseif (is_string($name) && $name !== 'sesiNameSearch') {
            $key = $name;
        }

        if (is_array($value)) {
            $value = $value[$key] ?? '';
        }

        $this->sesi_id[$key] = null;
        $this->sesi_items[$key] = null;
        $this->resetErrorBag(['sesi_id.'.$key, 'sesiNameSearch.'.$key]);

        $query = $this->sesiQuery();

        if (trim(strlen((string) $value)) > 0) {
            // $results = $query->searchSesi($value)->limit(12)->get();
            $results = $this->searchOutputSesi($query, $value, 12);
            $this->sesiResults[$key] = $this->mapSesi($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($sesi) use ($value, $normalizedValue) {
                $normalizedSesiKode = str_replace(['-', ' '], '', strtolower($sesi->kode));

                return $normalizedSesiKode === $normalizedValue;
            });

            if ($exactMatch) {
                $currentMode = $this->modeSesi[$key] ?? 'array';
                if ($currentMode == 'single') {
                    $this->sesiNameSearch[$key] = $exactMatch->kode;
                    $this->sesi_id[$key] = $exactMatch->id;
                    $this->sesi_items[$key] = $this->itemsSesi($exactMatch);
                    $this->sesiResults[$key] = [];
                } else {
                    $this->sesiNameSearch[$key] = '';
                    if (! isset($this->sesi_id_array[$key])) {
                        $this->sesi_id_array[$key] = [];
                    }
                    if (! isset($this->sesi_items_array[$key])) {
                        $this->sesi_items_array[$key] = [];
                    }
                    if (! in_array($exactMatch->id, $this->sesi_id_array[$key])) {
                        $this->sesi_id_array[$key][] = $exactMatch->id;
                        $this->sesi_items_array[$key][] = $this->itemsSesi($exactMatch);
                    }
                }
                $this->sesiResults[$key] = $this->getSesibyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->sesiResults[$key] = $this->getSesibyUser();
            } else {
                $this->sesiResults[$key] = $this->mapSesi(
                    $query->orderBy('sesis.id', 'desc')->limit(12)->get()
                );
            }
        }
    }

    // public function updatedSesiNameSearch($value, $key = 'default')
    // {
    //     // Pastikan index tersedia
    //     $this->sesi_id[$key] = null;
    //     $this->sesi_items[$key] = null;
    //     $this->resetErrorBag(['sesi_id.' . $key, 'sesiNameSearch.' . $key]);

    //     $query = $this->sesiQuery();

    //     if (trim(strlen($value)) > 0) {
    //         $results = $query->searchSesi($value)->limit(12)->get();
    //         $this->sesiResults[$key] = $this->mapSesi($results);

    //         // Cek Exact Match (Opsional)
    //         $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
    //         $exactMatch = $results->first(function ($sesi) use ($value, $normalizedValue) {
    //             $normalizedMkKode = str_replace(['-', ' '], '', strtolower($sesi->kode));
    //             return strtolower($sesi->kode) === strtolower($value)
    //                 || $normalizedMkKode === $normalizedValue;
    //         });

    //         if ($exactMatch) {
    //             $currentMode = $this->modeSesi[$key] ?? 'array';
    //             if ($currentMode == 'single') {
    //                 $this->selectSesi($exactMatch->id, $exactMatch->kode, $key);
    //             } else {
    //                 $this->selectSesiArray($exactMatch->id, $key);
    //                 $this->sesiNameSearch[$key] = ''; // Kosongkan search setelah add
    //             }
    //             $this->sesiResults[$key] = $this->getSesibyUser();
    //         }
    //     } else {
    //         $this->sesiResults[$key] = $this->getSesibyUser();
    //     }
    // }

    public function getSesibyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->sesiQuery();

        if (! $prodiId) {
            $defaultSesi = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapSesiSearch($defaultSesi)
                : $this->mapSesi($defaultSesi);
        }

        $mainResults = $query
            ->whereHas('sesi.rps.mk_rel.prodis', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = $this->sesiQuery()->whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('id', 'desc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapSesiSearch($mainResults)
            : $this->mapSesi($mainResults);
    }

    public function fetchSesi($query = '', $mode = 'single', $key = 'default')
    {
        $this->modeSesi[$key] = $mode;
        if (empty($query) || (! empty($this->sesi_id[$key]) || ! empty($this->sesi_id_array[$key]))) {
            $this->sesiResults[$key] = $this->getSesibyUser();
        }

    }

    public function selectSesi($id, $sesiName, $key = 'default')
    {
        $this->sesi_id[$key] = $id;
        $this->sesiNameSearch[$key] = $sesiName;
        $this->sesiResults[$key] = $this->getSesibyUser();

        $data = $this->sesiQuery()->find($id);
        if ($data) {
            $this->sesi_items[$key] = $this->itemsSesi($data);

            // if (property_exists($this, 'kode_cpmk') && $key == 'cpmk') {
            //     $this->kode_cpmk = $data->kode;
            // }
        }

        if (method_exists($this, 'fetchSesi')) {
            $this->fetchSesi('', $this->modeSesi[$key] ?? 'single', $key);
        }

        $this->resetErrorBag(['sesi_id.'.$key, 'sesiNameSearch.'.$key]);
    }

    public function selectSesiArray($id, $key = 'default')
    {
        $data = $this->sesiQuery()->find($id);
        if ($data) {
            if (! isset($this->sesi_id_array[$key])) {
                $this->sesi_id_array[$key] = [];
            }

            if (! in_array($id, $this->sesi_id_array[$key])) {
                $this->sesi_id_array[$key][] = $id;
                $this->sesi_items_array[$key][] = $this->itemsSesi($data);
            }

            // if (property_exists($this, 'kode_cpmk') && $key == 'cpmk') {
            //     $newDesc = trim($data->kode);
            //     if (!str_ends_with($newDesc, '.')) {
            //         $newDesc .= '.';
            //     }

            //     if (!empty($this->kode_cpmk)) {
            //         $this->kode_cpmk = rtrim($this->kode_cpmk) . ' ' . $newDesc;
            //     } else {
            //         $this->kode_cpmk = $newDesc;
            //     }
            // }
        }
    }

    public function resetSesiInput($key = 'default')
    {
        $this->reset(['sesi_id', 'sesi_items', 'sesiNameSearch']);
        $this->sesiResults[$key] = $this->getSesibyUser();

        // if (property_exists($this, 'kode_cpmk') && $key == 'cpmk') {
        //     $this->kode_cpmk = '';
        // }
    }

    public function resetSesiArray($key = 'default')
    {
        $this->sesi_id_array[$key] = [];
        $this->sesi_items_array[$key] = [];
        $this->sesiNameSearch[$key] = '';

        // if (property_exists($this, 'kode_cpmk') && $key == 'cpmk') {
        //     $this->kode_cpmk = '';
        // }
    }

    public function searchOutputSesi($querySesi, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allSesi = (clone $querySesi)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allSesi = $allSesi->filter(function ($s) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $s->id,
                        $searchLower
                    );

                    $matchPertemuan = $this->matchOnlyCount(
                        $s->pertemuan_ke ?? null,
                        $searchLower, ['pertemuan', 'pertemuan ke', 'pert', 'ke']
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | KODE
                    |--------------------------------------------------------------------------
                    */
                    $matchKode = $this->matchKode(
                        $s->kode,
                        $searchLower
                    );
                    $matchKodeSCPMK = $this->matchKode(
                        $s->kode_scpmk,
                        $searchLower
                    );
                    $matchKodeCPMK = $this->matchKode(
                        $s->kode_cpmk,
                        $searchLower
                    );

                    $matchMetode = $this->matchMetode(
                        $s->metode,
                        $searchLower
                    );

                    $matchHari = $this->containsStrict(
                        $s->hari,
                        $searchLower
                    );
                    $matchTanggal = $this->containsStrict(
                        $s->tanggal_pelaksanaan,
                        $searchLower
                    );
                    $matchJam = $this->containsStrict(
                        $s->jam_pelaksanaan,
                        $searchLower
                    );

                    $absensi = ($s->mhs_absensi ?? 0) . ' / ' . ($s->count_mahasiswa ?? 0);
                    $matchAbsensi = $this->containsStrict(
                        $absensi,
                        $searchLower
                    ) || $this->matchOnlyCount(
                        $s->mhs_absensi ?? null,
                        $searchLower, ['mahasiswa', 'mhs', 'maha', 'absen', 'abs', 'absensi']
                    );


                    $matchBobot = false;
                    if ($isNumericSearch) {
                        $matchBobot = $this->compareNumber(
                            (float) $s->bobot_normalisasi,
                            $searchLower
                        ) || $this->containsStrict(
                            $s->bobot_normalisasi,
                            $searchLower
                        );
                    }

                    $matchTugas = $this->containsStrict(
                        $s->tugas,
                        $searchLower
                    );
                    $matchWTugas = $this->matchCount(
                        $s->w_tugas,
                        $searchLower,
                        [
                            'min',
                            'menit',
                            'mnt',
                            'm/SKS',
                        ]
                    );
                    $matchWMandiri = $this->matchCount(
                        $s->w_mandiri,
                        $searchLower,
                        [
                            'min',
                            'menit',
                            'mnt',
                            'm/SKS',
                        ]
                    );


                    $matchCreatedAt = $this->matchDateField(
                        $s->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $s->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                        case 'metode':
                            return $matchMetode;
                    }

                    return
                        $matchID
                        || $matchKode
                        || $matchKodeSCPMK
                        || $matchKodeCPMK
                        || $matchMetode

                        || $matchPertemuan

                        || $matchHari
                        || $matchTanggal
                        || $matchJam
                        || $matchAbsensi

                        || $matchBobot
                        || $matchTugas
                        || $matchWTugas
                        || $matchWMandiri

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'kode' => fn ($s) => $s->kode,
                'kode_scpmk' => fn ($s) => $s->kode_scpmk,
                'kode_cpmk' => fn ($s) => $s->kode_cpmk,

                'metode' => fn ($s) => $s->metode,

                'pertemuan_ke' => fn ($s) => $s->pertemuan_ke,

                'hari_pelaksanaan' => fn ($s) => $s->hari,
                'jam_pelaksanaan' => fn ($s) => $s->jam_pelaksanaan,
                'jumlah_absensi', 'absensi' => fn ($s) => $s->mhs_absensi,
                'tanggal_pelaksanaan' => fn ($s) => $s->tanggal_pelaksanaan,

                'bobot_normalisasi', 'bobot' => fn ($s) => $s->bobot_normalisasi,
                'tugas' => fn ($s) => $s->tugas,
                'w_tugas' => fn ($s) => $s->w_tugas,
                'w_mandiri' => fn ($s) => $s->w_mandiri,

                'created_at' => fn ($s) => $s->created_at,
                'updated_at' => fn ($s) => $s->updated_at,

                default => fn ($s) => $s->id,
            };

            $allSesi = $sortDirection === 'asc'
                ? $allSesi->sortBy($sortValue)
                : $allSesi->sortByDesc($sortValue);

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allSesi->forPage($currentPage, $perPage)->values(),
                $allSesi->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return $querySesi->paginate($perPage);
    }
}
