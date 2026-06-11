<?php

namespace App\Models\Akademik;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataKuliah extends Model
{
    use SoftDeletes;

    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'level_mk', 'kode_mk', 'digit_semester', 'digit_mk',
        'nama_mk', 'semester', 'sks_kuliah', 'tipe_sks',
        'is_wajib', 'bahan_kajian', 'deskripsi',
    ];

    protected $appends = ['kode', 'kode_blok', 'mk', 'sks_tm', 'sks_pr', 'sks_pl', 'sks_sm', 'sks_text'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function prodis()
    {
        return $this->belongsToMany(Prodi::class, 'prodi_pivot_mk', 'mk_id', 'pr_id')
            ->withTrashed()
            ->withPivot('sort_order')
            ->orderBy('prodi_pivot_mk.sort_order', 'asc');
    }

    public function rps()
    {
        return $this->hasMany(RPS::class, 'mk_id');
    }

    // protected function tingkatanMode(): Attribute
    // {
    //     return Attribute::get(function () {
    //         return match ((int) $this->level_mk) {
    //             1 => 'mk-prodi',
    //             2 => 'mk-departemen',
    //             3 => 'mk-fakultas',
    //             4 => 'mk-universitas',
    //             default => 'mk',
    //         };
    //     });
    // }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $prefixDefault = $this->kode_mk ? strtoupper($this->kode_mk) : null;
            $prodi = $this->prodis->first();

            if ($prodi) {
                if ($this->level_mk == 1) { // Tingkat Prodi
                    $prefix = $prodi->kode_pr ?? $prodi->dp_rel?->kode_dp ?? $prodi->dp_rel?->fk_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->level_mk == 2) { // Tingkat Departemen
                    $prefix = $prodi->dp_rel?->kode_dp ?? $prodi->dp_rel?->fk_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->level_mk == 3) { // Tingkat Fakultas
                    $prefix = $prodi->dp_rel?->fk_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->level_mk == 4) { // Tingkat Universitas
                    $prefix = $prefixDefault ?? 'UNI';
                }
            } else {
                $prefix = $prefixDefault ?? 'UNI';
            }

            return $prefix.$this->digit_semester.$this->digit_mk;
        });
    }

    protected function kodeBlok(): Attribute
    {
        return Attribute::get(function () {
            $lastDigit = substr($this->digit_semester, -1);

            return match ($lastDigit) {
                '1', '2' => 1,
                '0' => 0,
                default => 1,
            };
        });
    }

    protected function ganjilGenap(): Attribute
    {
        return Attribute::get(function () {
            return $this->semester % 2 === 1 ? 'Ganjil' : 'Genap';
        });
    }

    protected function kodeSemester(): Attribute
    {
        return Attribute::get(function () {
            return $this->semester % 2 === 1 ? '01' : '02';
        });
    }

    // Helper untuk mengambil objek prodi pertama (Eager Loaded)
    // protected function getFirstProdi()
    // {
    //     // Menggunakan relationLoaded untuk mencegah N+1 Query jika belum di-load
    //     return $this->prodis->first();
    // }

    // protected function prodiId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->id);
    // }

    // protected function kodePr(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->kode_prodi);
    // }

    // protected function namaProdi(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->nama_pr);
    // }

    // // Data Departemen (Asumsi Prodi belongsTo Departemen)
    // protected function departemenId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->id);
    // }

    // protected function kodeDp(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->kode_departemen);
    // }

    // protected function namaDepartemen(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->nama_dp);
    // }

    // // Data Fakultas (Asumsi Departemen belongsTo Fakultas)
    // protected function fakultasId(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->fakultas?->id);
    // }

    // protected function kodeFk(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->fakultas?->kode_fakultas);
    // }

    // protected function namaFakultas(): Attribute
    // {
    //     return Attribute::get(fn () => $this->getFirstProdi()?->departemen?->fakultas?->nama_fk);
    // }

    protected function mk(): Attribute
    {
        return Attribute::get(fn () => $this->nama_mk);
    }

    // protected function semesterText(): Attribute
    // {
    //     return Attribute::get(fn () => 'Semester ' . $this->semester);
    // }

    protected function sks(): Attribute
    {
        return Attribute::get(fn () => $this->sks_kuliah);
    }

    // 0: Tatap Muka (TM)
    protected function sksTm(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 1 ? $this->sks_kuliah : null);
    }

    // 1: Praktikum (PR)
    protected function sksPr(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 2 ? $this->sks_kuliah : null);
    }

    // 2: Praktek Lapangan (PL)
    protected function sksPl(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 3 ? $this->sks_kuliah : null);
    }

    // 3: Simulasi (SM)
    protected function sksSm(): Attribute
    {
        return Attribute::get(fn () => $this->tipe_sks == 4 ? $this->sks_kuliah : null);
    }

    protected function sksText(): Attribute
    {
        return Attribute::get(function () {
            return match ((int) $this->tipe_sks) {
                1 => 'Tatap Muka',
                2 => 'Praktikum',
                3 => 'Praktek Lapangan',
                4 => 'Simulasi',
                0 => 'Teori',
                default => 'Tidak Diketahui',
            };
        });
    }

    protected function sksFull(): Attribute
    {
        return Attribute::get(function () {
            $sksPart = match ((int) $this->sks_text) {
                1 => 'Tatap Muka',
                2 => 'Praktikum',
                3 => 'Praktek Lapangan',
                4 => 'Simulasi',
                0 => 'Teori',
                default => 'Tidak Diketahui',
            };

            return $this->sks_kuliah.' SKS '.$sksPart;
        });
    }

    protected function semesterText(): Attribute
    {
        return Attribute::get(fn () => 'Semester '.$this->semester);
    }

    protected function wajib(): Attribute
    {
        return Attribute::get(fn () => $this->is_wajib);
    }

    protected function wajibText(): Attribute
    {
        return Attribute::get(fn () => $this->is_wajib == 1 ? 'Wajib' : 'Pilihan');
    }

    protected function createdDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->created_at) {
                return null;
            }

            return $this->created_at->translatedFormat('D, d M Y');
        });
    }

    protected function updatedDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->updated_at) {
                return null;
            }

            return $this->updated_at->translatedFormat('D, d M Y');
        });
    }

    public function scopeSearchMK($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';

        return $query->where(function ($q) use ($search, $searchTerm) {
            // 1. Cari Nama & Kode Manual
            $q->where('mata_kuliahs.nama_mk', 'like', $searchTerm);

            if (is_numeric($search)) {
                $q->orWhere('mata_kuliahs.id', 'like', $search);
            }

            // 4. Digit MK
            if (preg_match('/^\d+$/', $search)) {
                $q->orWhere('mata_kuliahs.digit_mk', $search);
            } else {
                $q->orWhere('mata_kuliahs.digit_mk', 'LIKE', $searchTerm);
            }

            // 7. Partial Code Search (Prefix & Digits)
            $cleanSearchUpper = strtoupper($search);
            if (preg_match('/[A-Z0-9]/', $cleanSearchUpper)) {
                $q->orWhere(function ($sq) use ($cleanSearchUpper) {
                    $prefixPart = preg_replace('/[^A-Z]/', '', $cleanSearchUpper);
                    $digitPart = preg_replace('/[^0-9]/', '', $cleanSearchUpper);

                    $sq->where(function ($sub) use ($prefixPart, $digitPart) {
                        if (! empty($prefixPart)) {
                            $sub->where(function ($low) use ($prefixPart) {
                                // 1. Cari langsung di Kode MK
                                $low->where('mata_kuliahs.kode_mk', 'like', $prefixPart.'%')

                                // 2. Tingkatan MK = 1 (Prodi): Cari di prodi, jika null ke departemen, jika null ke fakultas, dst.
                                    ->orWhere(function ($q) use ($prefixPart) {
                                        $q->where('mata_kuliahs.level_mk', 1)
                                            ->whereHas('prodis', function ($pro) use ($prefixPart) {
                                                $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                                                    ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                                    ->whereRaw("COALESCE(prodis.kode_pr, departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
                                            });
                                    })

                                // 3. Tingkatan MK = 2 (Departemen): Cari di departemen, jika null ke fakultas, dst.
                                    ->orWhere(function ($q) use ($prefixPart) {
                                        $q->where('mata_kuliahs.level_mk', 2)
                                            ->whereHas('prodis.dp_rel', function ($jur) use ($prefixPart) {
                                                $jur->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                                    ->whereRaw("COALESCE(departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
                                            });
                                    })

                                // 4. Tingkatan MK = 3 (Fakultas): Cari di fakultas, jika null ke 'UNI'
                                    ->orWhere(function ($q) use ($prefixPart) {
                                        $q->where('mata_kuliahs.level_mk', 3)
                                            ->whereHas('prodis.dp_rel.fk_rel', function ($fak) use ($prefixPart) {
                                                $fak->whereRaw("COALESCE(fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
                                            });
                                    })

                                // 5. Khusus tingkat Universitas (Tingkatan 4)
                                    ->when($prefixPart === 'UNI', function ($query) {
                                        $query->orWhere('mata_kuliahs.level_mk', 4);
                                    });
                            });
                        }

                        if (! empty($digitPart)) {
                            if (strlen($digitPart) <= 2) {
                                $sub->where('mata_kuliahs.digit_semester', 'like', $digitPart.'%');
                            } else {
                                $dSem = substr($digitPart, 0, 2);
                                $dMk = substr($digitPart, 2);
                                $sub->where('mata_kuliahs.digit_semester', 'like', $dSem.'%')
                                    ->where('mata_kuliahs.digit_mk', 'like', $dMk.'%');
                            }
                        }
                    });
                });
            }
        });
    }

    // public function scopeSearchMK($query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchTerm = '%'.$search.'%';
    //     $searchLower = '%'.strtolower($search).'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
    //         // 1. Cari Nama & Kode Manual
    //         $q->where('mata_kuliahs.nama_mk', 'like', $searchTerm)
    //             ->orWhere('mata_kuliahs.kode_mk', 'like', $searchTerm);

    //         if (is_numeric($search)) {
    //             $q->orWhere('mata_kuliahs.id', 'like', $search);
    //         }

    //         $q->orWhere(function ($dq) use ($searchLower, $searchTerm) {
    //             $dq->whereRaw("DATE_FORMAT(mata_kuliahs.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(mata_kuliahs.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.created_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.created_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("DATE_FORMAT(mata_kuliahs.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(mata_kuliahs.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.updated_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(mata_kuliahs.updated_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
    //         });

    //         // 2. Cari Semester (dengan Regex)
    //         $cleanSearch = $search;
    //         if (preg_match('/(?:s|sem|semester)\s*(\d+)/i', $search, $matches)) {
    //             $cleanSearch = $matches[1];
    //         }

    //         if (is_numeric($cleanSearch)) {
    //             $q->orWhere(function ($sub) use ($cleanSearch, $search) {
    //                 $sub->where('mata_kuliahs.id', $search)
    //                     ->orWhere('mata_kuliahs.semester', $cleanSearch);
    //             });
    //         } else {
    //             $searchLower = strtolower(trim($search));
    //             // ganjil
    //             if (
    //                 str_starts_with('ganjil', $searchLower) ||
    //                 str_starts_with('odd', $searchLower)
    //             ) {
    //                 $q->orWhereRaw('mata_kuliahs.semester % 2 = 1');
    //             }
    //             // genap
    //             if (
    //                 str_starts_with('genap', $searchLower) ||
    //                 str_starts_with('even', $searchLower)
    //             ) {
    //                 $q->orWhereRaw('mata_kuliahs.semester % 2 = 0');
    //             }
    //         }

    //         // 3. Wajib atau Pilihan
    //         if (strtolower($search) === 'wajib') {
    //             $q->orWhere('mata_kuliahs.is_wajib', 1);
    //         } elseif (strtolower($search) === 'pilihan') {
    //             $q->orWhere('mata_kuliahs.is_wajib', 0);
    //         }

    //         // 4. Digit MK
    //         if (preg_match('/^\d+$/', $search)) {
    //             $q->orWhere('mata_kuliahs.digit_mk', $search);
    //         } else {
    //             $q->orWhere('mata_kuliahs.digit_mk', 'LIKE', $searchTerm);
    //         }

    //         // 5. Tipe SKS
    //         $tipeMap = [
    //             'tm' => 1, 'tatap muka' => 1, 'teori' => 1,
    //             'pr' => 2, 'praktikum' => 2, 'praktek' => 2,
    //             'pl' => 3, 'praktek lapangan' => 3, 'lapangan' => 3,
    //             'sm' => 4, 'simulasi' => 4, 'studio' => 4,
    //         ];
    //         $searchLower = strtolower($search);
    //         if (array_key_exists($searchLower, $tipeMap)) {
    //             $q->orWhere('mata_kuliahs.tipe_sks', $tipeMap[$searchLower]);
    //         }

    //         // 6. SKS
    //         if (preg_match('/^(\d+(?:\.\d+)?) ?sks$/i', $search, $matches)) {
    //             $q->orWhere('mata_kuliahs.sks_kuliah', $matches[1]);
    //         }

    //         // 7. Partial Code Search (Prefix & Digits)
    //         $cleanSearchUpper = strtoupper($search);
    //         if (preg_match('/[A-Z0-9]/', $cleanSearchUpper)) {
    //             $q->orWhere(function ($sq) use ($cleanSearchUpper) {
    //                 $prefixPart = preg_replace('/[^A-Z]/', '', $cleanSearchUpper);
    //                 $digitPart = preg_replace('/[^0-9]/', '', $cleanSearchUpper);

    //                 $sq->where(function ($sub) use ($prefixPart, $digitPart) {
    //                     if (! empty($prefixPart)) {
    //                         $sub->where(function ($low) use ($prefixPart) {
    //                             // 1. Cari langsung di Kode MK
    //                             $low->where('mata_kuliahs.kode_mk', 'like', $prefixPart.'%')

    //                             // 2. Tingkatan MK = 1 (Prodi): Cari di prodi, jika null ke departemen, jika null ke fakultas, dst.
    //                                 ->orWhere(function ($q) use ($prefixPart) {
    //                                     $q->where('mata_kuliahs.level_mk', 1)
    //                                         ->whereHas('prodis', function ($pro) use ($prefixPart) {
    //                                             $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //                                                 ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
    //                                                 ->whereRaw("COALESCE(prodis.kode_pr, departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
    //                                         });
    //                                 })

    //                             // 3. Tingkatan MK = 2 (Departemen): Cari di departemen, jika null ke fakultas, dst.
    //                                 ->orWhere(function ($q) use ($prefixPart) {
    //                                     $q->where('mata_kuliahs.level_mk', 2)
    //                                         ->whereHas('prodis.dp_rel', function ($jur) use ($prefixPart) {
    //                                             $jur->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
    //                                                 ->whereRaw("COALESCE(departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
    //                                         });
    //                                 })

    //                             // 4. Tingkatan MK = 3 (Fakultas): Cari di fakultas, jika null ke 'UNI'
    //                                 ->orWhere(function ($q) use ($prefixPart) {
    //                                     $q->where('mata_kuliahs.level_mk', 3)
    //                                         ->whereHas('prodis.dp_rel.fk_rel', function ($fak) use ($prefixPart) {
    //                                             $fak->whereRaw("COALESCE(fakultas.kode_fk, 'UNI') LIKE ?", [$prefixPart.'%']);
    //                                         });
    //                                 })

    //                             // 5. Khusus tingkat Universitas (Tingkatan 4)
    //                                 ->when($prefixPart === 'UNI', function ($query) {
    //                                     $query->orWhere('mata_kuliahs.level_mk', 4);
    //                                 });
    //                         });
    //                     }

    //                     if (! empty($digitPart)) {
    //                         if (strlen($digitPart) <= 2) {
    //                             $sub->where('mata_kuliahs.digit_semester', 'like', $digitPart.'%');
    //                         } else {
    //                             $dSem = substr($digitPart, 0, 2);
    //                             $dMk = substr($digitPart, 2);
    //                             $sub->where('mata_kuliahs.digit_semester', 'like', $dSem.'%')
    //                                 ->where('mata_kuliahs.digit_mk', 'like', $dMk.'%');
    //                         }
    //                     }
    //                 });
    //             });
    //         }

    //         // 8. Silsilah (Prodi/Departemen/Fakultas)
    //         // $q->orWhereHas('prodis', function ($pq) use ($searchTerm) {
    //         //     $pq->where('nama_pr', 'like', $searchTerm)
    //         //         ->orWhere('kode_pr', 'like', $searchTerm)
    //         //         ->orWhereHas('dp_rel', function ($jq) use ($searchTerm) {
    //         //             $jq->where('nama_dp', 'like', $searchTerm)
    //         //                 ->orWhere('kode_dp', 'like', $searchTerm)
    //         //                 ->orWhereHas('fk_rel', function ($fq) use ($searchTerm) {
    //         //                     $fq->where('nama_fk', 'like', $searchTerm)
    //         //                         ->orWhere('kode_fk', 'like', $searchTerm);
    //         //                 });
    //         //         });
    //         // });
    //     });
    // }
}
