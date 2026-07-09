<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referensi extends Model
{
    use SoftDeletes;

    protected $table = 'referensis';

    protected $guarded = ['id'];

    // protected $appends = ['kode', 'penulis_tahun'];
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_ref);
        });
    }

    protected function penulisTahun(): Attribute
    {
        return Attribute::get(function () {
            $penulis = $this->penulis ?? 'Anonim';
            $tahun = $this->tahun ?? '----';

            return "{$penulis} ({$tahun})";
        });
    }

    protected function citation(): Attribute
    {
        return Attribute::get(function () {
            $parts = [];
            if (! empty($this->penulis)) {
                $parts[] = $this->penulis;
            }
            if (! empty($this->tahun)) {
                $parts[] = "({$this->tahun})";
            }
            if (! empty($this->judul)) {
                $parts[] = trim($this->judul).'.';
            }
            if (! empty($this->penerbit)) {
                $parts[] = trim($this->penerbit).'.';
            }

            return trim(implode(' ', $parts));
        });
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

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_ref', 'ref_id', 'rps_id')
            ->withPivot('sort_order');
    }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'cpmk_pivot_ref', 'ref_id', 'cpmk_id')
            ->withPivot('sort_order');
    }

    public function scpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'scpmk_pivot_ref', 'ref_id', 'scpmk_id')
            ->withPivot('sort_order');
    }

    public function scopeSearchRef($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchClean) {
            // 1. Pencarian Kolom Standar
            $q->where('referensis.kode_ref', 'like', $searchTerm)
                ->orWhere('referensis.kode_ref', 'like', $searchClean)
                ->orWhere('referensis.judul', 'like', $searchTerm)
                ->orWhere('referensis.penulis', 'like', $searchTerm)
                ->orWhere('referensis.penerbit', 'like', $searchTerm)
                ->orWhere('referensis.tahun', 'like', $searchTerm)
                ->orWhere('referensis.link', 'like', $searchTerm);

            // 2. Pencarian "Virtual" (Citation)
            $q->orWhereRaw("CONCAT_WS(' ', penulis, 
            IF(tahun IS NOT NULL AND tahun != '', CONCAT('(', tahun, ')'), NULL), 
            IF(judul IS NOT NULL AND judul != '', CONCAT(TRIM(judul), '.'), NULL), 
            IF(penerbit IS NOT NULL AND penerbit != '', CONCAT(TRIM(penerbit), '.'), NULL)
        ) LIKE ?", [$searchTerm]);

            // 3. ID
            if (is_numeric($search)) {
                $q->orWhere('referensis.id', $search);
            }
        });
    }

    public function scopeSearchRefSmart($query, $search)
    {
        $query->searchRef($search);

        $searchTerm = '%'.trim($search).'%';
        $searchLower = '%'.strtolower(trim($search)).'%';

        return $query->orWhere(function ($q) use ($searchTerm, $searchLower) {
            $numericFormats = ['%d/%m/%Y', '%Y-%m-%d'];
            $textFormats = ['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'];

            foreach ($numericFormats as $format) {
                $q->orWhereRaw("DATE_FORMAT(referensis.created_at, '$format') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(referensis.updated_at, '$format') LIKE ?", [$searchTerm]);
            }
            foreach ($textFormats as $format) {
                $q->orWhereRaw("LOWER(DATE_FORMAT(referensis.created_at, '$format')) LIKE ?", [$searchLower])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.updated_at, '$format')) LIKE ?", [$searchLower]);
            }
        });
    }
}
