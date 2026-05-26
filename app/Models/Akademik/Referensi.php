<?php


namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Referensi extends Model
{
    use SoftDeletes;

    protected $table = 'referensis';
    protected $guarded = ['id'];
    protected $appends = ['kode', 'penulis_tahun'];
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
        $searchLower = '%'.strtolower($search).'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {
            $q->where('referensis.kode_ref', 'like', $searchTerm)
                    ->orWhere('referensis.kode_ref', 'like', $searchClean)
                    ->orWhere('referensis.judul', 'like', $searchTerm)
                    ->orWhere('referensis.penulis', 'like', $searchTerm)
                    ->orWhere('referensis.penerbit', 'like', $searchTerm)
                    ->orWhere('referensis.tahun', 'like', $searchTerm)
                    ->orWhere('referensis.link', 'like', $searchTerm);

                if (is_numeric($searchTerm)) {
                    $q->orWhere('referensis.id', 'like', $search);
                }

                $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(referensis.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(referensis.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("DATE_FORMAT(referensis.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(referensis.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
                    ->orWhereRaw("LOWER(DATE_FORMAT(referensis.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
                });
                ;
        });
    }
}