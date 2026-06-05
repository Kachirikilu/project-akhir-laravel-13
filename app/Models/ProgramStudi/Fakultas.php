<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Fakultas extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['kode_fk', 'nama_fk'];
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

    protected function kode(): Attribute {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_fk'])) {
                return $this->attributes['kode_fk'];
            }
            return 'UNI';
        });
    }
    protected function tingkatanProdi(): Attribute {
        return Attribute::get(function () {
            if (!empty($this->attributes['kode_fk'])) {
                return 3;
            }
            return 4;
        });
    }

    protected function fakultas(): Attribute {
        return Attribute::get(fn() => $this->nama_fk);
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

    // public function scopeSearchFakultas(Builder $query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
    //         $q->where('fakultas.nama_fk', 'like', $searchTerm)
    //             ->orWhere('fakultas.kode_fk', 'like', $searchTerm)
    //             ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
                
    //             if (is_numeric($search)) {
    //                 $q->orWhere('fakultas.id', 'like', $search);
    //             }

    //             $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
    //                 $dq->whereRaw("DATE_FORMAT(fakultas.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(fakultas.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("DATE_FORMAT(fakultas.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(fakultas.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(fakultas.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
    //             });
    //             ;
    //     });
    // }
}
