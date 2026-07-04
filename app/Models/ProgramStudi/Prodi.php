<?php

namespace App\Models\ProgramStudi;

use App\Models\Auth\Dosen;
use App\Models\Akademik\CPL;
use App\Models\Akademik\MataKuliah;
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

        $searchTerm = '%' . $search . '%';

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

            /*
            |--------------------------------------------------------------------------
            | S1 + Nama Prodi
            | Contoh:
            | S1 Teknik Elektro
            | S1TeknikElektro
            | S1-Teknik Elektro
            |--------------------------------------------------------------------------
            */
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
            ", ['%' . $searchNormalized . '%']);

            /*
            |--------------------------------------------------------------------------
            | S1 + Kode Prodi
            | Contoh:
            | S1TKE
            | S1-TKE
            | S1 TKE
            |--------------------------------------------------------------------------
            */
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
            ", ['%' . $searchNormalized . '%']);

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

    // public function scopeSearchProdi(Builder $query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
    //         // 1. Filter dasar Prodi (Nama, Kode Prodi, ID)
    //         $q->where('prodis.nama_pr', 'like', $searchTerm)
    //             ->orWhere('prodis.kode_pr', 'like', $searchTerm);

    //         if (is_numeric($search)) {
    //             $q->orWhere('prodis.id', 'like', $search);
    //         }

    //         // 2. Filter Pintar Strata (S1, S2, S3 / Sarjana, Magister, Doktor)
    //         $q->orWhereRaw("
    //             CONCAT(
    //                 CASE
    //                     WHEN strata = 'Sarjana' THEN 'S1'
    //                     WHEN strata = 'Magister' THEN 'S2'
    //                     WHEN strata = 'Doktor' THEN 'S3'
    //                     ELSE strata
    //                 END,
    //                 ' ',
    //                 nama_pr
    //             ) LIKE ?", [$searchTerm])
    //             ->orWhereRaw("CONCAT(strata, ' ', nama_pr) LIKE ?", [$searchTerm]);

    //         $q->orWhere(function ($dq) use ($searchLower, $searchTerm) {
    //             $dq->whereRaw("DATE_FORMAT(prodis.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(prodis.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
    //         });

    //         // 3. Filter Relasi ke Departemen (Termasuk kode_dp)
    //         $q->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
    //             $j->withTrashed()->where(function ($sq) use ($searchTerm) {
    //                 $sq->where('nama_dp', 'like', $searchTerm)
    //                     ->orWhere('kode_dp', 'like', $searchTerm)
    //                     ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm]);
    //             })
    //             // 4. Filter Relasi ke Fakultas (Termasuk kode_fk)
    //                 ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
    //                     $f->withTrashed()->where(function ($sf) use ($searchTerm) {
    //                         $sf->where('nama_fk', 'like', $searchTerm)
    //                             ->orWhere('kode_fk', 'like', $searchTerm)
    //                             ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
    //                     });
    //                 });
    //         });
    //     });
    // }
}
