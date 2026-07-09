<?php

namespace App\Models\Akademik;

use App\Models\Kelas\Kelas;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RPS extends Model
{
    use SoftDeletes;

    protected $table = 'rps';

    protected $guarded = ['id'];

    // protected $appends = ['kode', 'mk', 'level_mk', 'revisi_day', 'count_scpmk'];

    protected $casts = [
        'revisi' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function cpmks(): BelongsToMany
    {
        return $this->belongsToMany(CPMK::class, 'rps_pivot_cpmk', 'rps_id', 'cpmk_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order')
            ->withTimestamps();
    }

    // public function scpmks()
    // {
    //     return $this->belongsToMany(SubCPMK::class, 'cpmk_pivot_scpmk', 'cpmk_id', 'scpmk_id')
    //         ->orderBy('sort_order')
    //         ->withPivot('sort_order')
    //         ->withTimestamps();
    // }

    // public function cpls(): BelongsToMany
    // {
    //     return $this->belongsToMany(CPL::class, 'rps_pivot_cpl', 'rps_id', 'cpl_id')
    //         ->withPivot('sort_order')
    //         ->orderBy('sort_order');
    // }

    public function refs(): BelongsToMany
    {
        return $this->belongsToMany(Referensi::class, 'rps_pivot_ref', 'rps_id', 'ref_id')
            ->withPivot('sort_order')
            ->orderBy('sort_order');
    }

    public function tim_dosens(): BelongsToMany
    {
        return $this->belongsToMany(
            TimDosen::class,
            'rps_pivot_tim_dosen',
            'rps_id',
            'tim_dosen_id'
        )
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'rps_id');
    }

    public function nilai_mahasiswas()
    {
        return $this->hasMany(
            NilaiMahasiswa::class,
            'mahasiswa_id'
        );
    }

    // public function getDosensSubCpmk($subCpmkId)
    // {
    //     $subCpmk = $this->scpmks()->find($subCpmkId);

    //     if ($subCpmk && $subCpmk->dosens->isNotEmpty()) {
    //         return $subCpmk->dosens;
    //     }

    //     return $this->dosens;
    // }

    public function getAllScpmkAttribute()
    {
        if ($this->cpmks->isEmpty()) {
            return collect();
        }

        return $this->cpmks->flatMap(function ($cpmk) {
            return $cpmk->scpmks->map(function ($scpmk) use ($cpmk) {
                if (! isset($scpmk->cpmk_list)) {
                    $scpmk->cpmk_list = collect();
                }
                $scpmk->cpmk_list->push($cpmk);

                return $scpmk;
            });
        })->values();
    }

    protected function scpmkAtr(): Attribute
    {
        return Attribute::get(function () {
            $allScpmk = $this->all_scpmk;

            $hasUts = $allScpmk->contains(fn ($i) => Str::contains($i->deskripsi ?? '', SubCPMK::$UTS_FIELDS, true) || Str::contains($i->metode ?? '', SubCPMK::$UTS_FIELDS, true));
            $hasUas = $allScpmk->contains(fn ($i) => Str::contains($i->deskripsi ?? '', SubCPMK::$UAS_FIELDS, true) || Str::contains($i->metode ?? '', SubCPMK::$UAS_FIELDS, true));

            $program = collect();
            $scpmkIndex = 0;

            for ($p = 1; $p <= 16; $p++) {

                $isAssignedToMeeting = function ($dosen, $pertemuanKe) use ($p) {
                    if (is_null($pertemuanKe)) {
                        return true;
                    }
                    $pertemuanArr = is_array($pertemuanKe) ? $pertemuanKe : json_decode($pertemuanKe, true);

                    return in_array($p, $pertemuanArr ?? []);
                };

                if ($p == 8 && ! $hasUts) {
                    $item = (object) ['kode' => 'UTS', 'kode_cpmk' => 'CPMK-UTS', 'bobot' => (float) $this->bobot_uts, 'metode' => 'UTS', 'deskripsi' => 'Ujian Tengah Semester'];
                } elseif ($p == 16 && ! $hasUas) {
                    $item = (object) ['kode' => 'UAS', 'kode_cpmk' => 'CPMK-UAS', 'bobot' => (float) $this->bobot_uas, 'metode' => 'UAS', 'deskripsi' => 'Ujian Akhir Semester'];
                } else {
                    $rawItem = $allScpmk->get($scpmkIndex);
                    if ($rawItem) {
                        $item = clone $rawItem;
                        $scpmkIndex++;
                    } else {
                        $item = (object) ['kode' => '-', 'kode_cpmk' => '-', 'deskripsi' => 'Materi belum ditentukan', 'bobot' => 0];
                    }
                }

                if (! isset($item->kode_cpmk) || $item->kode_cpmk == '-') {
                    $item->kode_cpmk = isset($item->cpmk_list) ? $item->cpmk_list->pluck('kode')->unique()->implode(', ') : '-';
                }
                $program->push($item);
            }

            $totalInput = $program->sum(fn ($i) => (float) ($i->bobot ?? 0));
            if ($totalInput > 0) {
                $program->transform(function ($item) use ($totalInput) {
                    $item->bobot_normalisasi = round(((float) ($item->bobot ?? 0) / $totalInput) * 100, 2);

                    return $item;
                });
                $totalNormalisasi = $program->sum(fn ($i) => (float) $i->bobot_normalisasi);
                $diff = round(100 - $totalNormalisasi, 2);
                if ($diff != 0 && $last = $program->last()) {
                    $last->bobot_normalisasi = round($last->bobot_normalisasi + $diff, 2);
                }
            }

            return $program;
        });
    }

    public function getAllRefsAttribute()
    {
        $refsRps = $this->refs ?? collect();
        $refsCpmk = $this->cpmks->flatMap->refs ?? collect();
        $refsSubCpmk = $this->cpmks->flatMap(function ($cpmk) {
            return $cpmk->scpmks->flatMap->refs;
        });

        return $refsRps
            ->concat($refsCpmk)
            ->concat($refsSubCpmk)
            ->unique('id')
            ->values();
    }

    public function mk_rel(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id')->withTrashed();
    }

    protected function kodeMk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->kode);
    }

    // protected function digitAkademik(): Attribute
    // {
    //     return Attribute::get(function () {
    //         $ta1 = (int) substr($this->akademik, 1, 4);
    //         $suffixTahun1 = match (true) {
    //             $ta1 >= 3000 => $ta1,
    //             $ta1 >= 2100 => substr((string) $ta1, -3),
    //             $ta1 >= 2000 => substr((string) $ta1, -2),
    //             default => (string) $ta1,
    //         };

    //         $ta2 = (int) substr($this->akademik, 5, 8);
    //         $suffixTahun2 = match (true) {
    //             $ta2 >= 3000 => $ta2,
    //             $ta2 >= 2100 => substr((string) $ta2, -3),
    //             $ta2 >= 2000 => substr((string) $ta2, -2),
    //             default => (string) $ta2,
    //         };

    //         return "{$suffixTahun1}{$suffixTahun2}";
    //     });
    // }

    protected function digitAkademik(): Attribute
    {
        return Attribute::get(function () {
            $formatTahun = function ($ta) {
                return match (true) {
                    $ta >= 3000 => (string) $ta,
                    $ta >= 2100 => substr((string) $ta, -3),
                    $ta >= 2000 => str_pad(substr((string) $ta, -2), 2, '0', STR_PAD_LEFT),
                    default => (string) $ta,
                };
            };

            $ta1 = (int) substr($this->akademik, 0, 4);
            $ta2 = (int) substr($this->akademik, 5, 4);

            $suffixTahun1 = $formatTahun($ta1);
            $suffixTahun2 = $formatTahun($ta2);

            return "{$suffixTahun1}{$suffixTahun2}";
        });
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $kodeMK = $this->kode_mk;
            if (! $kodeMK || ! $this->akademik) {
                return null;
            }
            $digitAkademik = $this->digit_akademik;
            $gg = $this->mk_rel?->kode_semester;

            return "{$digitAkademik}-{$gg}-{$kodeMK}";
        });
    }

    protected function deskripsiRps(): Attribute
    {
        return Attribute::get(function () {
            if (empty($this->deskripsi) || ! $this->deskripsi) {
                return $this->mk_rel?->deskripsi;
            }

            return $this->deskripsi;
        });
    }

    protected function mk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->mk);
    }

    protected function kodeSemester(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->kode_semester);
    }

    protected function wajib(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->is_wajib);
    }

    protected function wajibText(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->wajib_text);
    }

    protected function semester(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->semester);
    }

    protected function sks(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->sks);
    }

    protected function sksText(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->sks_text);
    }

    protected function sksFull(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->sks_full);
    }

    protected function levelMk(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->level_mk);
    }

    protected function rps(): Attribute
    {
        return Attribute::get(fn () => $this->mk_rel?->mk ?? 'Tanpa MK'
        );
    }

    protected function rpsWithKode(): Attribute
    {
        return Attribute::get(fn () => $this->kode.' - '.$this->mk_rel?->mk ?? 'Tanpa MK'
        );
    }

    protected function revisiDay(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->revisi) {
                return null;
            }

            return $this->revisi->translatedFormat('D, d M Y');
        });
    }

    protected function countCpmk(): Attribute
    {
        return Attribute::get(function () {
            return $this->cpmks->count();
        });
    }

    protected function countScpmk(): Attribute
    {
        return Attribute::get(function () {
            return $this->cpmks->sum(function ($cpmk) {
                return $cpmk->scpmks->count();
            });
        });
    }

    protected function totalBobot(): Attribute
    {
        return Attribute::get(function () {
            $totalSubCpmk = $this->cpmks->sum(function ($cpmk) {
                return (float) ($cpmk->scpmks->sum('bobot') ?? 0);
            });

            $allScpmk = $this->cpmks->flatMap->scpmks;

            $hasUTS = $allScpmk->contains(function ($scpmk) {
                $method = $scpmk->metode ?? '';
                $text = $scpmk->deskripsi ?? '';

                return Str::contains($method, SubCPMK::$UTS_FIELDS, ignoreCase: true) ||
                       Str::contains($text, SubCPMK::$UTS_FIELDS, ignoreCase: true);
            });

            $hasUAS = $allScpmk->contains(function ($scpmk) {
                $method = $scpmk->metode ?? '';
                $text = $scpmk->deskripsi ?? '';

                return Str::contains($method, SubCPMK::$UAS_FIELDS, ignoreCase: true) ||
                       Str::contains($text, SubCPMK::$UAS_FIELDS, ignoreCase: true);
            });

            $uts = $hasUTS ? 0 : (float) ($this->bobot_uts ?? 0);
            $uas = $hasUAS ? 0 : (float) ($this->bobot_uas ?? 0);

            return $totalSubCpmk + $uts + $uas;
        });
    }

    protected function draf(): Attribute
    {
        return Attribute::get(fn () => $this->is_draf);
    }

    protected function drafText(): Attribute
    {
        return Attribute::get(fn () => $this->is_draf == 1 ? 'Draf' : 'Aktif');
    }

    protected function drafFull(): Attribute
    {
        return Attribute::get(fn () => 'Status: '.$this->drafText);
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

    public function scopeSearchRPS($query, $search, $withBobot = false)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        // 1. Inisialisasi
        $yearPart = null;
        $semesterPart = null;
        $mkPart = null;

        if (preg_match('/^(\d+?)(01|02)([A-Za-z].*)?$/i', $searchClean, $matches)) {
            $yearPart = $matches[1];
            $semesterPart = $matches[2];
            $mkPart = $matches[3] ?? null;
        } elseif (preg_match('/^[A-Za-z]/', $searchClean)) {
            $mkPart = $searchClean;
        } elseif (is_numeric($searchClean)) {
            $yearPart = $searchClean;
        }

        if ($yearPart && strlen($yearPart) >= 4) {
            $yearPart = substr($yearPart, -2);
        }

        return $query->where(function ($q) use ($yearPart, $semesterPart, $mkPart, $search, $withBobot) {
            $mkPartClean = $mkPart ? preg_replace('/[^A-Za-z0-9]/', '', $mkPart) : null;

            if ($withBobot == false) {

                $q->where(function ($group) use ($yearPart, $semesterPart, $mkPartClean) {

                    // A. Filter Tahun (Mencari di kolom akademik)
                    if ($yearPart !== null) {
                        $group->where(function ($yq) use ($yearPart) {
                            if (strlen($yearPart) >= 4) {
                                $half = strlen($yearPart) / 2;
                                $y1 = substr($yearPart, 0, $half);
                                $y2 = substr($yearPart, $half);
                                $yq->where('akademik', 'like', "%$y1%")->where('akademik', 'like', "%$y2%");
                            } else {
                                $yq->where('akademik', 'like', "%$yearPart%");
                            }
                        });
                    }

                    // B. Filter Semester (Ganjil 01 / Genap 02)
                    if ($semesterPart !== null) {
                        $group->whereHas('mk_rel', function ($mq) use ($semesterPart) {
                            if ($semesterPart === '01') {
                                $mq->whereRaw('semester % 2 != 0');
                            } elseif ($semesterPart === '02') {
                                $mq->whereRaw('semester % 2 = 0');
                            }
                        });
                    }

                    // C. Filter Mata Kuliah
                    if ($mkPartClean !== null) {
                        $group->whereHas('mk_rel', function ($mq) use ($mkPartClean) {
                            $mq->searchMK($mkPartClean);
                        });
                    }
                });

                // 4. ID RPS
                if (is_numeric($search)) {
                    $q->orWhere('rps.id', 'like', $search);
                }

            } else {
                // --- 5. PENCARIAN TOTAL BOBOT (Toleran Typo/Singkat) ---
                if (preg_match('/(\d+)\s*(|%|pers|bob|tot)/i', $search, $matches)) {
                    $weight = $matches[1];
                    $q->orWhereRaw('(
                    COALESCE((
                        SELECT SUM(sub_cpmks.bobot)
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                    ), 0)
                    + IF(
                        EXISTS(
                            SELECT 1
                            FROM rps_pivot_cpmk
                            JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                            JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                            WHERE rps_pivot_cpmk.rps_id = rps.id
                            AND UPPER(sub_cpmks.metode) = \'UTS\'
                        ), 0, COALESCE(rps.bobot_uts, 0)
                    )
                    + IF(
                        EXISTS(
                            SELECT 1
                            FROM rps_pivot_cpmk
                            JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                            JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                            WHERE rps_pivot_cpmk.rps_id = rps.id
                            AND UPPER(sub_cpmks.metode) IN (\'UAS\', \'LAPORAN AKHIR\', \'HASIL PROYEK\', \'HASIL PROJEK\')
                        ), 0, COALESCE(rps.bobot_uas, 0)
                    )
                ) = ?', [$weight]);
                }

            }
        });
    }

    public function scopeSearchRPSSmart($query, $search, $withBobot = false)
    {
        if (blank(trim($search))) {
            return $query;
        }

        // Gunakan seluruh logic SearchRPS
        $query->searchRPS($search, $withBobot);

        if ($withBobot) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);

        return $query->orWhere(function ($q) use ($search, $searchTerm, $searchLower) {

            // Tanggal
            $q->where(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(rps.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(rps.updated_at, '$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(rps.created_at, '$format')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereRaw("LOWER(DATE_FORMAT(rps.updated_at, '$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });

            // Akademik
            $q->orWhere('akademik', 'like', $searchTerm);

            $q->orWhereHas('mk_rel', function ($mq) use ($search) {
                $mq->searchMKSmart($search);
            });

            // Jumlah CPMK
            if (preg_match('/(\d+)\s*(cpmk|cpm)$/i', $search, $m)) {
                $q->orWhereHas('cpmks', fn ($q) => $q, '=', (int) $m[1]);
            }

            // Jumlah SCPMK
            if (preg_match('/(\d+)\s*(pert|scpm|sub-?c)/i', $search, $m)) {
                $q->orWhereRaw(
                    '(
                    SELECT COUNT(*)
                    FROM rps_pivot_cpmk rpc
                    JOIN cpmk_pivot_scpmk cps
                        ON rpc.cpmk_id = cps.cpmk_id
                    WHERE rpc.rps_id = rps.id
                ) = ?',
                    [(int) $m[1]]
                );
            }

            // Status
            match ($searchLower) {
                'draf', 'draft', 'konsep', 'aseli' => $q->orWhere('is_draf', true),

                'aktif', 'active', 'publish', 'siap' => $q->orWhere('is_draf', false),

                default => null,
            };
        });
    }

}
