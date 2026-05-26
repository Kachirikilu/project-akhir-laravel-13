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

    protected function kodeKelas(): Attribute
    {
        return Attribute::get(function () {
            return $this->kelas_rel->kode;
        });
    }

    protected function ganjilGenap(): Attribute
    {
        return Attribute::get(function () {
            $kode = $this->kelas_rel->rps_rel?->mk_rel?->kode_semester;

            return $kode ? (int) $kode : null;
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

            return "{$mulai} - {$akhir}";
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

            return $akhir ? "{$mulai} - {$akhir}" : $mulai;
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

    // public function scopeSearchKelasJadwal($query, $search)
    // {
    //     if (blank($search)) {
    //         return $query;
    //     }
    //     $searchTerm = '%'.$search.'%';
    //     $searchLower = strtolower($search);
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {

    //         // ID exact
    //         if (is_numeric($search)) {
    //             $q->orWhere('id', $search);
    //         }

    //         // Password
    //         $q->orWhere('password', 'LIKE', $searchTerm)
    //             ->orWhere('label_kelas', 'LIKE', $searchTerm)
    //             ->orWhere('kode_wilayah', 'LIKE', $searchTerm)
    //             ->orWhereRaw(
    //                 "CONCAT(label_kelas, ' ', kode_wilayah) LIKE ?",
    //                 [$searchTerm]
    //             )
    //             ->orWhere('hari_pelaksanaan', 'LIKE', $searchTerm)
    //             ->orWhereHas('kelas_rel', function ($rq) use ($search, $searchClean) {
    //                 $rq->searchKelas($search);
    //             })

    //         // Kapasitas
    //             ->orWhereRaw(
    //                 'CAST(kapasitas AS CHAR) LIKE ?',
    //                 [$searchTerm]
    //             )

    //         // Jam mulai
    //             ->orWhereRaw(
    //                 "TIME_FORMAT(jam_mulai, '%H:%i') LIKE ?",
    //                 [$searchTerm]
    //             )

    //         // Jam berakhir
    //             ->orWhereRaw(
    //                 "TIME_FORMAT(jam_berakhir, '%H:%i') LIKE ?",
    //                 [$searchTerm]
    //             )

    //         // Gabungan jam: "08:00 - 10:30"
    //             ->orWhereRaw(
    //                 "CONCAT(
    //             TIME_FORMAT(jam_mulai, '%H:%i'),
    //             ' - ',
    //             TIME_FORMAT(jam_berakhir, '%H:%i')
    //         ) LIKE ?",
    //                 [$searchTerm]
    //             )

    //         // Tanggal mulai
    //             ->orWhereRaw(
    //                 "DATE_FORMAT(tanggal_mulai, '%d/%m/%Y') LIKE ?",
    //                 [$searchTerm]
    //             )

    //         // Tanggal berakhir
    //             ->orWhereRaw(
    //                 "DATE_FORMAT(tanggal_berakhir, '%d/%m/%Y') LIKE ?",
    //                 [$searchTerm]
    //             )

    //         // Gabungan tanggal
    //             ->orWhereRaw(
    //                 "CONCAT(
    //             DATE_FORMAT(tanggal_mulai, '%d/%m/%Y'),
    //             ' - ',
    //             DATE_FORMAT(tanggal_berakhir, '%d/%m/%Y')
    //         ) LIKE ?",
    //                 [$searchTerm]
    //             )

    //             ->orWhere(function ($dq) use ($searchLower, $searchTerm) {
    //                 $dq->whereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
    //             });
    //     });
    // }

    public function scopeSearchKelasJadwal($query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchTerm = '%'.$search.'%';
        $searchLower = '%'.strtolower($search).'%';
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {
            $q->where(function ($jq) use ($searchClean) {
                preg_match('/^([A-Za-z]+\-?\d+)(?:\-?([A-Za-z]))?(?:\-?([A-Za-z]{0,3}))?(?:\-?(\d{0,4}))?$/i', $searchClean, $matches);
                $kodeKelas = $matches[1] ?? null;
                $label = $matches[2] ?? null;
                $wilayah = $matches[3] ?? null;
                $tahun = $matches[4] ?? null;

                if ($kodeKelas) {
                    $kodeClean = preg_replace('/[^A-Za-z0-9]/', '', $kodeKelas);
                    $jq->whereHas('kelas_rel', function ($rq) use ($kodeClean, $searchClean) {
                        $rq->whereRaw("REPLACE(kode_kelas, '-', '') LIKE ?", ['%'.$kodeClean.'%'])
                            ->orWhere('kode_kelas', 'LIKE', $searchClean);
                    });
                }
                if ($label) {
                    $jq->where('label_kelas', 'LIKE', "%{$label}%");
                }
                if ($wilayah) {
                    $jq->where('kode_wilayah', 'LIKE', "%{$wilayah}%");
                }
                if ($tahun) {
                    if (strlen($tahun) >= 4) {
                        $tahun = substr($tahun, -2);
                    }
                    $jq->whereRaw('RIGHT(YEAR(tanggal_mulai), 2) LIKE ?', ["%{$tahun}%"]);
                }
                $jq->orWhereRaw("REPLACE(CONCAT(label_kelas, kode_wilayah, RIGHT(YEAR(tanggal_mulai), 2)), '-', '') LIKE ?", ['%'.$searchClean.'%']);
            })
                ->orWhere('password', 'LIKE', $searchTerm)
                ->orWhere('label_kelas', 'LIKE', $searchTerm)
                ->orWhere('kode_wilayah', 'LIKE', $searchTerm)
                ->orWhereRaw("CONCAT(label_kelas, ' ', kode_wilayah) LIKE ?", [$searchTerm])
                ->orWhere('hari_pelaksanaan', 'LIKE', $searchTerm)
                ->orWhereRaw('CAST(kapasitas AS CHAR) LIKE ?', [$searchTerm])
                ->orWhere(function ($jq) use ($searchTerm) {
                    $jq->whereRaw("TIME_FORMAT(jam_mulai, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("TIME_FORMAT(jam_berakhir, '%H:%i') LIKE ?", [$searchTerm])
                        ->orWhereRaw("CONCAT(TIME_FORMAT(jam_mulai, '%H:%i'), ' - ', TIME_FORMAT(jam_berakhir, '%H:%i')) LIKE ?", [$searchTerm]);
                })
                ->orWhere(function ($tq) use ($searchTerm) {
                    $tq->whereRaw("DATE_FORMAT(tanggal_mulai, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(tanggal_berakhir, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(tanggal_berakhir, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("CONCAT(DATE_FORMAT(tanggal_mulai, '%d/%m/%Y'), ' - ', DATE_FORMAT(tanggal_berakhir, '%d/%m/%Y')) LIKE ?", [$searchTerm]);
                })
                ->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%W, %d %M %Y')) LIKE ?", [$searchLower])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.updated_at, '%W, %d %M %Y')) LIKE ?", [$searchLower]);
                });
            if (is_numeric($search)) {
                $q->orWhere('kelas_jadwals.id', $search);
            }
        });
    }
}
