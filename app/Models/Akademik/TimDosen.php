<?php

namespace App\Models\Akademik;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use App\Models\Auth\Dosen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TimDosen extends Model
{
    use SoftDeletes;

    protected $table = 'tim_dosens';
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_tim_dosen);
        });
    }

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_tim_dosen', 'tim_dosen_id', 'rps_id')
                    ->withPivot('sort_order')
                    ->orderBy('sort_order')
                    ->withTimestamps();
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id')->withTrashed();
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'tim_dosen_pivot_dosen', 'tim_dosen_id', 'dosen_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order', 'pertemuan_ke'])
            ->withCasts(['pertemuan_ke' => 'array'])
            ->orderByPivot('sort_order')
            ->withTimestamps();
    }

    protected function tim(): Attribute
    {
        return Attribute::get(fn () => $this->nama_tim);
    }


    protected function ketua(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->where('pivot.is_ketua', true)->first()?->name ?? '-';
        });
    }

    protected function nip(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->where('pivot.is_ketua', true)->first()?->nip ?? '-';
        });
    }

    protected function peran(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ketua = $this->dosens->firstWhere('pivot.is_ketua', true);
                return $ketua?->pivot->peran ?? 'Koordinator';
            }
        );
    }

    protected function countDosen(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->count();
        });
    }
    protected function anggota(): Attribute
    {
        return Attribute::get(function () {
            return $this->count_dosen . ' Anggota';
        });
    }
    protected function countKoordinator(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->where('pivot.peran', 'Koordinator')->count();
        });
    }

    protected function countPengajar(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->where('pivot.peran', 'Pengajar')->count();
        });
    }

    protected function countAsisten(): Attribute
    {
        return Attribute::get(function () {
            return $this->dosens->where('pivot.peran', 'Asisten')->count();
        });
    }
    protected function kodePr(): Attribute
    {
        return Attribute::get(fn () => $this->pr_rel->kode);
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(fn () => $this->pr_rel->prodi);
    }

    protected function prodiPr(): Attribute
    {
        return Attribute::get(fn () => $this->pr_rel->prodi_pr);
    }



    // Accessor untuk tanggal
    protected function createdDay(): Attribute
    {
        return Attribute::get(fn () => $this->created_at?->translatedFormat('D, d M Y'));
    }

    protected function updatedDay(): Attribute
    {
        return Attribute::get(fn () => $this->updated_at?->translatedFormat('D, d M Y'));
    }

    public function scopeSearchTimDosen($query, $search)
    {
        if (empty(trim($search))) return $query;

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($searchTerm, $searchClean) {
            $q->where('nama_tim', 'like', $searchTerm)->orWhere('kode_tim_dosen', 'like', $searchClean)
            ->orWhereHas('dosens', function ($sub) use ($searchTerm) {
                $sub->where('name', 'like', $searchTerm)
                    ->orWhere('nip', 'like', $searchTerm);
            })
            ->orWhereHas('pr_rel', function ($mq) use ($searchTerm) {
                $mq->searchProdi($searchTerm);
            });
        });
    }


}