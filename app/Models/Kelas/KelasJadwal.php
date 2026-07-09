<?php

namespace App\Models\Kelas;

use App\Models\Auth\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class KelasJadwal extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function kelas_rel(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function sesis(): HasMany
    {
        return $this->hasMany(KelasSesi::class, 'kj_id')->orderBy('pertemuan_ke');
    }

    public function mahasiswas(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'mahasiswa_kelas', 'kj_id', 'mahasiswa_id')
            ->withTimestamps();
    }

    public function nilais()
    {
        return $this->hasMany(
            NilaiMahasiswa::class,
            'kj_id'
        );
    }

    protected function countMahasiswa(): Attribute
    {
        return Attribute::get(function () {
            return $this->mahasiswas->count();
        });
    }

    protected function countSesi(): Attribute
    {
        return Attribute::get(function () {
            return $this->sesis->count();
        });
    }

    protected function countMhsJadwal(): Attribute
    {
        return Attribute::get(function () {

            $mahasiswa = $this->count_mahasiswa;
            $kapasitas = $this->kapasitas;

            return $mahasiswa.' / '.$kapasitas;
        });
    }

    protected function kodeKelas(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->kode;
        });
    }

    protected function kodeMk(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->kode_mk;
        });
    }

    protected function kodeRps(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->kode_rps;
        });
    }

    protected function RpsId(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_id;
        });
    }

    protected function mk(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->mk;
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->pr_rel?->prodi;
        });
    }

    protected function semester(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->semester;
        });
    }

    protected function sks(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->sks;
        });
    }

    protected function sksText(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->sks_text;
        });
    }

    protected function wajib(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->wajib;
        });
    }

    protected function wajibText(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->rps_rel?->wajib_text;
        });
    }

    protected function ganjilGenap(): Attribute
    {
        return Attribute::get(function () {
            if (empty($this->tanggal_mulai)) {
                return null;
            }

            $tanggal = Carbon::parse($this->tanggal_mulai);

            return ($tanggal->month >= 7) ? 'Ganjil' : 'Genap';
        });
    }

    protected function tahunAkademik(): Attribute
    {
        return Attribute::get(function () {

            if (empty($this->tanggal_mulai)) {
                return null;
            }

            $tanggal = Carbon::parse($this->tanggal_mulai);

            if ($tanggal->month >= 7) {
                $tahunAwal = $tanggal->year;
            } else {
                $tahunAwal = $tanggal->year - 1;
            }

            return $tahunAwal.'/'.($tahunAwal + 1);
        });
    }

    protected function tahun(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal_mulai) {
                return '-';
            }

            $mulai = Carbon::parse($this->tanggal_mulai)->format('Y');

            return $mulai;
        });
    }

    protected function tahunBlok(): Attribute
    {
        return Attribute::get(function () {
            $ta1 = (int) $this->tahun;
            $suffixTahun = match (true) {
                $ta1 >= 3000 => $ta1,
                $ta1 >= 2100 => substr((string) $ta1, -3),
                $ta1 >= 2000 => substr((string) $ta1, -2),
                default => (string) $ta1,
            };

            return $suffixTahun;
        });
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            $kodeKls = $this->kode_kelas;
            $lblKls = $this->label_kelas.'-'.$this->kode_wilayah;
            // $gg = $this->ganjil_genap;
            $thn = $this->tahun_blok;

            return "{$kodeKls}-{$lblKls}-{$thn}";
        });
    }

    protected function kodeJadwal(): Attribute
    {
        return Attribute::get(function () {
            $lblKls = $this->label_kelas.'-'.$this->kode_wilayah;
            $thn = $this->tahun_blok;

            return "{$lblKls}-{$thn}";
        });
    }

    protected function labelFull(): Attribute
    {
        return Attribute::get(fn () => $this->label_kelas.' '.$this->kode_wilayah);
    }

    protected function labelExtra(): Attribute
    {
        return Attribute::get(function () {
            $lbl = $this->label_kelas;
            $wly = $this->kode_wilayah;
            if ($wly == 'IDL') {
                $wly = 'Indralaya';
            } elseif ($wly == 'PLG') {
                $wly = 'Palembang';
            } else {
                $wly = $this->kode_wilayah;
            }

            return "{$lbl} {$wly}";
        });
    }

    protected function hari(): Attribute
    {
        return Attribute::get(fn () => $this->hari_pelaksanaan);
    }

    protected function tanggalPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->tanggal_mulai) {
                return '-';
            }

            $mulai = Carbon::parse($this->tanggal_mulai)->format('d/m/Y');
            $akhir = $this->tanggal_berakhir
                ? Carbon::parse($this->tanggal_berakhir)->format('d/m/Y')
                : 'Selesai';

            return "{$mulai} – {$akhir}";
        });
    }

    protected function jamPelaksanaan(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->jam_mulai) {
                return '-';
            }

            $mulai = Carbon::parse($this->jam_mulai)->format('H:i');
            $akhir = $this->jam_berakhir
                ? Carbon::parse($this->jam_berakhir)->format('H:i')
                : '';

            return ($akhir ? "{$mulai}–{$akhir}" : $mulai).' WIB';
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

    public function scopeSearchKelasJadwal($query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchClean) {
            $q->where(function ($jq) use ($searchClean) {

                preg_match(
                    '/^([A-Za-z]+\-?\d+)?(?:\-?([A-Za-z]))?(?:\-?([A-Za-z]{0,10}))?(?:\-?(\d{0,4}))?$/i',
                    $searchClean,
                    $matches
                );

                $kodeKelas = $matches[1] ?? null;
                $label = strtoupper($matches[2] ?? '');
                $wilayah = strtoupper($matches[3] ?? '');
                $tahun = $matches[4] ?? null;

                // Normalisasi wilayah
                if ($wilayah !== '') {
                    if (str_starts_with($wilayah, 'PAL')) {
                        $wilayah = 'PLG';
                    } elseif (str_starts_with($wilayah, 'IND')) {
                        $wilayah = 'IDL';
                    }
                }

                if ($kodeKelas) {
                    $kodeClean = preg_replace('/[^A-Za-z0-9]/', '', $kodeKelas);

                    $jq->whereHas('kelas_rel', function ($rq) use ($kodeClean, $searchClean) {
                        $rq->whereRaw("REPLACE(kode_kelas, '-', '') LIKE ?", ['%'.$kodeClean.'%'])
                            ->orWhere('kode_kelas', 'like', '%'.$searchClean.'%');
                    });
                }

                if ($label || $wilayah || $tahun) {

                    $jq->where(function ($sub) use ($label, $wilayah, $tahun) {

                        if ($label) {
                            $sub->where('label_kelas', 'like', "%{$label}%");
                        }

                        if ($wilayah) {
                            $sub->where('kode_wilayah', 'like', "%{$wilayah}%");
                        }

                        if ($tahun) {
                            if (strlen($tahun) >= 4) {
                                $tahun = substr($tahun, -2);
                            }

                            $sub->whereRaw(
                                'RIGHT(YEAR(tanggal_mulai), 2) LIKE ?',
                                ["%{$tahun}%"]
                            );
                        }
                    });
                }

                $jq->orWhereRaw("
                    REPLACE(
                        CONCAT(
                            label_kelas,
                            CASE
                                WHEN kode_wilayah = 'PLG' THEN 'PALEMBANG'
                                WHEN kode_wilayah = 'IDL' THEN 'INDRALAYA'
                                ELSE kode_wilayah
                            END,
                            RIGHT(YEAR(tanggal_mulai),2)
                        ),
                        '-',
                        ''
                    ) LIKE ?
                ", ['%'.$searchClean.'%']);

                            // Tetap dukung APLG / AIDL
                            $jq->orWhereRaw("
                    REPLACE(
                        CONCAT(label_kelas,kode_wilayah,RIGHT(YEAR(tanggal_mulai),2)),
                        '-',
                        ''
                    ) LIKE ?
                ", ['%'.$searchClean.'%']);
            });

            if (Auth::user()->admin || Auth::user()->dosen) {
                $q->orWhere('kelas_jadwals.password', $search);
            }
            if (is_numeric($search)) {
                $q->orWhere('kelas_jadwals.id', $search);
            }
        });
    }

    public function scopeSearchKelasJadwalSmart($query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        // seluruh logika dasar
        $query->searchKelasJadwal($search);

        $search = trim($search);
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);

        return $query->orWhere(function ($q) use ($search, $searchTerm, $searchLower) {

            // Smart Kelas
            $q->orWhereHas('kelas_rel', fn ($k) => $k->searchKelasSmart($search));

            // Kapasitas
            $q->orWhereRaw('CAST(kapasitas AS CHAR) LIKE ?', [$searchTerm]);

            // Jam
            $q->orWhere(function ($jq) use ($searchTerm) {
                $jq->whereRaw("TIME_FORMAT(jam_mulai,'%H:%i') LIKE ?", [$searchTerm])
                    ->orWhereRaw("TIME_FORMAT(jam_berakhir,'%H:%i') LIKE ?", [$searchTerm])
                    ->orWhereRaw("CONCAT(TIME_FORMAT(jam_mulai,'%H:%i'),' - ',TIME_FORMAT(jam_berakhir,'%H:%i')) LIKE ?", [$searchTerm]);
            });

            // Rentang tanggal
            $q->orWhere(function ($tq) use ($searchTerm) {
                foreach (['tanggal_mulai', 'tanggal_berakhir'] as $field) {
                    $tq->orWhereRaw("DATE_FORMAT($field,'%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT($field,'%Y-%m-%d') LIKE ?", [$searchTerm]);
                }

                $tq->orWhereRaw("
                    CONCAT(
                        DATE_FORMAT(tanggal_mulai,'%d/%m/%Y'),
                        ' - ',
                        DATE_FORMAT(tanggal_berakhir,'%d/%m/%Y')
                    ) LIKE ?
                ", [$searchTerm]);
            });

            // Created / Updated
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at,'$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at,'$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at,'$format')) LIKE ?", ["%{$searchLower}%"])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at,'$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });

        });
    }

    // public function scopeWhereKode($query, string $kode)
    // {
    //     return $query->whereRaw(
    //         "
    //         CONCAT(
    //             label_kelas,
    //             '-',
    //             kode_wilayah,
    //             '-',
    //             CASE
    //                 WHEN YEAR(tanggal_mulai) >= 3000
    //                     THEN CAST(YEAR(tanggal_mulai) AS CHAR)

    //                 WHEN YEAR(tanggal_mulai) >= 2100
    //                     THEN RIGHT(CAST(YEAR(tanggal_mulai) AS CHAR), 3)

    //                 WHEN YEAR(tanggal_mulai) >= 2000
    //                     THEN RIGHT(CAST(YEAR(tanggal_mulai) AS CHAR), 2)

    //                 ELSE CAST(YEAR(tanggal_mulai) AS CHAR)
    //             END
    //         ) = ?
    //         ",
    //         [$kode]
    //     );
    // }
}
