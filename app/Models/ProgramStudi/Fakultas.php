<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fakultas extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_fk', 'nama_fk', 'nilai_fk'];

    protected $appends = ['kode', 'fakultas'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function departemens(): HasMany
    {
        return $this->hasMany(Departemen::class, 'fk_id');
    }

    public function prodis(): HasManyThrough
    {
        return $this->hasManyThrough(
            Prodi::class,
            Departemen::class,
            'fk_id',
            'dp_id',
            'id',
            'id'
        );
    }

    protected function rekapFk(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->nilai_fk ?? 0, 2)
        );
    }

    protected function indexFk(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_fk ?? 0;
                $index = match (true) {
                    $nilai >= 85 => 4.00,
                    $nilai >= 80 => 3.70,
                    $nilai >= 75 => 3.30,
                    $nilai >= 70 => 3.00,
                    $nilai >= 65 => 2.70,
                    $nilai >= 60 => 2.30,
                    $nilai >= 55 => 2.00,
                    $nilai >= 40 => 1.00,
                    default => 0.00,
                };

                return number_format($index, 2);
            }
        );
    }

    protected function akreditasFk(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_fk ?? 0;

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
            }
        );
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_fk'])) {
                return $this->attributes['kode_fk'];
            }

            return 'UNI';
        });
    }

    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_fk'])) {
                return 3;
            }

            return 4;
        });
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->nama_fk);
    }

    protected function fakultasFk(): Attribute
    {
        return Attribute::get(fn () => 'Fakultas '.$this->nama_fk);
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

    public function scopeSearchFakultas(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm) {
            $q->where('fakultas.nama_fk', 'like', $searchTerm)
                ->orWhere('fakultas.kode_fk', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);

            if (is_numeric($search)) {
                $q->orWhere('fakultas.id', 'like', $search);
            }
        });
    }

    public function scopeSearchFakultasSmart(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchCleaned = trim(preg_replace('/(nilai|index)/i', '', $search));

        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';

        return $query->where(function ($q) use ($search, $searchCleaned, $searchTerm, $searchLower) {

            // Filter Dasar
            $q->where('fakultas.nama_fk', 'like', $searchTerm)
                ->orWhere('fakultas.kode_fk', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);

            if (is_numeric($search)) {
                $q->orWhere('fakultas.id', 'like', $search);
            }
            // --- B. Pencarian Cerdas Nilai/Index ---
            $q->orWhere(function ($sub) use ($searchCleaned) {
                // 1. Mutu Huruf (A, B+, dll)
                $mapHuruf = [
                    'A' => [85, 100], 'A-' => [80, 84.99], 'B+' => [75, 79.99],
                    'B' => [70, 74.99], 'B-' => [65, 69.99], 'C+' => [60, 64.99],
                    'C' => [55, 59.99], 'D' => [40, 54.99], 'E' => [0, 39.99],
                ];
                $upperSearch = strtoupper($searchCleaned);
                if (isset($mapHuruf[$upperSearch])) {
                    $sub->orWhereBetween('nilai_fk', $mapHuruf[$upperSearch]);

                    return; // Keluar jika sudah ketemu berdasarkan huruf
                }

                // 2. Pencarian Numerik
                if (preg_match('/([><=]?)\s*(\d*\.?\d+)/', $searchCleaned, $matches)) {
                    $operator = $matches[1] ?: 'LIKE';
                    $val = $matches[2];

                    if ($operator === 'LIKE') {
                        // A. Pencarian langsung ke nilai_fk (misal: "86" atau "3.7")
                        $sub->orWhereRaw('CAST(ROUND(nilai_fk, 2) AS CHAR) LIKE ?', ['%'.$val.'%']);

                        // B. Jika angka <= 4.00, kita perlakukan sebagai INDEKS
                        if ((float) $val <= 4.00) {
                            $mapIndeks = [
                                '4.00' => [85, 100], '3.70' => [80, 84.99], '3.30' => [75, 79.99],
                                '3.00' => [70, 74.99], '2.70' => [65, 69.99], '2.30' => [60, 64.99],
                                '2.00' => [55, 59.99], '1.00' => [40, 54.99], '0.00' => [0, 39.99],
                            ];

                            foreach ($mapIndeks as $indeks => $range) {
                                if ($indeks === number_format((float) $val, 2, '.', '')) {
                                    $sub->orWhereBetween('nilai_fk', $range);
                                }
                            }
                        }
                    } else {
                        // Jika ada operator (>, <, =), langsung ke nilai_fk
                        $sub->orWhere('nilai_fk', $operator, (float) $val);
                    }
                }
            });

            // 3. Filter Tanggal
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {
                $numericFormats = ['%d/%m/%Y', '%Y-%m-%d'];
                $textFormats = ['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'];

                foreach ($numericFormats as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(fakultas.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(fakultas.updated_at, '$format') LIKE ?", [$searchTerm]);
                }
                foreach ($textFormats as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(fakultas.created_at, '$format')) LIKE ?", [$searchLower])
                        ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.updated_at, '$format')) LIKE ?", [$searchLower]);
                }
            });
        });
    }
}
