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
        return $this->belongsToMany(CPMK::class, 'cpl_pivot_cpmk', 'cpl_id', 'cpmk_id')
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
                if ($this->tingkat_cpl == 1) { // Tingkat Prodi
                    $prefix = $prodi->kode;
                } elseif ($this->tingkat_cpl == 2) { // Tingkat Departemen
                    $prefix = $prodi->dp_rel?->kode;
                } elseif ($this->tingkat_cpl == 3) { // Tingkat Fakultas
                    $prefix = $prodi->dp_rel?->fk_rel?->kode;
                } elseif ($this->tingkat_cpl == 4) { // Tingkat Universitas
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
        $normalized = preg_replace('/[^A-Z0-9]/', '', $search);

        $prefixPart = null;
        $kodePart = null;

        // Cari posisi kata 'CPL' agar tidak terpotong huruf 'C' pada kode prodi/departemen (misal: TKC)
        $cplPos = strpos($normalized, 'CPL');

        if ($cplPos !== false) {
            $prefixPart = substr($normalized, 0, $cplPos) ?: null;
            $kodePart = substr($normalized, $cplPos) ?: null;
        } else {
            $prefixPart = $normalized ?: null;
        }

        return $query->where(function ($q) use ($search, $searchTerm, $prefixPart, $kodePart) {
            $q->where('cpls.deskripsi', 'like', $searchTerm)
                ->orWhere('cpls.kode_cpl', 'like', $searchTerm);

            if (is_numeric($search)) {
                $q->orWhere('cpls.id', $search);
            }

            if ($prefixPart) {
                $q->orWhere(function ($sub) use ($prefixPart, $kodePart) {
                    // Jika user menyertakan nomor CPL (misal: S1-TKE-CPL01), wajib cocok dengan kode_cpl-nya
                    if ($kodePart) {
                        $sub->whereRaw("REPLACE(UPPER(cpls.kode_cpl), '-', '') LIKE ?", ['%'.$kodePart.'%']);
                    }

                    $sub->where(function ($low) use ($prefixPart) {
                        // 1. Tingkat Prodi (1), Departemen (2), dan Fakultas (3) — semua lewat relasi prodis
                        $low->whereHas('prodis', function ($pro) use ($prefixPart) {
                            $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                                ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                ->where(function ($x) use ($prefixPart) {
                                    // Tingkat 1: Prodi (cocokkan 'S1TKE' maupun 'TKE')
                                    $x->orWhere(function ($q1) use ($prefixPart) {
                                        $q1->where('cpls.tingkat_cpl', 1)
                                            ->where(function ($p) use ($prefixPart) {
                                                // Dengan strata (S1TKE)
                                                $p->whereRaw("
                                               REPLACE(UPPER(CONCAT(
                                                   CASE
                                                       WHEN prodis.strata = 'Sarjana' THEN 'S1'
                                                       WHEN prodis.strata = 'Magister' THEN 'S2'
                                                       WHEN prodis.strata = 'Doktor' THEN 'S3'
                                                       ELSE ''
                                                   END,
                                                   COALESCE(NULLIF(prodis.kode_pr, ''), '')
                                               )), '-', '') LIKE ?
                                           ", [$prefixPart.'%'])
                                                // Tanpa strata (TKE)
                                                    ->orWhereRaw("
                                               REPLACE(UPPER(COALESCE(NULLIF(prodis.kode_pr, ''), '')), '-', '') LIKE ?
                                           ", [$prefixPart.'%']);
                                            });
                                    })
                                    // Tingkat 2: Departemen (via prodis -> departemens)
                                        ->orWhere(function ($q2) use ($prefixPart) {
                                            $q2->where('cpls.tingkat_cpl', 2)
                                                ->whereRaw("REPLACE(UPPER(COALESCE(departemens.kode_dp, '')), '-', '') LIKE ?", [$prefixPart.'%']);
                                        })
                                    // Tingkat 3: Fakultas (via prodis -> departemens -> fakultas)
                                        ->orWhere(function ($q3) use ($prefixPart) {
                                            $q3->where('cpls.tingkat_cpl', 3)
                                                ->whereRaw("REPLACE(UPPER(COALESCE(fakultas.kode_fk, '')), '-', '') LIKE ?", [$prefixPart.'%']);
                                        });
                                });
                        });

                        // 2. Tingkat 4: Universitas ('UNI')
                        if (str_starts_with('UNI', $prefixPart)) {
                            $low->orWhere('cpls.tingkat_cpl', 4);
                        }
                    });
                });
            }
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
                    FROM cpl_pivot_cpmk
                    JOIN rps_pivot_cpmk ON cpl_pivot_cpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
                    WHERE cpl_pivot_cpmk.cpl_id = cpls.id
                ) = ?', [$targetRps]);
            });
        }

        return $query;
    }
}
