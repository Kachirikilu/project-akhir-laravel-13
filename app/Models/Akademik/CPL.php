<?php

namespace App\Models\Akademik;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CPL extends Model
{
    use SoftDeletes;

    protected $table = 'cpls';

    protected $guarded = ['id'];

    // protected $appends = ['kode'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function rekap_mahasiswas()
    {
        return $this->hasMany(
            RekapCPLMahasiswa::class,
            'cpl_id'
        );
    }

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'prodi_pivot_cpl', 'cpl_id', 'pr_id')
            ->withPivot('sort_order');
    }

    // public function rps(): BelongsToMany
    // {
    //     return $this->belongsToMany(RPS::class, 'rps_pivot_cpl', 'cpl_id', 'rps_id')
    //         ->withPivot('sort_order');
    // }

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'cpmk_pivot_cpl', 'cpl_id', 'cpmk_id')
            ->withPivot('sort_order');
    }

    // protected function kode(): Attribute
    // {
    //     return Attribute::get(function () {
    //         return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_cpl);
    //     });
    // }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $prodi = $this->prodis->first();
            $prefix = 'UNI';
            if ($prodi) {
                if ($this->level_cpl == 1) { // Tingkat Prodi
                    $prefix = $prodi->kode_pr ?? $prodi->dp_rel?->kode_dp ?? $prodi->dp_rel?->fk_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->level_cpl == 2) { // Tingkat Departemen
                    $prefix = $prodi->dp_rel?->kode_dp ?? $prodi->dp_rel?->fk_rel?->kode_fk ?? $prefixDefault ?? 'UNI';
                } elseif ($this->level_cpl == 3) { // Tingkat Fakultas
                    $prefix = $prodi->dp_rel?->fk_rel?->kode_fk ?? 'UNI';
                } elseif ($this->level_cpl == 4) { // Tingkat Universitas
                    $prefix = 'UNI';
                }
            } else {
                return $this->kode_cpl;
            }

            return $prefix.'-'.$this->kode_cpl;
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

    public function scopeSearchCPL($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = strtoupper(trim($search));
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';

        $prefixPart = null;
        $kodePart = null;

        $normalized = strtoupper(
            preg_replace('/[^A-Z0-9]/', '', $search)
        );

        $prefixPart = null;
        $kodePart = null;

        $cplPos = strpos($normalized, 'C');

        if ($cplPos !== false) {

            $prefixPart = substr($normalized, 0, $cplPos);
            $kodePart = substr($normalized, $cplPos);

            if ($prefixPart === '') {
                $prefixPart = null;
            }

            if ($kodePart === '') {
                $kodePart = null;
            }

        } else {
            $prefixPart = $normalized;
        }

        return $query->where(function ($q) use (
            $search,
            $searchTerm,
            $prefixPart,
            $kodePart
        ) {
            $q->where('cpls.deskripsi', 'like', $searchTerm);
            if (is_numeric($search)) {
                $q->orWhere('cpls.id', $search);
            }
            $q->orWhere('cpls.kode_cpl', $searchTerm);

            if ($prefixPart) {
                $q->orWhere(function ($sub) use ($prefixPart, $kodePart) {
                    if ($kodePart) {
                        $sub->orWhere('cpls.kode_cpl', 'like', '%'.$kodePart.'%');
                    }
                    $sub->where(function ($low) use ($prefixPart) {

                        // 1. CPL Level 1: Prodi
                        $low->orWhere(function ($q) use ($prefixPart) {
                            $q->where('cpls.level_cpl', 1)
                                ->whereHas('prodis', function ($pro) use ($prefixPart) {
                                    $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                                        ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                        ->where(function ($x) use ($prefixPart) {
                                            $normalizedPrefix = str_replace('-', '', $prefixPart);
                                            $x->whereRaw("REPLACE(COALESCE(NULLIF(prodis.kode_pr, ''), NULLIF(departemens.kode_dp, ''), NULLIF(fakultas.kode_fk, ''), 'UNI'), '-', '') LIKE ?", [$normalizedPrefix.'%'])
                                                ->orWhereRaw("REPLACE(CONCAT(CASE WHEN prodis.strata = 'Sarjana' THEN 'S1' WHEN prodis.strata = 'Magister' THEN 'S2' WHEN prodis.strata = 'Doktor' THEN 'S3' ELSE '' END, COALESCE(NULLIF(prodis.kode_pr, ''), NULLIF(departemens.kode_dp, ''), NULLIF(fakultas.kode_fk, ''), 'UNI')), '-', '') LIKE ?", [$normalizedPrefix.'%']);
                                        });
                                });
                        });

                        // 2. CPL Level 2: Departemen
                        $low->orWhere(function ($q) use ($prefixPart) {
                            $q->where('cpls.level_cpl', 2)
                                ->whereHas('prodis.dp_rel', function ($dp) use ($prefixPart) {
                                    $dp->where('kode_dp', 'LIKE', $prefixPart.'%');
                                });
                        });

                        // 3. CPL Level 3: Fakultas
                        $low->orWhere(function ($q) use ($prefixPart) {
                            $q->where('cpls.level_cpl', 3)
                                ->whereHas('prodis.dp_rel.fk_rel', function ($fk) use ($prefixPart) {
                                    $fk->where('kode_fk', 'LIKE', $prefixPart.'%');
                                });
                        });

                        // 4. CPL Level 4: Universitas (UNI)
                        $low->orWhere(function ($q) use ($prefixPart) {
                            $q->where('cpls.level_cpl', 4);
                            if ($prefixPart !== 'UNI') {
                            }
                        });
                    });
                });
            }
            $q->orWhere('cpls.kode_cpl', 'like', $searchTerm);
        });
    }

    // public function scopeSearchCPL($query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchTerm = '%'.$search.'%';
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchClean) {
    //         $q->where('cpls.kode_cpl', 'like', $searchTerm)
    //             ->orWhere('cpls.kode_cpl', 'like', $searchClean)
    //             ->orWhere('cpls.deskripsi', 'like', $searchTerm);

    //         if (is_numeric($search)) {
    //             $q->orWhere('cpls.id', 'like', $search);
    //         }
    //     });
    // }

    // public function scopeSearchCPL($query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchTerm = '%'.$search.'%';
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {
    //         $q->where('cpls.kode_cpl', 'like', $searchTerm)
    //                 ->orWhere('cpls.kode_cpl', 'like', $searchClean)
    //                 ->orWhere('cpls.deskripsi', 'like', $searchTerm);

    //             if (is_numeric($search)) {
    //                 $q->orWhere('cpls.id', 'like', $search);
    //             }

    //             $q->orWhere(function($dq) use ($searchLower, $searchTerm) {
    //                 $dq->whereRaw("DATE_FORMAT(cpls.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(cpls.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.created_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.created_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.created_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.created_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("DATE_FORMAT(cpls.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("DATE_FORMAT(cpls.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.updated_at, '%a, %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.updated_at, '%W, %d %M %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.updated_at, '%a %d %b %Y')) LIKE ?", ['%' . $searchLower . '%'])
    //                 ->orWhereRaw("LOWER(DATE_FORMAT(cpls.updated_at, '%W %d %M %Y')) LIKE ?", ['%' . $searchLower . '%']);
    //             });
    //     });
    // }
}
