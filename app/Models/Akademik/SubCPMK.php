<?php

namespace App\Models\Akademik;

use App\Models\Auth\Dosen;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCPMK extends Model
{
    use SoftDeletes;

    public static array $UTS_FIELDS = [];

    public static array $UAS_FIELDS = [];

    protected static function booted()
    {
        self::$UTS_FIELDS = config('app.uts_fields', ['UTS', 'EVALUASI AWAL']);
        self::$UAS_FIELDS = config('app.uas_fields', ['UAS', 'EVALUASI AKHIR', 'LAPORAN AKHIR', 'HASIL PROYEK', 'HASIL PROJEK']);
    }

    protected $table = 'sub_cpmks';

    protected $guarded = ['id'];

    // protected $appends = ['kode', 'tugas', 'w_tugas', 'w_mandiri'];

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'cpmk_pivot_scpmk', 'scpmk_id', 'cpmk_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'scpmk_pivot_ref', 'scpmk_id', 'ref_id')
            ->withPivot('sort_order');
    }

    // public function dosens(): BelongsToMany
    // {
    //     return $this->belongsToMany(Dosen::class, 'dosen_pivot_scpmk', 'scpmk_id', 'dosen_id')
    //         ->withPivot(['rps_id', 'sort_order'])
    //         ->withTimestamps()
    //         ->orderBy('sort_order');
    // }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_scpmk);
        });
    }

    protected function tugas(): Attribute
    {
        return Attribute::get(fn () => $this->deskripsi_tugas);
    }

    protected function wTugas(): Attribute
    {
        return Attribute::get(fn () => $this->waktu_tugas ?? 60);
    }

    protected function wMandiri(): Attribute
    {
        return Attribute::get(fn () => $this->waktu_mandiri ?? 60);
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

    public function bobotFormat(): Attribute
    {
        return Attribute::get(function () {
            $bobot = $this->bobot ?? null;
            if ($bobot === null) {
                return '-';
            }
            if ($bobot % 1 == 0) {
                return (int) $bobot;
            }

            return number_format($bobot, 2);
        });
    }

    public function scopeSearchSCPMK($query, $search, $withBobot = false)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchClean, $withBobot) {

            if ($withBobot == false) {
                $q->where('sub_cpmks.kode_scpmk', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.kode_scpmk', 'like', $searchClean)
                    ->orWhere('sub_cpmks.deskripsi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.materi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.metodologi', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.indikator', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.metode', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.deskripsi_tugas', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.waktu_tugas', 'like', $searchTerm)
                    ->orWhere('sub_cpmks.waktu_mandiri', 'like', $searchTerm);

                if (is_numeric($search)) {
                    $q->orWhere('sub_cpmks.id', 'like', $search);
                }
            }

            $searchConverted = str_replace(',', '.', $searchTerm);
            $q->orWhere('sub_cpmks.bobot', 'like', '%'.$searchConverted.'%');
        });
    }

    // public function scopeSearchSCPMK($query, $search, $withBobot = false)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean, $withBobot) {

    //         if ($withBobot == false) {

    //             $q->where('sub_cpmks.kode_scpmk', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.kode_scpmk', 'like', $searchClean)
    //                 ->orWhere('sub_cpmks.deskripsi', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.materi', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.metodologi', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.indikator', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.deskripsi_tugas', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.waktu_tugas', 'like', $searchTerm)
    //                 ->orWhere('sub_cpmks.waktu_mandiri', 'like', $searchTerm);

    //             $q->orWhere(function ($enumQ) use ($searchTerm, $searchLower) {
    //                 if (str_contains('ujian tengah semester', $searchLower) || str_contains('uts', $searchLower)) {
    //                     $enumQ->orWhere('sub_cpmks.metode', 'UTS');
    //                 }
    //                 if (str_contains('ujian akhir semester', $searchLower) || str_contains('uas', $searchLower)) {
    //                     $enumQ->orWhere('sub_cpmks.metode', 'UAS');
    //                 }
    //                 if ($searchLower === 'ujian') {
    //                     $enumQ->orWhereIn('sub_cpmks.metode', ['UTS', 'UAS']);
    //                 }
    //                 $enumQ->orWhere('sub_cpmks.metode', 'like', $searchTerm);
    //             });

    //             if (is_numeric($search)) {
    //                 $q->orWhere('sub_cpmks.id', 'like', $search);
    //             }

    //             $q->orWhere(function ($dq) use ($searchLower, $searchTerm) {
    //                 $dq->whereRaw("DATE_FORMAT(sub_cpmks.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(sub_cpmks.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.created_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("DATE_FORMAT(sub_cpmks.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(sub_cpmks.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(sub_cpmks.updated_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
    //             });
    //         }

    //         $searchConverted = str_replace(',', '.', $searchTerm);
    //         $q->orWhere('sub_cpmks.bobot', 'like', '%'.$searchConverted.'%');
    //     });
    // }

    /**
     * Cek apakah item adalah UTS atau setara UTS
     */
    public static function isUTS($method, $text = ''): bool
    {
        $method = strtoupper($method ?? '');
        $text = strtoupper($text);

        return in_array($method, self::$UTS_FIELDS, true)
            || str_contains($text, 'UTS')
            || str_contains($text, 'EVALUASI AWAL');
    }

    /**
     * Cek apakah item adalah UAS atau setara UAS
     */
    public static function isUAS($method, $text = ''): bool
    {
        $method = strtoupper($method ?? '');
        $text = strtoupper($text);

        return in_array($method, self::$UAS_FIELDS, true)
            || str_contains($text, 'UAS')
            || str_contains($text, 'EVALUASI AKHIR')
            || str_contains($text, 'LAPORAN AKHIR')
            || str_contains($text, 'HASIL PROYEK')
            || str_contains($text, 'HASIL PROJEK');
    }
}
