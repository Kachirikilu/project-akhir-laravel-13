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
            $gg = $this->ganjil_genap;
            $thn = $this->tahun_blok;

            return "{$kodeKls}-{$lblKls}-{$gg}-{$thn}";
        });
    }

    protected function labelFull(): Attribute
    {
        return Attribute::get(fn () => $this->label_kelas.' '.$this->kode_wilayah);
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

    public function scopeSearchKelasJadwal($query, $search)
    {
        $searchTerm = '%'.$search.'%';
        $searchLower = strtolower($search);
        $searchCleanTerm = '%'.preg_replace('/[^A-Za-z0-9]/', '', $search).'%';
        
        return $query->where(function ($q) use ($searchLower, $search, $searchTerm, $searchCleanTerm) {
            $q->where(function ($inner) use ($searchTerm, $searchCleanTerm) {
                $inner->where('kelas_jadwals.label_kelas', 'like', $searchTerm)
                    ->orWhere('kelas_jadwals.kode_wilayah', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT(COALESCE(kelas_jadwals.label_kelas, ''), COALESCE(kelas_jadwals.kode_wilayah, '')) LIKE ?", [$searchCleanTerm]);
            })
                ->orWhereHas('kelas_rel', function ($kq) use ($search) {
                    $kq->searchKelas($search);
                })
                ->orWhereHas('kelas_rel.rps_rel.mk_rel', function ($mq) use ($searchCleanTerm) {
                    $mq->where('kode_semester', 'like', $searchCleanTerm);
                })
                ->orWhere('kelas_jadwals.hari_pelaksanaan', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.jam_mulai', 'like', $searchTerm)
                ->orWhere('kelas_jadwals.jam_berakhir', 'like', $searchTerm)
                ->orWhere(function ($dq) use ($searchLower, $searchTerm) {
                    $dq->whereRaw("DATE_FORMAT(kelas_jadwals.tanggal_mulai, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.tanggal_mulai, '%Y-%m-%d') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(kelas_jadwals.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
                        ->orWhereRaw("LOWER(DATE_FORMAT(kelas_jadwals.created_at, '%W, %d %M %Y')) LIKE ?", ["%$searchLower%"]);
                });

            if (is_numeric($search)) {
                $q->orWhere('kelas_jadwals.id', $search)
                    ->orWhere('kelas_jadwals.kapasitas', $search);
            }
        });
    }
}
