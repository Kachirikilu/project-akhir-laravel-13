<?php

namespace App\Models\Akademik;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CPMK extends Model
{
    use SoftDeletes;

    protected $table = 'cpmks';

    protected $guarded = ['id'];

    // protected $appends = ['kode'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_cpmk);
        });
    }

    protected function countScpmk(): Attribute
    {
        return Attribute::get(function () {
            return $this->scpmks->count();
        });
    }

    protected function countCpl(): Attribute
    {
        return Attribute::get(function () {
            return $this->cpls->count();
        });
    }

    protected function deskripsiCpl(): Attribute
    {
        return Attribute::get(function () {
            if (! empty($this->deskripsi)) {
                return $this->deskripsi;
            }

            if ($this->relationLoaded('cpls') || $this->cpls()->exists()) {
                return $this->cpls
                    ->map(function ($cpl) {
                        $desc = trim($cpl->deskripsi);

                        return str_ends_with($desc, '.') ? $desc : $desc.'.';
                    })
                    ->implode(' ');
            }

            return '-';
        });
    }

    protected function totalBobot(): Attribute
    {
        return Attribute::get(function () {
            return $this->scpmks->sum('bobot') ?? 0;
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
        return $this->belongsToMany(RPS::class, 'rps_pivot_cpmk', 'cpmk_id', 'rps_id')
            ->withTimestamps();
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'cpmk_pivot_cpl', 'cpmk_id', 'cpl_id')
            ->withPivot('sort_order');
    }

    public function scpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->withTimestamps();
    }

    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'cpmk_pivot_ref', 'cpmk_id', 'ref_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order');
    }

    public function scopeSearchCPMK($query, $search, $withBobot = false)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchClean, $withBobot) {

            if ($withBobot == false) {
                // --- 1. PENCARIAN TEKS DASAR ---
                $q->where('cpmks.kode_cpmk', 'like', $searchTerm)
                    ->orWhere('cpmks.kode_cpmk', 'like', $searchClean)
                    ->orWhere('cpmks.deskripsi', 'like', $searchTerm);

                if (is_numeric($search)) {
                    $q->orWhere('cpmks.id', 'like', $search);
                }
            } else {
                // --- 4. PENCARIAN TOTAL BOBOT (Langsung dari CPMK) ---
                if (preg_match('/(\d+)\s*(|%|pers|bob|tot)/i', $search, $matches)) {
                    $weight = $matches[1];
                    $q->orWhereExists(function ($sq) use ($weight) {
                        $sq->select(DB::raw(1))
                            ->from('cpmk_pivot_scpmk')
                            ->join('sub_cpmks', 'cpmk_pivot_scpmk.scpmk_id', '=', 'sub_cpmks.id')
                            ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id')
                            ->groupBy('cpmk_pivot_scpmk.cpmk_id')
                            ->havingRaw('SUM(sub_cpmks.bobot) = ?', [$weight]);
                    });
                }
            }
        });
    }

    public function scopeSearchCPMKSmart($query, $search, $withBobot = false)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $query->searchCPMK($search, $withBobot);

        if ($withBobot) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);

        return $query->orWhere(function ($q) use ($search, $searchTerm, $searchLower) {

            // ===== CPL =====
            $q->orWhereExists(function ($sq) use ($searchTerm) {
                $sq->select(DB::raw(1))
                    ->from('cpls')
                    ->join('cpmk_pivot_cpl', 'cpls.id', '=', 'cpmk_pivot_cpl.cpl_id')
                    ->whereColumn('cpmk_pivot_cpl.cpmk_id', 'cpmks.id')
                    ->where(function ($sub) use ($searchTerm) {
                        $sub->where('cpls.deskripsi', 'like', $searchTerm)
                            ->orWhere('cpls.kode_cpl', 'like', $searchTerm);
                    });
            });

            $q->orWhereRaw("
                (
                    SELECT GROUP_CONCAT(cpls.deskripsi SEPARATOR ' ')
                    FROM cpls
                    JOIN cpmk_pivot_cpl
                        ON cpls.id = cpmk_pivot_cpl.cpl_id
                    WHERE cpmk_pivot_cpl.cpmk_id = cpmks.id
                ) LIKE ?
            ", [$searchTerm]);

            // ===== Jumlah SCPMK =====
            if (preg_match('/(\d+)\s*(pert|scpm|sub-?c)/i', $search, $m)) {
                $q->orWhereExists(function ($sq) use ($m) {
                    $sq->select(DB::raw(1))
                        ->from('cpmk_pivot_scpmk')
                        ->whereColumn('cpmk_pivot_scpmk.cpmk_id', 'cpmks.id')
                        ->groupBy('cpmk_pivot_scpmk.cpmk_id')
                        ->havingRaw('COUNT(*) = ?', [(int) $m[1]]);
                });
            }

            // ===== Jumlah CPL =====
            if (preg_match('/^(\d+)\s*(cpl|capaian)$/i', $search, $m)) {
                $q->orWhereExists(function ($sq) use ($m) {
                    $sq->select(DB::raw(1))
                        ->from('cpmk_pivot_cpl')
                        ->whereColumn('cpmk_pivot_cpl.cpmk_id', 'cpmks.id')
                        ->groupBy('cpmk_pivot_cpl.cpmk_id')
                        ->havingRaw('COUNT(*) = ?', [(int) $m[1]]);
                });
            }

            // ===== Tanggal =====
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(cpmks.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(cpmks.updated_at, '$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(cpmks.created_at, '$format')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereRaw("LOWER(DATE_FORMAT(cpmks.updated_at, '$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });

        });
    }
}
