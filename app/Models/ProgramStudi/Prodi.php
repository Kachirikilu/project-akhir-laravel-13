<?php

namespace App\Models\ProgramStudi;

use App\Models\Akademik\MataKuliah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function dp_rel()
    {
        return $this->belongsTo(Departemen::class, 'dp_id')->withTrashed();
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

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            if ($this->strata == 'Sarjana') {
                return 'S1 '.$this->nama_pr;
            }
            if ($this->strata == 'Magister') {
                return 'S2 '.$this->nama_pr;
            }
            if ($this->strata == 'Doktor') {
                return 'S3 '.$this->nama_pr;
            }

            return $this->nama_pr;
        });
    }

    protected function prodiPr(): Attribute
    {
        return Attribute::get(function () {
            return 'Program Studi '.$this->prodi;
        });
    }

    protected function kode(): Attribute
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
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            // 1. Filter dasar Prodi (Nama, Kode Prodi, ID)
            $q->where('prodis.nama_pr', 'like', $searchTerm)
                ->orWhere('prodis.kode_pr', 'like', $searchTerm);

            if (is_numeric($search)) {
                $q->orWhere('prodis.id', 'like', $search);
            }

            // 2. Filter Pintar Strata (S1, S2, S3 / Sarjana, Magister, Doktor)
            $q->orWhereRaw("
                CONCAT(
                    CASE 
                        WHEN strata = 'Sarjana' THEN 'S1' 
                        WHEN strata = 'Magister' THEN 'S2' 
                        WHEN strata = 'Doktor' THEN 'S3' 
                        ELSE strata 
                    END, 
                    ' ', 
                    nama_pr
                ) LIKE ?", [$searchTerm])
                ->orWhereRaw("CONCAT(strata, ' ', nama_pr) LIKE ?", [$searchTerm]);

            $q->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                $dq->whereRaw("DATE_FORMAT(prodis.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(prodis.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.created_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(prodis.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(prodis.updated_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
            });

            // 3. Filter Relasi ke Departemen (Termasuk kode_dp)
            $q->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
                $j->withTrashed()->where(function ($sq) use ($searchTerm) {
                    $sq->where('nama_dp', 'like', $searchTerm)
                        ->orWhere('kode_dp', 'like', $searchTerm)
                        ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm]);
                })
                // 4. Filter Relasi ke Fakultas (Termasuk kode_fk)
                    ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
                        $f->withTrashed()->where(function ($sf) use ($searchTerm) {
                            $sf->where('nama_fk', 'like', $searchTerm)
                                ->orWhere('kode_fk', 'like', $searchTerm)
                                ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
                        });
                    });
            });
        });
    }
}
