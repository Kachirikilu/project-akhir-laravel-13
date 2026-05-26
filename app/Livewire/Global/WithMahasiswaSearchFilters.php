<?php

namespace App\Livewire\Global;

use App\Models\Auth\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMahasiswaSearchFilters
{
    use WithPagination;

    public $mahasiswaSearchQuery = '';

    public $mahasiswaSearchResults = [];

    public $modeMahasiswa = '';

    public $mahasiswa_id;

    public $mahasiswa_name = '';

    public $mahasiswa_items;

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
        if ((strlen($search) > 1 || is_numeric($search)) && ! $this->mahasiswa_name) {
            $this->mahasiswaSearchResults = $this->mapMahasiswaSearch(
                $this->mahasiswaQuery()->searchMahasiswa($search)->limit(12)->get()
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

        // 1. Jalankan Query Pencarian Biasa (untuk filter dropdown mengetik nama/nim biasa)
        $results = $query->searchMahasiswa($value)->limit(12)->get();
        $this->mahasiswaResults = $this->mapMahasiswa($results);

        // 2. Deteksi Pola Multi / Parameter Acak
        $hasSemicolon = str_contains($value, ';');
        $searchClean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value));

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

        // KUNCI PERUBAHAN: Wajib mengandung ';' agar logic multi-search ini bisa dieksekusi
        $allowExecution = $hasSemicolon && ($angkatan || $kelas || $wilayah || $strata || $kodeProdi);

        if ($this->modeMahasiswa !== 'single' && $allowExecution) {
            $multiQuery = $this->mahasiswaQuery();
            $multiQuery->where(function ($q) use ($angkatan, $kelas, $wilayah, $strata, $kodeProdi) {
                if ($angkatan) {
                    $q->where('angkatan', $angkatan);
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

        // 3. Pencarian Exact Match Biasa (Jika input biasa nama/nim/email)
        // Bagian ini akan selalu berjalan mendeteksi NIM/Email secara real-time tanpa perlu tanda ';'
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
                if (! in_array($exactMatch->id, $this->mahasiswa_id_array ?? [])) {
                    $this->mahasiswa_id_array[] = $exactMatch->id;
                    $this->mahasiswa_items_array[] = $this->itemsMahasiswa($exactMatch);
                }
                $this->mahasiswaNameSearch = '';
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
}
