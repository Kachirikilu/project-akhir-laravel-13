<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'rps_id',
        'kj_id',
        'nilai',
        'ganjil_genap',
        'akademik',
        'bobot_array',
        'is_locked',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_array' => 'array',
        'bobot_array' => 'array',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }

    public function jadwal_rel(): BelongsTo
    {
        return $this->belongsTo(
            KelasJadwal::class,
            'kj_id'
        );
    }

    public function rps_rel(): BelongsTo
    {
        return $this->belongsTo(
            RPS::class,
            'rps_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function hitungNilaiAkhir(): float
    {
        $nilai = $this->nilai_array ?? [];
        $bobot = $this->bobot_array ?? [];
        $total = 0;

        foreach ($nilai as $index => $n) {
            $n = (float) ($n ?? 0);
            $b = (float) ($bobot[$index] ?? 0);
            $total += $n * $b;
        }

        return round($total, 2);
    }

    // protected function nilaIpk(): Attribute
    // {
    //     return Attribute::get(function () {
    //         $nilai = (float) ($this->nilai ?? 0);
    //         $indeksRasio = ($nilai / 100) * 4;
    //         return number_format($indeksRasio, 2);
    //     });
    // }

    protected function nilaiMutu(): Attribute
    {
        return Attribute::get(function () {

            $nilai = $this->nilai;

            return match (true) {
                $nilai >= 85 => 'A',
                $nilai >= 80 => 'A-',
                $nilai >= 75 => 'B+',
                $nilai >= 70 => 'B',
                $nilai >= 65 => 'B-',
                $nilai >= 60 => 'C+',
                $nilai >= 55 => 'C',
                $nilai >= 40 => 'D',
                default => 'E',
            };
        });
    }

    protected function nilaiIndex(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->nilai_mutu) {
                'A' => 4.00,
                'A-' => 3.70,
                'B+' => 3.30,
                'B' => 3.00,
                'B-' => 2.70,
                'C+' => 2.30,
                'C' => 2.00,
                'D' => 1.00,
                default => 0.00,
            };
        });
    }

    protected function kodeMk(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk_rel?->kode);
    }

    protected function kodeRps(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->kode);
    }

    protected function levelMk(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk_rel?->level_mk);
    }

    protected function digitMk(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk_rel?->digit_mk);
    }

    protected function mk(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk_rel?->mk);
    }

    protected function sks(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk_rel?->sks_kuliah);
    }

    protected function kodeCpmkArray(): Attribute
    {
        return Attribute::get(function () {

            return collect($this->mapping_pertemuan)
                ->pluck('kode_cpmk')
                ->values()
                ->toArray();
        });
    }

    protected function kodeScpmkArray(): Attribute
    {
        return Attribute::get(function () {

            return collect($this->mapping_pertemuan)
                ->pluck('kode_scpmk')
                ->values()
                ->toArray();
        });
    }

    protected function metodeArray(): Attribute
    {
        return Attribute::get(function () {

            return collect($this->mapping_pertemuan)
                ->pluck('metode')
                ->values()
                ->toArray();
        });
    }

    protected function bobotRpsArray(): Attribute
    {
        return Attribute::get(function () {
            $mapping = collect($this->mapping_pertemuan);
            $totalBobot = $mapping->sum('bobot');
            if ($totalBobot <= 0) {
                return $mapping->pluck('bobot')->values()->toArray();
            }

            return $mapping->map(function ($item) use ($totalBobot) {
                $bobotNormalisasi = $item['bobot'] / $totalBobot;

                return round($bobotNormalisasi, 4);
            })
                ->values()
                ->toArray();
        });
    }

    protected function bobotCpmkArray(): Attribute
    {
        return Attribute::get(function () {
            $mapping = collect($this->mapping_pertemuan);

            $totalBobotPerCpmk = $mapping->groupBy('kode_cpmk')
                ->map(fn ($group) => $group->sum('bobot'));

            return $mapping->map(function ($item) use ($totalBobotPerCpmk) {
                $cpmk = $item['kode_cpmk'];
                $totalCpmkBobot = $totalBobotPerCpmk[$cpmk] ?? 0;

                if ($totalCpmkBobot <= 0) {
                    return 0;
                }

                $bobotNormalisasiCpmk = $item['bobot'] / $totalCpmkBobot;

                return round($bobotNormalisasiCpmk, 4);
            })
                ->values()
                ->toArray();
        });
    }

    // protected function mappingPertemuan(): Attribute
    // {
    //     return Attribute::get(function () {

    //         $rps = $this->rps_rel;

    //         if (! $rps) {
    //             return [];
    //         }

    //         $allScpmk = $rps->cpmks
    //             ->flatMap(function ($cpmk) {
    //                 return $cpmk->scpmks->map(function ($scpmk) use ($cpmk) {

    //                     $scpmk->parent_kode_cpmk = $cpmk->kode;

    //                     return $scpmk;
    //                 });
    //             })
    //             ->values();

    //         $getNearestCpmk = function (int $sourceIndex) use ($allScpmk) {
    //             for ($i = $sourceIndex - 1; $i >= 0; $i--) {
    //                 if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
    //                     return $allScpmk[$i]->parent_kode_cpmk;
    //                 }
    //             }
    //             for ($i = $sourceIndex; $i < $allScpmk->count(); $i++) {
    //                 if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
    //                     return $allScpmk[$i]->parent_kode_cpmk;
    //                 }
    //             }

    //             return '-';
    //         };

    //         $hasUts = $allScpmk->contains(function ($item) {
    //             return Str::contains(
    //                 $item->deskripsi ?? '',
    //                 SubCPMK::$UTS_FIELDS,
    //                 ignoreCase: true
    //             ) || Str::contains(
    //                 $item->metode ?? '',
    //                 SubCPMK::$UTS_FIELDS,
    //                 ignoreCase: true
    //             );
    //         });

    //         $hasUas = $allScpmk->contains(function ($item) {
    //             return Str::contains(
    //                 $item->deskripsi ?? '',
    //                 SubCPMK::$UAS_FIELDS,
    //                 ignoreCase: true
    //             ) || Str::contains(
    //                 $item->metode ?? '',
    //                 SubCPMK::$UAS_FIELDS,
    //                 ignoreCase: true
    //             );
    //         });

    //         $result = [];
    //         $sourceIndex = 0;

    //         for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {

    //             // UTS otomatis
    //             if (! $hasUts && $pertemuan == 8) {
    //                 $result[] = [
    //                     'kode_scpmk' => 'UTS',
    //                     'kode_cpmk' => $getNearestCpmk($sourceIndex),
    //                     'metode' => 'UTS',
    //                     'bobot' => $rps->bobot_uts / 100,
    //                 ];

    //                 continue;
    //             }
    //             // UAS otomatis
    //             if (! $hasUas && $pertemuan == 16) {
    //                 $result[] = [
    //                     'kode_scpmk' => 'UAS',
    //                     'kode_cpmk' => $getNearestCpmk($sourceIndex),
    //                     'metode' => 'UAS',
    //                     'bobot' => $rps->bobot_uas / 100,
    //                 ];

    //                 continue;
    //             }

    //             $scpmk = $allScpmk->get($sourceIndex);

    //             $result[] = [
    //                 'kode_scpmk' => $scpmk?->kode ?? '-',
    //                 'kode_cpmk' => $getNearestCpmk($sourceIndex),
    //                 'metode' => $scpmk?->metode ?? '-',
    //                 'bobot' => ($scpmk?->bobot ?? 0) / 100,
    //             ];

    //             $sourceIndex++;
    //         }

    //         return $result;
    //     });
    // }

    protected function mappingPertemuan(): Attribute
    {
        return Attribute::get(function () {
            $rps = $this->rps_rel;

            if (! $rps) {
                return [];
            }

            // 1. Ambil semua Sub-CPMK secara berurutan sesuai kurikulum
            $allScpmk = $rps->cpmks
                ->flatMap(function ($cpmk) {
                    return $cpmk->scpmks->map(function ($scpmk) use ($cpmk) {
                        $scpmk->parent_kode_cpmk = $cpmk->kode;

                        return $scpmk;
                    });
                })
                ->values();

            // 2. Ambil keywords filter dari .env
            $envUtsFields = env('UTS_FIELDS', 'UTS,EVALUASI AWAL');
            $envUasFields = env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK');

            $utsFields = array_map('trim', explode(',', $envUtsFields));
            $uasFields = array_map('trim', explode(',', $envUasFields));

            // 3. Deteksi apakah RPS sudah punya baris UTS/UAS bawaan
            $hasUts = $allScpmk->contains(function ($item) use ($utsFields) {
                $text = ($item->deskripsi ?? '').' '.($item->metode ?? '');

                return \Str::contains($text, $utsFields, ignoreCase: true);
            });

            $hasUas = $allScpmk->contains(function ($item) use ($uasFields) {
                $text = ($item->deskripsi ?? '').' '.($item->metode ?? '');

                return \Str::contains($text, $uasFields, ignoreCase: true);
            });

            // 4. CLOSURE: Mencari CPMK terdekat berdasarkan posisi index saat ini
            $getNearestCpmk = function (int $currentIndex) use ($allScpmk) {
                for ($i = $currentIndex - 1; $i >= 0; $i--) {
                    if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
                        return $allScpmk[$i]->parent_kode_cpmk;
                    }
                }
                for ($i = $currentIndex; $i < $allScpmk->count(); $i++) {
                    if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
                        return $allScpmk[$i]->parent_kode_cpmk;
                    }
                }

                return '-';
            };

            $result = [];
            $sourceIndex = 0;

            // 5. Loop tepat 16 kali
            for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {

                // A. INJEKSI SLOT UTS OTOMATIS (Slot ke-8)
                if (! $hasUts && $pertemuan == 8) {
                    $result[] = [
                        'no_pertemuan' => $pertemuan, // 🌟 Flag nomor pertemuan
                        'is_evaluasi' => 'UTS',      // 🌟 Flag penanda UTS
                        'kode_scpmk' => 'UTS',
                        'kode_cpmk' => $getNearestCpmk($sourceIndex),
                        'metode' => 'UTS',
                        'bobot' => $rps->bobot_uts / 100,
                    ];

                    continue;
                }

                // B. INJEKSI SLOT UAS OTOMATIS (Slot ke-16)
                if (! $hasUas && $pertemuan == 16) {
                    $result[] = [
                        'no_pertemuan' => $pertemuan,
                        'is_evaluasi' => 'UAS',      // 🌟 Flag penanda UAS
                        'kode_scpmk' => 'UAS',
                        'kode_cpmk' => $getNearestCpmk($sourceIndex),
                        'metode' => 'UAS',
                        'bobot' => $rps->bobot_uas / 100,
                    ];

                    continue;
                }

                // C. PERTEMUAN BIASA
                $scpmk = $allScpmk->get($sourceIndex);

                // Cek apakah sub-CPMK bawaan DB ini sebenarnya adalah UTS/UAS murni dari teksnya
                $evalType = null;
                if ($scpmk) {
                    $fullText = ($scpmk->deskripsi ?? '').' '.($scpmk->metode ?? '');
                    if (\Str::contains($fullText, $utsFields, ignoreCase: true)) {
                        $evalType = 'UTS';
                    } elseif (\Str::contains($fullText, $uasFields, ignoreCase: true)) {
                        $evalType = 'UAS';
                    }
                }

                $result[] = [
                    'no_pertemuan' => $pertemuan,
                    'is_evaluasi' => $evalType, // 🌟 Bernilai 'UTS', 'UAS', atau null jika materi biasa
                    'kode_scpmk' => $scpmk?->kode ?? '-',
                    'kode_cpmk' => $scpmk?->parent_kode_cpmk ?? $getNearestCpmk($sourceIndex),
                    'metode' => $scpmk?->metode ?? '-',
                    'bobot' => ($scpmk?->bobot ?? 0) / 100,
                ];

                $sourceIndex++;
            }

            return $result;
        });
    }

    // protected function mappingPertemuan(): Attribute
    // {
    //     return Attribute::get(function () {
    //         $rps = $this->rps_rel;

    //         if (! $rps) {
    //             return [];
    //         }

    //         // 1. Ambil semua Sub-CPMK secara sekuensial
    //         $allScpmk = $rps->cpmks
    //             ->flatMap(function ($cpmk) {
    //                 return $cpmk->scpmks->map(function ($scpmk) use ($cpmk) {
    //                     $scpmk->parent_kode_cpmk = $cpmk->kode;

    //                     return $scpmk;
    //                 });
    //             })
    //             ->values();

    //         // 🌟 2. AMBIL KEYWORDS DARI .ENV DAN KONVERSI MENJADI ARRAY
    //         $envUtsFields = env('UTS_FIELDS', 'UTS,EVALUASI AWAL');
    //         $envUasFields = env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK');

    //         $utsFields = array_map('trim', explode(',', $envUtsFields));
    //         $uasFields = array_map('trim', explode(',', $envUasFields));

    //         // 3. Deteksi keberadaan UTS/UAS berdasarkan teks di deskripsi atau metode
    //         $hasUts = $allScpmk->contains(function ($item) use ($utsFields) {
    //             $text = ($item->deskripsi ?? '').' '.($item->metode ?? '');

    //             return \Str::contains($text, $utsFields, ignoreCase: true);
    //         });

    //         $hasUas = $allScpmk->contains(function ($item) use ($uasFields) {
    //             $text = ($item->deskripsi ?? '').' '.($item->metode ?? '');

    //             return \Str::contains($text, $uasFields, ignoreCase: true);
    //         });

    //         $result = [];
    //         $totalItems = $allScpmk->count();

    //         // 4. Map data & injeksi bobot otomatis jika baris evaluasi tidak ditemukan
    //         foreach ($allScpmk as $index => $scpmk) {
    //             $bobotSub = ($scpmk->bobot ?? 0) / 100;

    //             // Injeksi UTS ke elemen tengah (misal indeks ke-6 atau ke-7) jika tidak ada baris UTS murni
    //             if (! $hasUts && $index === 6 && ($rps->bobot_uts > 0)) {
    //                 $bobotSub += ($rps->bobot_uts / 100);
    //             }

    //             // Injeksi UAS ke elemen paling terakhir jika tidak ada baris UAS murni
    //             if (! $hasUas && $index === ($totalItems - 1) && ($rps->bobot_uas > 0)) {
    //                 $bobotSub += ($rps->bobot_uas / 100);
    //             }

    //             $result[] = [
    //                 'kode_scpmk' => $scpmk->kode ?? '-',
    //                 'kode_cpmk' => $scpmk->parent_kode_cpmk ?? '-',
    //                 'metode' => $scpmk->metode ?? '-',
    //                 'bobot' => $bobotSub,
    //             ];
    //         }

    //         return $result;
    //     });
    // }
}
