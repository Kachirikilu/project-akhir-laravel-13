<?php

namespace App\Models\Auth;

use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Kelas\KelasSesi;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = [
        'user_id',
        'pr_id',
        'name',
        'nip',
        'nidn',
        'nidk',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'no_hp',
        'is_wa_active',
        'wa_limit',
        'no_karpeg',
        'pangkat_terakhir',
        'golongan_terakhir',
        'tmt_golongan',
        'jabatan_fungsional',
        'tmt_jabatan',
        'status',
    ];

    public function rps(): BelongsToMany
    {
        return $this->belongsToMany(RPS::class, 'rps_pivot_dosen', 'dosen_id', 'rps_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order'])
            ->withTimestamps();
    }

    public function scpmks(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'dosen_pivot_scpmk', 'dosen_id', 'scpmk_id')
            ->withPivot(['rps_id', 'sort_order'])
            ->withTimestamps();
    }

    public function sesiMengajars(): BelongsToMany
    {
        return $this->belongsToMany(KelasSesi::class, 'sesi_pivot_dosen', 'dosen_id', 'sesi_id')
            ->withPivot(['peran', 'is_ketua', 'sort_order']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id');
    }

    protected function nidnNidk(): Attribute
    {
        return Attribute::get(function () {
            $nidn = $this->nidn ?? '---';
            $nidk = $this->nidk ?? '---';
            return "NIDN: {$nidn} / NIDK: {$nidk}";
        });
    }

    // protected function countRps(): Attribute
    // {
    //     return Attribute::get(function () {
    //         return $this->rps->count();
    //     });
    // }

    // protected function totalSks(): Attribute
    // {
    //     return Attribute::make(
    //         get: function () {
    //             return (float) $this->rps->sum(function ($rps) {
    //                 return $rps->mk_rel() ? $rps->mk_rel()->sum('sks_kuliah') : 0;
    //             });
    //         }
    //     );
    // }

    protected function noWa(): Attribute
    {
        return Attribute::get(function () {
            $phone = $this->no_hp;
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }

            return $phone;
        });
    }
    protected function waAktif(): Attribute
    {
        return Attribute::get(function () {
            return $this->is_wa_active;
        });
    }

    protected static function booted()
    {
        static::saving(function ($dosen) {
            if ($dosen->nip) {
                $exists = DB::table('admins')
                    ->where('nip', $dosen->nip)
                    ->exists();

                if ($exists) {
                    throw new \Exception("NIP {$dosen->nip} sudah digunakan oleh Admin!");
                }
            }
        });
    }

    public function scopeSearchDosen($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
            $fields = ['name', 'nip', 'nidn', 'nidk', 'nik', 'status'];
            foreach ($fields as $field) {
                $q->orWhere("dosens.$field", 'like', $searchTerm);
            }
            if (is_numeric($search)) {
                $q->orWhere('dosens.id', $search);
            }
            $q->orWhereHas('user', function ($u) use ($searchTerm, $searchLower) {
                $u->where('email', 'like', $searchTerm);
            });
            $q->orWhereHas('pr_rel', function ($p) use ($searchTerm) {
                $p->where('nama_pr', 'like', $searchTerm)
                    ->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
                        $j->where('nama_dp', 'like', $searchTerm)
                            ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm])
                            ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
                                $f->where('nama_fk', 'like', $searchTerm)
                                    ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
                            });
                    });
            });
        });
    }

    // public function scopeSearchDosen($query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
    //         $fields = ['name', 'nip', 'nidn', 'nidk', 'nik', 'status'];
    //         foreach ($fields as $field) {
    //             $q->orWhere("dosens.$field", 'like', $searchTerm);
    //         }
    //         if (is_numeric($search)) {
    //             $q->orWhere('dosens.id', $search);
    //         }
    //         $q->orWhereHas('user', function ($u) use ($searchTerm, $searchLower) {
    //             $u->where('email', 'like', $searchTerm)
    //                 ->orWhere(function ($dq) use ($searchTerm, $searchLower) {
    //                     $dq->whereRaw("DATE_FORMAT(users.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                         ->orWhereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                         ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
    //                         ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W, %d %M %Y')) LIKE ?", [$searchLower])
    //                         ->orWhereRaw("DATE_FORMAT(users.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                         ->orWhereRaw("DATE_FORMAT(users.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                         ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a, %d %b %Y')) LIKE ?", [$searchLower])
    //                         ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W, %d %M %Y')) LIKE ?", [$searchLower]);
    //                 });
    //         });
    //         $q->orWhereHas('pr_rel', function ($p) use ($searchTerm) {
    //             $p->where('nama_pr', 'like', $searchTerm)
    //                 ->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
    //                     $j->where('nama_dp', 'like', $searchTerm)
    //                         ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm])
    //                         ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
    //                             $f->where('nama_fk', 'like', $searchTerm)
    //                                 ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm]);
    //                         });
    //                 });
    //         });
    //     });
    // }
}
