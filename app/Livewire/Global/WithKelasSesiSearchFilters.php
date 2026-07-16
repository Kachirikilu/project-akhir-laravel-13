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

    public function inputSesiFilter()
    {
        $search = trim($this->sesiSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->sesi_name)) {
            $this->sesiSearchResults = $this->mapSesiSearch(
                $this->sesiQuery()->searchKelasSesi($search)->limit(12)->get()
                // $this->searchOutputSesi($this->sesiQuery(), $search, 12)
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

    public function updatedSesiNameSearch($value)
    {
        $this->sesi_id = null;
        $this->sesi_items = null;
        $this->resetErrorBag(['sesi_id', 'sesiNameSearch']);

        $query = $this->sesiQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchKelasSesi($value)->limit(12)->get();
            // $results = $this->searchOutputSesi($query, $value, null, 12);
            $this->sesiResults = $this->mapSesi($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($c) use ($normalizedValue) {
                $normalizedSesiKode = str_replace(['-', ' '], '', strtolower($c->kode));

                return $normalizedSesiKode === $normalizedValue;
            });

            if ($exactMatch) {
                $this->sesi_id = $exactMatch->id;
                $this->sesi_items = $this->itemsSesi($exactMatch);
                $this->sesiNameSearch = $exactMatch->kode;
                $this->sesiResults = [];
            }
            if ($exactMatch) {
                if ($this->modeSesi == 'single') {
                    $this->sesiNameSearch = $exactMatch->kode;
                    $this->sesi_id = $exactMatch->id;
                    $this->sesi_items = $this->itemsSesi($exactMatch);
                    $this->sesiResults = [];
                } else {
                    $this->sesiNameSearch = '';
                    $this->sesi_id_array[] = $exactMatch->id;
                    $this->sesi_items_array[] = $this->itemsSesi($exactMatch);
                    $this->sesi_id_array = collect($this->sesi_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->sesi_items_array = collect($this->sesi_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $mappedResults = $this->mapSesi(collect([$exactMatch]));
                $this->pushToSesiItems($mappedResults);
                $this->sesiResults = $this->getSesibyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->sesiResults = $this->getSesibyUser();
            } else {
                $this->sesiResults = $this->mapSesi(
                    $query->orderBy('kelas_sesi.pertemuan_ke', 'desc')->limit(12)->get()
                );
            }
        }
    }

    public function getSesibyUser($mode = 'complex')
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
            ->whereHas('sesi.kelas_jadwals.kelas.rps.mk_rel.prodis', function ($q) use ($prodiId) {
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


    public function fetchSesi($mode = 'single')
    {
        $this->modeSesi = $mode;
        if ($this->sesi_id) {
            $sesi = KelasSesi::find($this->sesi_id);
            if ($sesi) {
                $this->sesiNameSearch = $sesi->kode;
                $this->sesi_items = $this->itemsSesi($sesi);
            }
            $this->sesiResults = $this->getSesibyUser();
            return;
        }
    }

    public function selectSesi($id, $sesiName)
    {
        $this->sesi_id = $id;
        $this->sesiNameSearch = $sesiName;
        $this->sesiResults = $this->getSesibyUser();

        $data = $this->sesiQuery()->find($id);
        if ($data) {
            $this->sesi_items = $this->itemsSesi($data);
            $mappedResults = $this->mapSesi(collect([$data]));
            $this->pushToSesiItems($mappedResults);
        }

        if (method_exists($this, 'fetchSesi')) {
            $this->fetchSesi();
        }

        $this->resetErrorBag(['sesi_id', 'sesiNameSearch']);
    }

    public function selectSesiArray($id)
    {
        $data = $this->sesiQuery()->find($id);
        if ($data && ! in_array($id, $this->sesi_id_array)) {
            $this->sesi_id_array[] = $id;
            $this->sesi_items_array[] = $this->itemsSesi($data);

            $mappedResults = $this->mapSesi(collect([$data]));
            $this->pushToSesiItems($mappedResults);
        }
    }

    public function resetSesiInput()
    {
        $this->reset(['sesi_id', 'sesi_items', 'sesiNameSearch']);
        $this->sesiResults = $this->getSesibyUser();
    }

    public function resetSesiArray()
    {
        $this->sesi_id_array = [];
        $this->sesi_items_array = [];
        $this->sesiNameSearch = '';
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

                    $absensi = ($s->total_absensi ?? 0).' / '.($s->count_mahasiswa ?? 0);
                    $matchAbsensi = $this->containsStrict(
                        $absensi,
                        $searchLower
                    ) || $this->matchOnlyCount(
                        $s->total_absensi ?? null,
                        $searchLower, ['mahasiswa', 'mhs', 'maha', 'absen', 'abs', 'absensi']
                    );

                    $absensiAll = ($s->total_absensi_all ?? 0).' / '.($s->count_mahasiswa ?? 0);
                    $matchAbsensiAll = $this->containsStrict(
                        $absensi,
                        $searchLower
                    ) || $this->matchOnlyCount(
                        $s->total_absensi_all ?? null,
                        $searchLower, ['mahasiswa', 'mhs', 'maha', 'absen', 'abs', 'absensi', 'absensi terdata', 'terdata']
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
                        || $matchAbsensiAll

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
                'total_absensi', 'absensi' => fn ($s) => $s->total_absensi,
                'total_absensi_all', 'absensi_all' => fn ($s) => $s->total_absensi_all,
                'tanggal_pelaksanaan' => fn ($s) => $s->tanggal_pelaksanaan,

                'bobot_normalisasi', 'bobot' => fn ($s) => $s->bobot_normalisasi,
                'tugas' => fn ($s) => $s->tugas,
                'w_tugas' => fn ($s) => $s->w_tugas,
                'w_mandiri' => fn ($s) => $s->w_mandiri,

                'created_at' => fn ($s) => $s->created_at,
                'updated_at' => fn ($s) => $s->updated_at,
                'id' => fn ($s) => $s->id,
                default => fn ($s) => $s->pertemuan_ke,
            };

            $allSesi = $sortDirection === 'asc'
                ? $allSesi->sortBy($sortValue)
                : $allSesi->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allSesi->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allSesi->forPage($currentPage, $perPage)->values(),
                $allSesi->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $querySesi;
        }

        return $querySesi->paginate($perPage);
    }
}
