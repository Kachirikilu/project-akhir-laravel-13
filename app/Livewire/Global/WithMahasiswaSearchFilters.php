<?php

namespace App\Livewire\Global;

use App\Models\Auth\Mahasiswa;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMahasiswaSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $mahasiswaSearchQuery = '';

    public $mahasiswaSearchResults = [];

    public $modeMahasiswa = '';

    public $mahasiswa_id;

    public $mahasiswa_name = '';

    public $mahasiswa_items = [];

    public $mahasiswaNameSearch = '';

    public $mahasiswaResults = [];

    public $selectedMahasiswaId = null;

    public $mahasiswa_id_array = [];

    public $mahasiswa_items_array = [];

    private function mapMahasiswa($collection)
    {
        return $collection->map(fn ($m) => [
            'id' => $m->id,
            'kode' => $m->nim,
            'nidn' => $m->nidn ?? null,
            'nidk' => $m->nidk ?? null,
            'name' => $m->name,
            'prodi' => $m->pr_rel?->prodi,
            'wilayah' => $m->wilayah,
            'angkatan' => $m->angkatan,
            'angkatan_full' => $m->angkatan_full,
            'status' => $m->status,
            'status_full' => $m->status_full,
        ])->toArray();
    }

    private function mapMahasiswaSearch($collection)
    {
        return $collection->map(fn ($m) => [
            'id' => $m->id,
            'kode' => $m->nim,
            'nim_full' => 'NIM: '.$m->nim,
            'name' => $m->name,
            'prodi' => $m->pr_rel?->prodi,
            'angkatan' => $m->angkatan,
            'angkatan_full' => $m->angkatan_full,
            'status' => $m->status,
            'status_full' => $m->status_full,
        ])->toArray();
    }

    private function mahasiswaQuery()
    {
        return Mahasiswa::query()->with('user');
    }

    private function itemsMahasiswa($m)
    {
        if (! $m) {
            return null;
        }

        return [
            'id' => $m->id,
            'kode' => $m->nim,
            'slot1' => $m->name,
            'slot2' => $m->pr_rel?->prodi,
            'slot3' => $m->wilayah,
            'slot4' => $m->angkatan_full,
            'slot5' => $m->status_full,
        ];
    }

    public function inputMahasiswaFilter()
    {
        $search = trim($this->mahasiswaSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->mahasiswa_name)) {
            $this->mahasiswaSearchResults = $this->mapMahasiswaSearch(
                $this->mahasiswaQuery()->searchMahasiswa($search)->limit(12)->get()
                // $this->searchOutputMahasiswa($this->mahasiswaQuery(), $search, 12)
            );
        } elseif (empty($search) || $this->mahasiswa_name) {
            $this->mahasiswaSearchResults = $this->getMahasiswabyUser('search');
        } else {
            $this->mahasiswaSearchResults = [];
        }
    }

    public function resetMahasiswaFilter()
    {
        $this->reset(['selectedMahasiswaId', 'mahasiswaSearchQuery', 'mahasiswa_name', 'mahasiswa_items']);
        $this->resetPage();
    }

    public function selectMahasiswaForFilter($id)
    {
        $data = $this->mahasiswaQuery()->find($id);

        if ($data) {
            $this->selectedMahasiswaId = $id;
            $this->mahasiswa_name = $data->name;
            $this->mahasiswaSearchQuery = $data->name;
            $this->mahasiswa_items = $this->itemsMahasiswa($data);
            $this->mahasiswaSearchResults = [];
            $this->resetPage();
        }
    }

    // public function updatedMahasiswaNameSearch($value)
    // {
    //     $this->mahasiswa_id = null;
    //     $this->mahasiswa_items = null;
    //     $this->resetErrorBag(['mahasiswa_id', 'mahasiswaNameSearch']);

    //     $query = $this->mahasiswaQuery();

    //     if (trim(strlen($value)) > 0) {
    //         $results = $query->searchMahasiswa($value)->limit(12)->get();
    //         $this->mahasiswaResults = $this->mapMahasiswa($results);

    //         $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
    //         $exactMatch = $results->first(function ($d) use ($value, $normalizedValue) {
    //             $normalizedMahasiswaNIM = str_replace(['-', ' '], '', strtolower($d->nim));

    //             return strtolower($d->name) === strtolower($value)
    //                 || strtolower($d->user->email) === strtolower($value)
    //                 || $normalizedMahasiswaNIM === $normalizedValue;
    //         });

    //         if ($exactMatch) {
    //             if ($this->modeMahasiswa == 'single') {
    //                 $this->mahasiswaNameSearch = $exactMatch->name;
    //                 $this->mahasiswa_id = $exactMatch->id;
    //                 $this->mahasiswa_items = $this->itemsMahasiswa($exactMatch);
    //             } else {
    //                 $this->mahasiswaNameSearch = '';
    //                 $this->mahasiswa_id_array[] = $exactMatch->id;
    //                 $this->mahasiswa_items_array[] = $this->itemsMahasiswa($exactMatch);
    //             }
    //             $this->mahasiswaResults = $this->getMahasiswabyUser();
    //         }
    //     } else {
    //         if (Auth::user()->pr_id) {
    //             $this->mahasiswaResults = $this->getMahasiswabyUser();
    //         } else {
    //             $this->mahasiswaResults = $this->mapMahasiswa(
    //                 $query->orderBy('mahasiswas.name')->limit(12)->get()
    //             );
    //         }
    //     }
    // }

    public function updatedMahasiswaNameSearch($value)
    {
        $this->mahasiswa_id = null;
        $this->mahasiswa_items = null;
        $this->resetErrorBag(['mahasiswa_id', 'mahasiswaNameSearch']);

        $inputStr = str($value)->lower()->trim();
        if (empty($inputStr->toString())) {
            $this->mahasiswaResults = Auth::user()->pr_id ? $this->getMahasiswabyUser() : $this->mapMahasiswa($this->mahasiswaQuery()->orderBy('mahasiswas.name')->limit(12)->get());

            return;
        }

        $query = $this->mahasiswaQuery();
        // $results = $this->searchOutputMahasiswa($query, $value, 12);
        $results = $query->searchMahasiswa($value)->limit(12)->get();
        $this->mahasiswaResults = $this->mapMahasiswa($results);

        // 2. Deteksi Pola Multi / Parameter Acak
        $hasSemicolon = str_contains($value, ';');
        $searchClean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value));

        // --- PERBAIKAN 1: Deteksi kode NIM dilakukan di awal menggunakan $value asli ---
        $nimFilterCode = null;
        if (preg_match('/NIM(\d{3});/i', $value, $matches)) {
            $nimFilterCode = $matches[1];
        }

        preg_match('/(?=.*(\d{4}))/i', $searchClean, $mAngkatan);
        preg_match('/(?=.*\b([AB])\b)/i', preg_replace('/(S1|S2|S3)/', '', strtoupper($value)), $mKelas);
        preg_match('/(?=.*(IDL|PLG))/i', $searchClean, $mWilayah);
        preg_match('/(?=.*(S1|S2|S3|SARJANA|MAGISTER|DOKTOR))/i', $searchClean, $mStrata);

        $clearFormat = strtoupper(preg_replace('/[^A-Za-z0-9-]/', '', $value));
        $segments = explode('-', $clearFormat);
        $lastSegment = end($segments);
        $lastSegment = str_replace(';', '', $lastSegment);
        $kodeProdi = (preg_match('/^[A-Z]+$/i', $lastSegment) && ! in_array($lastSegment, ['A', 'B', 'IDL', 'PLG', 'S1', 'S2', 'S3'])) ? strtoupper($lastSegment) : null;

        $angkatan = $mAngkatan[1] ?? null;
        $kelas = $mKelas[1] ?? null;
        $wilayah = $mWilayah[1] ?? null;
        $strataRaw = $mStrata[1] ?? null;

        $strata = match ($strataRaw) {
            'S1', 'SARJANA' => 'Sarjana',
            'S2', 'MAGISTER' => 'Magister',
            'S3', 'DOKTOR' => 'Doktor',
            default => null,
        };

        $allowExecution = $hasSemicolon && ($angkatan || $kelas || $wilayah || $strata || $kodeProdi || $nimFilterCode);

        if ($this->modeMahasiswa !== 'single' && $allowExecution) {

            $multiQuery = $this->mahasiswaQuery();
            $multiQuery->where(function ($q) use ($angkatan, $kelas, $wilayah, $strata, $kodeProdi, $nimFilterCode) {
                if ($angkatan) {
                    $q->where('angkatan', $angkatan);
                }

                // Filter digit tengah NIM UNSRI (Karakter ke-5 sebanyak 3 digit)
                if ($nimFilterCode) {
                    $q->whereRaw('SUBSTRING(nim, 5, 3) = ?', [$nimFilterCode]);
                }

                if ($kelas === 'A') {
                    $q->whereRaw('RIGHT(nim, 1) % 2 != 0');
                }
                if ($kelas === 'B') {
                    $q->whereRaw('RIGHT(nim, 1) % 2 = 0');
                }
                if ($wilayah) {
                    $q->where('kode_wilayah', 'LIKE', "%{$wilayah}%");
                }
                if ($kodeProdi || $strata) {
                    $q->whereHas('pr_rel', function ($prq) use ($kodeProdi, $strata) {
                        if ($strata) {
                            $prq->where('strata', $strata);
                        }
                        if ($kodeProdi) {
                            $prq->where(function ($sub) use ($kodeProdi) {
                                $sub->whereRaw('UPPER(kode_pr) LIKE ?', ["%{$kodeProdi}%"])
                                    ->orWhereHas('dp_rel', function ($dpq) use ($kodeProdi) {
                                        $dpq->whereRaw('UPPER(kode_dp) LIKE ?', ["%{$kodeProdi}%"]);
                                    });
                            });
                        }
                    });
                }
            });

            $matchedMahasiswas = $multiQuery->get();
            if ($matchedMahasiswas->isNotEmpty()) {
                foreach ($matchedMahasiswas as $match) {
                    if (! in_array($match->id, $this->mahasiswa_id_array ?? [])) {
                        $this->mahasiswa_id_array[] = $match->id;
                        $this->mahasiswa_items_array[] = $this->itemsMahasiswa($match);
                    }
                }
                $this->mahasiswaNameSearch = '';
                $this->mahasiswaResults = $this->getMahasiswabyUser();

                return;
            }
        }
        $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
        $exactMatch = $results->first(function ($d) use ($value, $normalizedValue) {
            $normalizedMahasiswaNIM = str_replace(['-', ' '], '', strtolower($d->nim));

            return strtolower($d->name) === strtolower($value)
                || strtolower($d->user->email) === strtolower($value)
                || $normalizedMahasiswaNIM === $normalizedValue;
        });

        if ($exactMatch) {
            if ($this->modeMahasiswa == 'single') {
                $this->mahasiswaNameSearch = $exactMatch->name;
                $this->mahasiswa_id = $exactMatch->id;
                $this->mahasiswa_items = $this->itemsMahasiswa($exactMatch);
            } else {
                $this->mahasiswaNameSearch = '';
                if (! in_array($exactMatch->id, $this->mahasiswa_id_array ?? [])) {
                    $this->mahasiswa_id_array[] = $exactMatch->id;
                    $this->mahasiswa_items_array[] = $this->itemsMahasiswa($exactMatch);
                }
                $this->mahasiswa_id_array = collect($this->mahasiswa_id_array)
                    ->unique()
                    ->values()
                    ->all();
                $this->mahasiswa_items_array = collect($this->mahasiswa_items_array)
                    ->unique('id')
                    ->values()
                    ->all();
            }
            $this->mahasiswaResults = $this->getMahasiswabyUser();
        }
    }

    public function getMahasiswabyUser($mode = 'full')
    {
        $user = Auth::user();
        $prodiId = $user->pr_id ?? null;

        $query = $this->mahasiswaQuery();

        if (! $prodiId) {
            $defaultMahasiswa = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapMahasiswaSearch($defaultMahasiswa)
                : $this->mapMahasiswa($defaultMahasiswa);
        }

        $mainResults = $query
            ->whereHas('pr_rel', function ($q) use ($prodiId) {
                $q->where('prodis.id', $prodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = Mahasiswa::whereNotIn('id', $mainResults->pluck('id'))
                ->orderBy('name', 'asc')
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapMahasiswaSearch($mainResults)
            : $this->mapMahasiswa($mainResults);
    }

    public function fetchMahasiswa($query = '', $mode = 'single')
    {
        $this->modeMahasiswa = $mode;
        if (empty($query) || $this->mahasiswa_id) {
            $this->mahasiswaResults = $this->getMahasiswabyUser();
        }

    }

    public function selectMahasiswa($id, $mahasiswaName)
    {
        $this->mahasiswa_id = $id;
        $this->mahasiswaNameSearch = $mahasiswaName;
        $this->mahasiswaResults = $this->getMahasiswabyUser();

        $data = $this->mahasiswaQuery()->find($id);
        if ($data) {
            $this->mahasiswa_items = $this->itemsMahasiswa($data);
        }

        if (method_exists($this, 'fetchMahasiswa')) {
            $this->fetchMahasiswa('');
        }

        $this->resetErrorBag(['mahasiswa_id', 'mahasiswaNameSearch']);
    }

    public function selectMahasiswaArray($id)
    {
        $data = $this->mahasiswaQuery()->find($id);
        if ($data && ! in_array($id, $this->mahasiswa_id_array)) {
            $this->mahasiswa_id_array[] = $id;
            $this->mahasiswa_items_array[] = $this->itemsMahasiswa($data);
        }
    }

    public function resetMahasiswaInput()
    {
        $this->reset(['mahasiswa_id', 'mahasiswa_items', 'mahasiswaNameSearch']);
        $this->mahasiswaResults = $this->getMahasiswabyUser();
    }

    public function resetMahasiswaArray()
    {
        $this->mahasiswa_id_array = [];
        $this->mahasiswa_items_array = [];
        $this->mahasiswaNameSearch = '';
    }

    public function searchOutputMahasiswa($queryMahasiswa, $searchRaw, $perPage, $sortField = null, $sortDirection = 'asc')
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || $sortField) {

            $allMahasiswa = (clone $queryMahasiswa)->get();

            if (! empty($search)) {

                $mode = $this->detectSearchMode($searchLower);

                $allMahasiswa = $allMahasiswa->filter(function ($mahasiswa) use ($searchLower, $mode) {
                    $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    $isNumericSearch = is_numeric($number) && $number !== '';

                    $matchID = $this->matchID(
                        $mahasiswa->id,
                        $searchLower
                    );

                    $matchName = $this->containsStrict(
                        $mahasiswa->name,
                        $searchLower
                    );
                    $matchEmail = $this->containsStrict(
                        $mahasiswa->user->email,
                        $searchLower
                    );
                    $matchStatus = $this->containsStrict(
                        $mahasiswa->status,
                        $searchLower
                    );

                    $matchKampus = $this->containsStrict(
                        'Kampus '.$mahasiswa->kode_wilayah,
                        $searchLower
                    ) || $this->containsStrict(
                        'Kampus '.$mahasiswa->wilayah,
                        $searchLower
                    );

                    $matchNIM = $this->matchOnlyCount(
                        $mahasiswa->nim ?? null,
                        $searchLower, ['nim', 'id1', 'identity1']
                    ) || $this->containsStrict(
                        $mahasiswa->nim,
                        $searchLower
                    );
                    $matchNIK = $this->matchOnlyCount(
                        $mahasiswa->nik,
                        $searchLower, ['nik']
                    ) || $this->containsStrict(
                        $mahasiswa->nik,
                        $searchLower
                    );

                    $matchKodePr = $this->matchKode(
                        $mahasiswa->pr_rel->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $mahasiswa->pr_rel->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $mahasiswa->pr_rel->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $mahasiswa->pr_rel->prodi,
                        $mahasiswa->pr_rel->prodi_pr,
                        $mahasiswa->pr_rel->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$mahasiswa->pr_rel->kode_pr,
                            $pr.' ('.$mahasiswa->pr_rel->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $mahasiswa->pr_rel->departemen,
                        $mahasiswa->pr_rel->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$mahasiswa->pr_rel->kode_dp,
                            $dp.' ('.$mahasiswa->pr_rel->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $mahasiswa->pr_rel->fakultas,
                        $mahasiswa->pr_rel->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$mahasiswa->pr_rel->kode_fk,
                            $fk.' ('.$mahasiswa->pr_rel->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchCreatedAt = $this->matchDateField(
                        $mahasiswa->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $mahasiswa->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID;
                    }

                    return
                        $matchID
                        || $matchName
                        || $matchEmail
                        || $matchStatus

                        || $matchNIM
                        || $matchNIK

                        || $matchKodePr
                        || $matchKodeDp
                        || $matchKodeFk

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'name' => fn ($mahasiswa) => $mahasiswa->name,
                'email' => fn ($mahasiswa) => $mahasiswa->email,

                'nim' => fn ($mahasiswa) => $mahasiswa->nim ?? null,
                'nik' => fn ($mahasiswa) => $mahasiswa->nik ?? null,

                'status' => fn ($mahasiswa) => $mahasiswa->status ?? null,
                'prodi', 'program_studi' => fn ($mahasiswa) => $mahasiswa->pr_rel->prodi ?? null,

                'created_at' => fn ($mahasiswa) => $mahasiswa->created_at,
                'updated_at' => fn ($mahasiswa) => $mahasiswa->updated_at,

                default => fn ($mahasiswa) => $mahasiswa->id,
            };

            $allMahasiswa = $sortDirection === 'asc'
                ? $allMahasiswa->sortBy($sortValue)
                : $allMahasiswa->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allMahasiswa->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allMahasiswa->forPage($currentPage, $perPage)->values(),
                $allMahasiswa->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryMahasiswa;
        }

        return $queryMahasiswa->paginate($perPage);
    }
}
