<?php

namespace App\Models\ProgramStudi;

use App\Models\Akademik\CPL;
use App\Models\Akademik\MataKuliah;
use App\Models\Auth\Dosen;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prodi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dp_id',
        'kode_pr',
        'nama_pr',
        'strata',
        'nilai_pr',
    ];

    protected $appends = ['kode', 'prodi', 'departemen', 'fakultas'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class, 'pr_id', 'id');
    }

    public function dp_rel()
    {
        return $this->belongsTo(Departemen::class, 'dp_id')->withTrashed();
    }

    protected function rekapPr(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->nilai_pr ?? 0, 2)
        );
    }

    protected function indexPr(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_pr ?? 0;
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

    protected function akreditasPr(): Attribute
    {
        return Attribute::make(
            get: function () {
                $nilai = $this->nilai_pr ?? 0;

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

    public function cpls()
    {
        return $this->belongsToMany(
            CPL::class,
            'prodi_pivot_cpl',
            'pr_id',
            'cpl_id'
        )->withPivot('sort_order');
    }

    public function mata_kuliahs()
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'prodi_pivot_mk',
            'pr_id',
            'mk_id'
        )->withTimestamps();
    }

    protected function strataS(): Attribute
    {
        return Attribute::get(function () {
            if ($this->strata == 'Sarjana') {
                return 'S1';
            }
            if ($this->strata == 'Magister') {
                return 'S2';
            }
            if ($this->strata == 'Doktor') {
                return 'S3';
            }

            return null;
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->strata_s) {
                return $this->nama_pr;
            }

            return $this->strata_s.' '.$this->nama_pr;
        });
    }

    protected function prodiPr(): Attribute
    {
        return Attribute::get(function () {
            return 'Program Studi '.$this->prodi;
        });
    }

    protected function prodiStrata(): Attribute
    {
        return Attribute::get(function () {
            return $this->strata.' '.$this->nama_pr;
        });
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $s = $this->strata_s.'-';
            if (! empty($this->attributes['kode_pr'])) {
                return $s.$this->attributes['kode_pr'];
            }
            $kodeDepartemen = $this->dp_rel?->kode_dp;
            if (! empty($kodeDepartemen)) {
                return $s.$kodeDepartemen;
            }
            $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $s.$kodeFakultas;
            }

            return $s.'UNI';
        });
    }

    protected function kodeShort(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return $this->attributes['kode_pr'];
            }
            $kodeDepartemen = $this->dp_rel?->kode_dp;
            if (! empty($kodeDepartemen)) {
                return $kodeDepartemen;
            }
            $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }

            return 'UNI';
        });
    }

    protected function kodePr(): Attribute
    {
        return Attribute::get(function () {
            return $this->kode;
        });
    }

    protected function kodeDp(): Attribute
    {
        return Attribute::get(function () {
            $kodeDepartemen = $this->dp_rel?->kode_dp;
            if (! empty($kodeDepartemen)) {
                return $kodeDepartemen;
            }
            $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }

            return 'UNI';
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return $kodeFakultas;
            }

            return 'UNI';
        });
    }

    protected function fkId(): Attribute
    {
        return Attribute::get(function () {
            return $this->dp_rel?->fk_rel?->id;
        });
    }

    // protected function kodeText(): Attribute
    // {
    //     return Attribute::get(function () {
    //         if (! empty($this->attributes['kode_pr'])) {
    //             return $this->attributes['kode_pr'];
    //         }
    //         $kodeDepartemen = $this->dp_rel?->kode_dp;
    //         if (! empty($kodeDepartemen)) {
    //             return $kodeDepartemen;
    //         }
    //         $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
    //         if (! empty($kodeFakultas)) {
    //             return $kodeFakultas;
    //         }
    //         return 'UNI';
    //     });
    // }
    protected function tingkatanProdi(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->attributes['kode_pr'])) {
                return 1;
            }
            $kodeDepartemen = $this->dp_rel?->kode_dp;
            if (! empty($kodeDepartemen)) {
                return 2;
            }
            $kodeFakultas = $this->dp_rel?->fk_rel?->kode_fk;
            if (! empty($kodeFakultas)) {
                return 3;
            }

            return 4;
        });
    }

    protected function departemen(): Attribute
    {
        return Attribute::get(fn () => $this->dp_rel?->nama_dp);
    }

    protected function departemenDp(): Attribute
    {
        return Attribute::get(fn () => 'Departemen '.$this->dp_rel?->nama_dp);
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(fn () => $this->dp_rel?->fk_rel?->nama_fk);
    }

    protected function fakultasFk(): Attribute
    {
        return Attribute::get(fn () => 'Fakultas '.$this->dp_rel?->fk_rel?->nama_fk);
    }

    protected function fakultasId(): Attribute
    {
        return Attribute::get(fn () => $this->dp_rel?->fk_rel?->id);
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

    public function scopeSearchProdi(Builder $query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);

        $searchNormalized = strtoupper(
            preg_replace('/[^A-Za-z0-9]/', '', $search)
        );

        $searchTerm = '%'.$search.'%';

        $strataExpr = "
            CASE
                WHEN prodis.strata = 'Sarjana' THEN 'S1'
                WHEN prodis.strata = 'Magister' THEN 'S2'
                WHEN prodis.strata = 'Doktor' THEN 'S3'
                ELSE prodis.strata
            END
        ";

        return $query->where(function ($q) use (
            $search,
            $searchTerm,
            $searchNormalized,
            $strataExpr
        ) {
            // Nama Prodi & Kode Prodi
            $q->where('prodis.nama_pr', 'like', $searchTerm)
                ->orWhere('prodis.kode_pr', 'like', $searchTerm)
                ->orWhere('prodis.target_sks', 'like', $searchTerm);

            // Cari berdasarkan ID
            if (is_numeric($search)) {
                $q->orWhere('prodis.id', $search);
            }

            $q->orWhereRaw("
                REPLACE(
                    REPLACE(
                        UPPER(
                            CONCAT(
                                $strataExpr,
                                prodis.nama_pr
                            )
                        ),
                        '-', ''
                    ),
                    ' ',
                    ''
                ) LIKE ?
            ", ['%'.$searchNormalized.'%']);
            $q->orWhereRaw("
                REPLACE(
                    REPLACE(
                        UPPER(
                            CONCAT(
                                $strataExpr,
                                COALESCE(
                                    NULLIF(prodis.kode_pr, ''),
                                    (
                                        SELECT d.kode_dp
                                        FROM departemens d
                                        WHERE d.id = prodis.dp_id
                                        LIMIT 1
                                    ),
                                    (
                                        SELECT f.kode_fk
                                        FROM fakultas f
                                        JOIN departemens d
                                            ON d.fk_id = f.id
                                        WHERE d.id = prodis.dp_id
                                        LIMIT 1
                                    ),
                                    'UNI'
                                )
                            )
                        ),
                        '-', ''
                    ),
                    ' ',
                    ''
                ) LIKE ?
            ", ['%'.$searchNormalized.'%']);

            // Departemen
            $q->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
                $j->withTrashed()
                    ->where(function ($sq) use ($searchTerm) {
                        $sq->where('nama_dp', 'like', $searchTerm)
                            ->orWhere('kode_dp', 'like', $searchTerm);
                    });
            });
        });
    }

    public function scopeSearchProdiSmart(Builder $query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        // seluruh fitur SearchProdi
        $query->searchProdi($search);

        $search = trim($search);
        $searchCleaned = trim(preg_replace('/(nilai|index)/i', '', $search));
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);

        return $query->orWhere(function ($q) use ($search, $searchCleaned, $searchTerm, $searchLower) {

            // ===== Nilai / Index =====
            $q->orWhere(function ($sub) use ($searchCleaned) {

                $mapHuruf = [
                    'A' => [85, 100], 'A-' => [80, 84.99],
                    'B+' => [75, 79.99], 'B' => [70, 74.99],
                    'B-' => [65, 69.99], 'C+' => [60, 64.99],
                    'C' => [55, 59.99], 'D' => [40, 54.99],
                    'E' => [0, 39.99],
                ];

                $upper = strtoupper($searchCleaned);

                if (isset($mapHuruf[$upper])) {
                    $sub->orWhereBetween('nilai_pr', $mapHuruf[$upper]);

                    return;
                }

                if (preg_match('/([><=]?)\s*(\d*\.?\d+)/', $searchCleaned, $m)) {

                    $operator = $m[1] ?: 'LIKE';
                    $value = (float) $m[2];

                    if ($operator === 'LIKE') {

                        $sub->orWhereRaw(
                            'CAST(ROUND(nilai_pr,2) AS CHAR) LIKE ?',
                            ['%'.$m[2].'%']
                        );

                        $mapIndex = [
                            '4.00' => [85, 100],
                            '3.70' => [80, 84.99],
                            '3.30' => [75, 79.99],
                            '3.00' => [70, 74.99],
                            '2.70' => [65, 69.99],
                            '2.30' => [60, 64.99],
                            '2.00' => [55, 59.99],
                            '1.00' => [40, 54.99],
                            '0.00' => [0, 39.99],
                        ];

                        $key = number_format($value, 2, '.', '');

                        if (isset($mapIndex[$key])) {
                            $sub->orWhereBetween('nilai_pr', $mapIndex[$key]);
                        }

                    } else {
                        $sub->orWhere('nilai_pr', $operator, $value);
                    }
                }
            });

            // ===== Tanggal =====
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(prodis.created_at,'$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(prodis.updated_at,'$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at,'$format')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at,'$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });

            // ===== Relasi =====
            $q->orWhereHas('dp_rel', fn ($dp) => $dp->searchDepartemenSmart($search));
        });
    }
}
