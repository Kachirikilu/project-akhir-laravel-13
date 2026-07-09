<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departemen extends Model
{
    use SoftDeletes;

    protected $fillable = ['fk_id', 'kode_dp', 'nama_dp', 'nilai_dp'];

    protected $appends = ['kode', 'departemen', 'fakultas'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function fk_rel()
    {
        return $this->belongsTo(Fakultas::class, 'fk_id')->withTrashed();
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

    protected function akreditasDp(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_dp ?? 0;

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
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);

        // Bersihkan kata "Nilai" atau "Index" agar sistem fokus pada angkanya
        $searchCleaned = trim(preg_replace('/(nilai|index)/i', '', $search));

        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';

        return $query->where(function ($q) use ($search, $searchCleaned, $searchTerm, $searchLower) {
            // 1. Filter dasar Departemen
            $q->where('departemens.nama_dp', 'like', $searchTerm)
                ->orWhere('departemens.kode_dp', 'like', $searchTerm)
                ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm]);

            if (is_numeric($search)) {
                $q->orWhere('departemens.id', 'like', $search);
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
                    $sub->orWhereBetween('nilai_dp', $mapHuruf[$upperSearch]);

                    return; // Keluar jika sudah ketemu berdasarkan huruf
                }

                // 2. Pencarian Numerik
                if (preg_match('/([><=]?)\s*(\d*\.?\d+)/', $searchCleaned, $matches)) {
                    $operator = $matches[1] ?: 'LIKE';
                    $val = $matches[2];

                    if ($operator === 'LIKE') {
                        // A. Pencarian langsung ke nilai_dp (misal: "86" atau "3.7")
                        // Gunakan CAST agar SQL membandingkan sebagai string/desimal yang bersih
                        $sub->orWhereRaw('CAST(ROUND(nilai_dp, 2) AS CHAR) LIKE ?', ['%'.$val.'%']);

                        // B. Jika angka <= 4.00, kita perlakukan sebagai INDEKS
                        // Gunakan logika strict agar "2" tidak menangkap "2.30" atau "2.70" kecuali user mengetik "2.0"
                        if ((float) $val <= 4.00) {
                            $mapIndeks = [
                                '4.00' => [85, 100], '3.70' => [80, 84.99], '3.30' => [75, 79.99],
                                '3.00' => [70, 74.99], '2.70' => [65, 69.99], '2.30' => [60, 64.99],
                                '2.00' => [55, 59.99], '1.00' => [40, 54.99], '0.00' => [0, 39.99],
                            ];

                            foreach ($mapIndeks as $indeks => $range) {
                                // Jika user ketik "3.7" (persis), maka ambil range 3.70
                                // Jika user ketik "3", maka ambil range 3.00 saja
                                if ($indeks === number_format((float) $val, 2, '.', '')) {
                                    $sub->orWhereBetween('nilai_dp', $range);
                                }
                            }
                        }
                    } else {
                        // Jika ada operator (>, <, =), langsung ke nilai_dp
                        $sub->orWhere('nilai_dp', $operator, (float) $val);
                    }
                }
            });
            // 3. Filter Tanggal
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {
                $numericFormats = ['%d/%m/%Y', '%Y-%m-%d'];
                $textFormats = ['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'];

                foreach ($numericFormats as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(departemens.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(departemens.updated_at, '$format') LIKE ?", [$searchTerm]);
                }
                foreach ($textFormats as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(departemens.created_at, '$format')) LIKE ?", [$searchLower])
                        ->orWhereRaw("LOWER(DATE_FORMAT(departemens.updated_at, '$format')) LIKE ?", [$searchLower]);
                }
            });

            // 4. Filter berdasarkan Fakultas (Relasi)
            $q->orWhereHas('fk_rel', function ($pr) use ($search) {
                $pr->searchFakultasSmart($search);
            });
        });
    }
}
