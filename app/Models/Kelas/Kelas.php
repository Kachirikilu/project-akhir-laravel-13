<?php

namespace App\Models\Kelas;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function rps_rel(): BelongsTo
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(KelasJadwal::class, 'kelas_id');
    }

    protected function kode(): Attribute
    {
        return Attribute::get(function () {
            return preg_replace('/([A-Za-z])([0-9])/', '$1-$2', $this->kode_kelas);
        });
    }

    protected function kodeRps(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->kode);
    }

    protected function kodeMK(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->kode_mk);
    }

    protected function kodePr(): Attribute
    {
        return Attribute::get(fn () => $this->pr_rel?->kode);
    }

    protected function kelas(): Attribute
    {
        return Attribute::get(fn () => $this->nama_kelas);
    }

    protected function deskripsiKelas(): Attribute
    {
        return Attribute::get(function () {
            if (empty($this->deskripsi) || ! $this->deskripsi) {
                return $this->rps_rel?->deskripsi_rps;
            }

            return $this->deskripsi;
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(fn () => $this->pr_rel?->prodi);
    }

    protected function semester(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->semester);
    }

    protected function sks(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->sks);
    }

    protected function sksText(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->sks_text);
    }

    protected function wajib(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->wajib);
    }

    protected function wajibText(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->wajib_text);
    }

    protected function mk(): Attribute
    {
        return Attribute::get(fn () => $this->rps_rel?->mk);
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

    public function scopeSearchKelas($query, $search)
    {
        $searchTerm = '%'.$search.'%';
        $searchLower = strtolower($search);

        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->where(function ($q) use ($search, $searchTerm, $searchClean) {

            if (preg_match('/^([A-Za-z]+[0-9]+)(?:([A-Za-z])(?:([A-Za-z]*)(?:([0-9]{0,4}))?)?)?$/', $searchClean, $matches)) {

                $matchKodeKelas = $matches[1] ?? null;
                $matchLabelWilayah = $matches[2] ?? null;
                $matchKodeWilayah = $matches[3] ?? null;
                $matchTahun = $matches[4] ?? null;

                $q->where(function ($subQ) use ($matchKodeKelas) {
                    $subQ->where('kelas.kode_kelas', 'like', '%'.$matchKodeKelas.'%');
                });
            }

            $q->orWhere('kelas.kode_kelas', 'like', '%'.$searchClean.'%')
                ->orWhere('kelas.nama_kelas', 'like', $searchTerm)
                ->orWhereHas('jadwals', function ($jq) use ($searchTerm) {
                    $table = $jq->getModel()->getTable();
                    $jq->where($table.'.label_kelas', 'like', $searchTerm)
                        ->orWhere($table.'.kode_wilayah', 'like', $searchTerm);
                })

                ->orWhereHas('rps_rel', function ($rq) use ($search) {
                    $rq->searchRPS($search);
                });

            if (is_numeric($search)) {
                $q->orWhere('kelas.id', '=', $search);
            }
        });
    }

    public function scopeSearchKelasSmart($query, $search)
    {
        if (blank(trim($search))) {
            return $query;
        }

        $query->searchKelas($search);

        $search = trim($search);
        $searchTerm = "%{$search}%";
        $searchLower = strtolower($search);
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        return $query->orWhere(function ($q) use ($search, $searchTerm, $searchLower, $searchClean) {

            // Parsing kode lengkap (kelas + label + wilayah + tahun)
            if (preg_match('/^([A-Za-z]+[0-9]+)(?:([A-Za-z])(?:([A-Za-z]*)(?:([0-9]{0,4}))?)?)?$/', $searchClean, $m)) {

                $kode    = $m[1] ?? null;
                $label   = $m[2] ?? null;
                $wilayah = $m[3] ?? null;
                $tahun   = $m[4] ?? null;

                $q->orWhere(function ($sub) use ($kode, $label, $wilayah, $tahun) {

                    $sub->where('kelas.kode_kelas', 'like', "%{$kode}%");

                    if ($label) {

                        $sub->whereHas('jadwals', function ($jq) use ($label, $wilayah, $tahun) {

                            $table = $jq->getModel()->getTable();

                            $jq->where("$table.label_kelas", 'like', "%{$label}%");

                            if ($wilayah) {
                                $jq->where("$table.kode_wilayah", 'like', "%{$wilayah}%");
                            }

                            if ($tahun) {
                                $jq->where(
                                    "$table.tanggal_mulai",
                                    'like',
                                    '%'.(strlen($tahun) == 2 ? "20$tahun" : $tahun).'%'
                                );
                            }
                        });
                    }
                });
            }

            // Smart RPS (otomatis ikut Smart MK)
            $q->orWhereHas('rps_rel', fn ($rq) => $rq->searchRPSSmart($search));

            // Smart Prodi
            $q->orWhereHas('pr_rel', fn ($pq) => $pq->searchProdiSmart($search));

            // Tanggal
            $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {

                foreach (['%d/%m/%Y', '%Y-%m-%d'] as $format) {
                    $dq->orWhereRaw("DATE_FORMAT(kelas.created_at,'$format') LIKE ?", [$searchTerm])
                    ->orWhereRaw("DATE_FORMAT(kelas.updated_at,'$format') LIKE ?", [$searchTerm]);
                }

                foreach (['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'] as $format) {
                    $dq->orWhereRaw("LOWER(DATE_FORMAT(kelas.created_at,'$format')) LIKE ?", ["%{$searchLower}%"])
                    ->orWhereRaw("LOWER(DATE_FORMAT(kelas.updated_at,'$format')) LIKE ?", ["%{$searchLower}%"]);
                }
            });
        });
    }
}
