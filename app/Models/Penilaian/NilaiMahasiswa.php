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
        'kj_id',
        'nilai',
        'nilai_array',
        'bobot_array',
        'is_locked',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_array' => 'array',
        'bobot_array' => 'array',
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

    protected function nilaiIndex(): Attribute
    {
        return Attribute::get(function () {

            return match ($this->nilai_mutu) {
                'A' => '4.00',
                'A-' => '3.70',
                'B+' => '3.30',
                'B' => '3.00',
                'B-' => '2.70',
                'C+' => '2.30',
                'C' => '2.00',
                'D' => '1.00',
                default => '0.00',
            };
        });
    }

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

    protected function mappingPertemuan(): Attribute
    {
        return Attribute::get(function () {

            $rps = $this->rps_rel;

            if (! $rps) {
                return [];
            }

            $allScpmk = $rps->cpmks
                ->flatMap(function ($cpmk) {
                    return $cpmk->scpmks->map(function ($scpmk) use ($cpmk) {

                        $scpmk->parent_kode_cpmk = $cpmk->kode;

                        return $scpmk;
                    });
                })
                ->values();

            $getNearestCpmk = function (int $sourceIndex) use ($allScpmk) {
                for ($i = $sourceIndex - 1; $i >= 0; $i--) {
                    if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
                        return $allScpmk[$i]->parent_kode_cpmk;
                    }
                }
                for ($i = $sourceIndex; $i < $allScpmk->count(); $i++) {
                    if (! empty($allScpmk[$i]?->parent_kode_cpmk)) {
                        return $allScpmk[$i]->parent_kode_cpmk;
                    }
                }

                return '-';
            };

            $hasUts = $allScpmk->contains(function ($item) {
                return Str::contains(
                    $item->deskripsi ?? '',
                    SubCPMK::$UTS_FIELDS,
                    ignoreCase: true
                ) || Str::contains(
                    $item->metode ?? '',
                    SubCPMK::$UTS_FIELDS,
                    ignoreCase: true
                );
            });

            $hasUas = $allScpmk->contains(function ($item) {
                return Str::contains(
                    $item->deskripsi ?? '',
                    SubCPMK::$UAS_FIELDS,
                    ignoreCase: true
                ) || Str::contains(
                    $item->metode ?? '',
                    SubCPMK::$UAS_FIELDS,
                    ignoreCase: true
                );
            });

            $result = [];
            $sourceIndex = 0;

            for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {

                // UTS otomatis
                if (! $hasUts && $pertemuan == 8) {
                    $result[] = [
                        'kode_scpmk' => 'UTS',
                        'kode_cpmk' => $getNearestCpmk($sourceIndex),
                        'metode' => 'UTS',
                        'bobot' => $rps->bobot_uts / 100,
                    ];

                    continue;
                }
                // UAS otomatis
                if (! $hasUas && $pertemuan == 16) {
                    $result[] = [
                        'kode_scpmk' => 'UAS',
                        'kode_cpmk' => $getNearestCpmk($sourceIndex),
                        'metode' => 'UAS',
                        'bobot' => $rps->bobot_uas / 100,
                    ];

                    continue;
                }

                $scpmk = $allScpmk->get($sourceIndex);

                $result[] = [
                    'kode_scpmk' => $scpmk?->kode ?? '-',
                    'kode_cpmk' => $getNearestCpmk($sourceIndex),
                    'metode' => $scpmk?->metode ?? '-',
                    'bobot' => ($scpmk?->bobot ?? 0) / 100,
                ];

                $sourceIndex++;
            }

            return $result;
        });
    }
}
