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
                    $prefix = $prodi->kode;
                } elseif ($this->level_cpl == 2) { // Tingkat Departemen
                    $prefix = $prodi->dp_rel?->kode;
                } elseif ($this->level_cpl == 3) { // Tingkat Fakultas
                    $prefix = $prodi->dp_rel?->fk_rel?->kode;
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
                    $sub->where(function ($low) use ($prefixPart, $kodePart) {

                        // 1. CPL Level 1: Prodi
                        $low->orWhere(function ($q) use ($prefixPart, $kodePart) {

                            $q->where('cpls.level_cpl', 1)
                                ->when($kodePart, function ($q) use ($kodePart) {
                                    $q->where('cpls.kode_cpl', 'like', '%'.$kodePart.'%');
                                })
                                ->whereHas('prodis', function ($pro) use ($prefixPart) {

                                    $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                                        ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                        ->where(function ($x) use ($prefixPart) {

                                            $x->whereRaw("
                                            REPLACE(
                                                CONCAT(
                                                    CASE
                                                        WHEN prodis.strata='Sarjana' THEN 'S1'
                                                        WHEN prodis.strata='Magister' THEN 'S2'
                                                        WHEN prodis.strata='Doktor' THEN 'S3'
                                                        ELSE ''
                                                    END,
                                                    COALESCE(
                                                        NULLIF(prodis.kode_pr,''),
                                                        NULLIF(departemens.kode_dp,''),
                                                        NULLIF(fakultas.kode_fk,''),
                                                        'UNI'
                                                    )
                                                ),
                                            '-','')
                                            LIKE ?
                                        ", [$prefixPart.'%']);

                                        });

                                });

                        });
                    });
                });
            }
            $q->orWhere('cpls.kode_cpl', 'like', $searchTerm);
        });
    }

    public function scopeSearchCPLSmart($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }
        $query->searchCPL($search);
        if (preg_match('/^(\d+)\s*rps$/i', trim($search), $matches)) {
            $targetRps = (int) $matches[1];

            $query->where(function ($q) use ($targetRps) {
                $q->whereRaw('(
                    SELECT COUNT(DISTINCT rps_pivot_cpmk.rps_id)
                    FROM cpmk_pivot_cpl
                    JOIN rps_pivot_cpmk ON cpmk_pivot_cpl.cpmk_id = rps_pivot_cpmk.cpmk_id
                    WHERE cpmk_pivot_cpl.cpl_id = cpls.id
                ) = ?', [$targetRps]);
            });
        }

        return $query;
    }
}
