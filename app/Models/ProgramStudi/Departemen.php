<?php

namespace App\Models\ProgramStudi;

use App\Models\Auth\Dosen;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departemen extends Model
{
    use SoftDeletes;

    protected $fillable = ['fk_id', 'kode_dp', 'nama_dp', 'nilai_dp', 'kadep_id', 'sekdep_id'];

    protected $appends = ['kode', 'departemen', 'fakultas'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function kadep_rel()
    {
        return $this->belongsTo(Dosen::class, 'kadep_id');
    }

    public function sekdep_rel()
    {
        return $this->belongsTo(Dosen::class, 'sekdep_id');
    }

    public function fk_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fk_id');
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class, 'dp_id');
    }

    protected function departemen(): Attribute
    {
        return Attribute::get(fn () => $this->nama_dp);
    }

    protected function rekapDp(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->nilai_dp ?? 0, 2)
        );
    }

    protected function indexDp(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_dp ?? 0;
                $index = match (true) {
                    $nilai >= config('rps.min_a', 86) => 4.00,
                    $nilai >= config('rps.min_a_min', 80) => 3.70,
                    $nilai >= config('rps.min_b_plus', 75) => 3.30,
                    $nilai >= config('rps.min_b', 70) => 3.00,
                    $nilai >= config('rps.min_b_min', 65) => 2.70,
                    $nilai >= config('rps.min_c_plus', 60) => 2.30,
                    $nilai >= config('rps.min_c', 56) => 2.00,
                    $nilai >= config('rps.min_d', 40) => 1.00,
                    default => 0.00,
                };

                return number_format($index, 2);
            }
        );
    }

    protected function akreditasDp(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_dp ?? 0;

                return match (true) {
                    $nilai >= config('rps.min_a', 86) => 'A',
                    $nilai >= config('rps.min_a_min', 80) => 'A-',
                    $nilai >= config('rps.min_b_plus', 75) => 'B+',
                    $nilai >= config('rps.min_b', 70) => 'B',
                    $nilai >= config('rps.min_b_min', 65) => 'B-',
                    $nilai >= config('rps.min_c_plus', 60) => 'C+',
                    $nilai >= config('rps.min_c', 56) => 'C',
                    $nilai >= config('rps.min_d', 40) => 'D',
                    default => 'E',
                };
            }
        );
    }

    protected function namaKadep(): Attribute
    {
        return Attribute::get(fn () => $this->kadep_rel->name ?? '-');
    }

    protected function nipKadep(): Attribute
    {
        return Attribute::get(fn () => $this->kadep_rel->nip ?? '-');
    }

    protected function namaSekdep(): Attribute
    {
        return Attribute::get(fn () => $this->sekdep_rel->name ?? '-');
    }

    protected function nipSekdep(): Attribute
    {
        return Attribute::get(fn () => $this->sekdep_rel->nip ?? '-');
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_dp'])) {
                return $this->attributes['kode_dp'];
            }
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }

            return 'UNI';
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }

            return 'UNI';
        });
    }

    // protected function kodeText(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if (!empty($this->attributes['kode_dp'])) {
    //             return $this->attributes['kode_dp'];
    //         }
    //         $kodeFakultas = $this->fk_rel?->kode_fk;
    //         if (!empty($kodeFakultas)) {
    //             return $kodeFakultas;
    //         }
    //         return 'UNI';
    //     });
    // }
    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_dp'])) {
                return 2;
            }
            $kodeFakultas = $this->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return 3;
            }

            return 4;
        });
    }

    protected function departemenDp(): Attribute
    {
        return Attribute::get(fn () => 'Departemen '.$this->nama_dp);
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->fk_rel?->nama_fk);
    }

    protected function fakultasFk(): Attribute
    {
        return Attribute::get(fn () => 'Fakultas '.$this->fk_rel?->nama_fk);
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

    public function scopeSearchDepartemen(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm) {
            // 1. Filter dasar Departemen
            $q->where('departemens.nama_dp', 'like', $searchTerm)
                ->orWhere('departemens.kode_dp', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm]);

            if (is_numeric($search)) {
                $q->orWhere('departemens.id', 'like', $search);
            }
        });
    }

    public function scopeSearchDepartemenSmart(Builder $query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $query->searchDepartemen($search);

        $search = trim($search);
        $searchClean = trim(preg_replace('/(nilai|index)/i', '', $search));
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);

        return $query->orWhere(function ($q) use ($search, $searchClean, $searchTerm, $searchLower) {

            $searchDigits = preg_replace('/[^0-9]/', '', $search);

            $q->orWhereHas('kadep_rel', function ($dq) use ($searchTerm, $searchDigits) {
                $dq->where('name', 'LIKE', $searchTerm)
                    ->orWhere('nip', 'LIKE', $searchTerm);

                if (! empty($searchDigits)) {
                    $dq->orWhereRaw("REPLACE(REPLACE(nip, ' ', ''), '.', '') LIKE ?", ["{$searchDigits}%"]);
                }
            })->orWhereHas('sekdep_rel', function ($wq) use ($searchTerm, $searchDigits) {
                $wq->where('name', 'LIKE', $searchTerm)
                    ->orWhere('nip', 'LIKE', $searchTerm);

                if (! empty($searchDigits)) {
                    $wq->orWhereRaw("REPLACE(REPLACE(nip, ' ', ''), '.', '') LIKE ?", ["{$searchDigits}%"]);
                }
            });

            // ===== Nilai / Index =====
            $q->orWhere(function ($sub) use ($searchClean) {

                $mapHuruf = [
                    'A' => [config('rps.min_a', 86), 100],
                    'A-' => [config('rps.min_a_min', 80), config('rps.min_a', 86) - 0.01],
                    'B+' => [config('rps.min_b_plus', 75), config('rps.min_a_min', 80) - 0.01],
                    'B' => [config('rps.min_b', 70), config('rps.min_b_plus', 75) - 0.01],
                    'B-' => [config('rps.min_b_min', 65), config('rps.min_b', 70) - 0.01],
                    'C+' => [config('rps.min_c_plus', 60), config('rps.min_b_min', 65) - 0.01],
                    'C' => [config('rps.min_c', 56), config('rps.min_c_plus', 60) - 0.01],
                    'D' => [config('rps.min_d', 40), config('rps.min_c', 56) - 0.01],
                    'E' => [0, config('rps.min_d', 40) - 0.01],
                ];

                $upper = strtoupper($searchClean);

                if (isset($mapHuruf[$upper])) {
                    $sub->orWhereBetween('nilai_dp', $mapHuruf[$upper]);

                    return;
                }

                if (preg_match('/([><=]?)\s*(\d*\.?\d+)/', $searchClean, $m)) {

                    $operator = $m[1] ?: 'LIKE';
                    $value = (float) $m[2];

                    if ($operator === 'LIKE') {

                        $sub->orWhereRaw(
                            'CAST(ROUND(nilai_dp,2) AS CHAR) LIKE ?',
                            ['%'.$m[2].'%']
                        );

                        $mapIndex = [
                            '4.00' => [config('rps.min_a', 86), 100],
                            '3.70' => [config('rps.min_a_min', 80), config('rps.min_a', 86) - 0.01],
                            '3.30' => [config('rps.min_b_plus', 75), config('rps.min_a_min', 80) - 0.01],
                            '3.00' => [config('rps.min_b', 70), config('rps.min_b_plus', 75) - 0.01],
                            '2.70' => [config('rps.min_b_min', 65), config('rps.min_b', 70) - 0.01],
                            '2.30' => [config('rps.min_c_plus', 60), config('rps.min_b_min', 65) - 0.01],
                            '2.00' => [config('rps.min_c', 56), config('rps.min_c_plus', 60) - 0.01],
                            '1.00' => [config('rps.min_d', 40), config('rps.min_c', 56) - 0.01],
                            '0.00' => [0, config('rps.min_d', 40) - 0.01],
                        ];

                        $key = number_format($value, 2, '.', '');

                        if (isset($mapIndex[$key])) {
                            $sub->orWhereBetween('nilai_dp', $mapIndex[$key]);
                        }

                    } else {
                        $sub->orWhere('nilai_dp', $operator, $value);
                    }
                }
            });

            // ===== Tanggal =====
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(departemens.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(departemens.updated_at, '$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(departemens.created_at, '$format')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereRaw("LOWER(DATE_FORMAT(departemens.updated_at, '$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });

            // ===== Fakultas =====
            $q->orWhereHas('fk_rel', fn ($fk) => $fk->searchFakultasSmart($search));
        });
    }
}
