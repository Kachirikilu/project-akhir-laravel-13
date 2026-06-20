<?php

namespace App\Models\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['email', 'password', 'current_team_id'])] // Menggunakan PHP Attribute ala Laravel 13 (Name sengaja dilepas/di-comment seperti versi lama)
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;

    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'name',
        'role',
        'identity1',
        'identity2',
        'identity3',
        'nik',
        'status',
        'status_full',
        'kode_wilayah',
        'wilayah',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        // Penggabungan format date lama dan cast bawaan Laravel 13 (hashed password & 2FA)
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }

    /**
     * Scope: Filter berdasarkan lokasi prodi/departemen/fakultas
     */
    public function scopeInLocationUser($query, $type, $id)
    {
        if (! $id) {
            return $query;
        }

        return $query->where(function ($q) use ($type, $id) {
            $roles = ['admin', 'dosen', 'mahasiswa'];
            foreach ($roles as $role) {
                $q->orWhereHas($role.($type !== 'prodi' ? '.pr_rel' : ''), function ($r) use ($type, $id) {
                    if ($type === 'prodi') {
                        $r->where('pr_id', $id);
                    }
                    if ($type === 'departemen') {
                        $r->where('dp_id', $id);
                    }
                    if ($type === 'fakultas') {
                        $r->whereHas('dp_rel', fn ($j) => $j->where('fk_id', $id));
                    }
                });
            }
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // --- RELASI USER KE ROLE ---
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class);
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function pendidikans(): HasMany
    {
        return $this->hasMany(Pendidikan::class);
    }

    // --- ATTRIBUTE / ACCESSOR UTAMA USER ---
    protected function name(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->name ?? $this->email;
        });
    }

    public function role(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->admin()->exists() => 'Admin',
            $this->dosen()->exists() => 'Dosen',
            $this->mahasiswa()->exists() => 'Mahasiswa',
            default => 'User',
        });
    }

    protected function roleId(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->id;
            } elseif ($this->dosen) {
                $value = $this->dosen->id;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->id;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function nik(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->nik;
            } elseif ($this->dosen) {
                $value = $this->dosen->nik;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->nik;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function identity1(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->nip;
            } elseif ($this->dosen) {
                $value = $this->dosen->nip;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->nim;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function identity2(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->nitk;
            } elseif ($this->dosen) {
                $value = $this->dosen->nidn;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function identity3(): Attribute
    {
        return Attribute::get(function () {
            $value = $this->dosen?->nidk;

            return $value ?: null;
        });
    }

    protected function kodeWilayah(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->kode_wilayah;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->kode_wilayah;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function wilayah(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->wilayah;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->wilayah;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function tmtLahir(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->tempat_lahir;
            } elseif ($this->dosen) {
                $value = $this->dosen->tempat_lahir;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->tempat_lahir;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function tanggalLahir(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = null;

                if ($this->admin) {
                    $value = $this->admin->tanggal_lahir;
                } elseif ($this->dosen) {
                    $value = $this->dosen->tanggal_lahir;
                } elseif ($this->mahasiswa) {
                    $value = $this->mahasiswa->tanggal_lahir;
                }

                if (empty($value)) {
                    return null;
                }

                return Carbon::parse($value)->format('Y-m-d');
            }
        );
    }

    protected function tglLahir(): Attribute
    {
        return Attribute::make(
            get: function () {
                return Carbon::parse($this->tanggal_lahir)->translatedFormat('j F Y');
            }
        );
    }

    protected function gender(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->jenis_kelamin;
            } elseif ($this->dosen) {
                $value = $this->dosen->jenis_kelamin;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->jenis_kelamin;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function agama(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->agama;
            } elseif ($this->dosen) {
                $value = $this->dosen->agama;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->agama;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function noHp(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->no_hp;
            } elseif ($this->dosen) {
                $value = $this->dosen->no_hp;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->no_hp;
            }

            return empty($value) ? null : $value;
        });
    }

    protected function noWa(): Attribute
    {
        return Attribute::get(function () {
            $phone = $this->no_hp; 

            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            return $phone ?? null;
        });
    }
    
    protected function waAktif(): Attribute
    {
        return Attribute::get(function () {

            $value = false;

            if ($this->admin) {
                $value = $this->admin->is_wa_active;
            } elseif ($this->dosen) {
                $value = $this->dosen->is_wa_active;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->is_wa_active;
            }

            return empty($value) ? false : $value;
        });
    }

    protected function noHpBack(): Attribute
    {
        return Attribute::get(function () {
            $value = null;

            if ($this->admin) {
                $value = $this->admin->no_hp;
            } elseif ($this->dosen) {
                $value = $this->dosen->no_hp;
            } elseif ($this->mahasiswa) {
                $value = $this->mahasiswa->no_hp;
            }

            if (empty($value)) {
                return null;
            }
            $cleaned = preg_replace('/\D/', '', $value);
            $cleaned = preg_replace('/^(62|0)+/', '', $cleaned);

            return $cleaned;
        });
    }

    protected function status(): Attribute
    {
        return Attribute::get(fn () => $this->admin?->status ??
            $this->dosen?->status ??
            $this->mahasiswa?->status ??
            'Tidak Ada'
        );
    }

    protected function statusFull(): Attribute
    {
        return Attribute::get(fn () => 'Status: '.$this->status);
    }

    // --- ATTRIBUTE PRODI / DEPARTEMEN / FAKULTAS ---
    protected function prId(): Attribute
    {
        return Attribute::get(function () {
            return $this->admin?->pr_id
                ?? $this->dosen?->pr_id
                ?? $this->mahasiswa?->pr_id ?? 0;
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->prodi;
        });
    }

    protected function prodiPr(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->prodi_pr;
        });
    }

    protected function prodiStrata(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->prodi_strata;
        });
    }

    protected function kodePr(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->kode;
        });
    }

    protected function dpId(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->dp_id;
        });
    }

    protected function kodeDp(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->kode_dp;
        });
    }

    protected function departemen(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->departemen;
        });
    }

    protected function departemenDp(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->departemen_dp;
        });
    }

    protected function fkId(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->dp_rel?->fk_id;
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->kode_fk;
        });
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->fakkultas;
        });
    }

    protected function fakultasFk(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);

            return $profile?->pr_rel?->fakkultas_fk;
        });
    }

    // public function getWhatsappNumberAttribute()
    // {
    //     $profile = $this->admin ?: ($this->dosen ?: $this->mahasiswa);
    //     $phone = $profile?->no_hp;
    //     $phone = preg_replace('/[^0-9]/', '', $phone);
    //     if (str_starts_with($phone, '0')) {
    //         $phone = '62' . substr($phone, 1);
    //     }
    //     return $phone;
    // }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->profile_photo_path) {
                return Storage::disk('public')->url($this->profile_photo_path);
            }

            return $this->defaultProfilePhotoUrl();
        });
    }

    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(fn ($segment) => mb_substr($segment, 0, 1))->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=FFFFFF&background=0080FF';
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
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

    public function scopeSearchUser($query, $search, $withTahun = false)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $withTahun) {

            if ($withTahun == false) {
                $q->where('email', 'like', $searchTerm);

                if (is_numeric($search)) {
                    $q->orWhere('users.id', $search);
                }

                $roleConfigs = [
                    'admin' => ['name', 'nip', 'nitk', 'nik', 'status', 'id'],
                    'dosen' => ['name', 'nip', 'nidn', 'nidk', 'nik', 'status', 'id'],
                    'mahasiswa' => ['name', 'nim', 'nik', 'angkatan', 'status', 'id'],
                ];

                foreach ($roleConfigs as $role => $fields) {
                    $q->orWhereHas($role, function ($r) use ($searchTerm, $fields) {
                        $r->where(function ($sub) use ($searchTerm, $fields) {
                            foreach ($fields as $field) {
                                $sub->orWhere($field, 'like', $searchTerm);
                            }
                        });
                    });
                }

            } else {
                $q->whereHas('mahasiswa', fn ($q) => $q->where('angkatan', 'like', [$searchTerm]));
            }
        });
    }

    // public function scopeSearchUser($query, $search, $withTahun = false)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $withTahun) {

    //         if ($withTahun == false) {
    //             $q->where('email', 'like', $searchTerm);

    //             if (is_numeric($search)) {
    //                 $q->orWhere('users.id', $search);
    //             }

    //             $q->orWhere(function ($dq) use ($searchLower, $searchTerm) {
    //                 $dq->whereRaw("DATE_FORMAT(users.created_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("DATE_FORMAT(users.updated_at, '%d/%m/%Y') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("DATE_FORMAT(users.updated_at, '%Y-%m-%d') LIKE ?", [$searchTerm])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a, %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W, %d %M %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%a %d %b %Y')) LIKE ?", ['%'.$searchLower.'%'])
    //                     ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '%W %d %M %Y')) LIKE ?", ['%'.$searchLower.'%']);
    //             });

    //             $roleConfigs = [
    //                 'admin' => ['name', 'nip', 'nitk', 'nik', 'status', 'id'],
    //                 'dosen' => ['name', 'nip', 'nidn', 'nidk', 'nik', 'status', 'id'],
    //                 'mahasiswa' => ['name', 'nim', 'nik', 'angkatan', 'status', 'id'],
    //             ];

    //             foreach ($roleConfigs as $role => $fields) {
    //                 $q->orWhereHas($role, function ($r) use ($searchTerm, $fields) {
    //                     $r->where(function ($sub) use ($searchTerm, $fields) {
    //                         foreach ($fields as $field) {
    //                             $sub->orWhere($field, 'like', $searchTerm);
    //                         }
    //                     });
    //                 });

    //                 $q->orWhereHas("$role.pr_rel", function ($p) use ($searchTerm) {
    //                     $p->where(function ($pGroup) use ($searchTerm) {
    //                         $pGroup->where('nama_pr', 'like', $searchTerm)
    //                             ->orWhere('kode_pr', 'like', $searchTerm);
    //                         $pGroup->orWhereRaw("CONCAT(strata, ' ', nama_pr) LIKE ?", [$searchTerm]);
    //                         $aliasStrataSql = "
    //                         CASE
    //                             WHEN LOWER(strata) LIKE '%sarjana%' THEN 'S1'
    //                             WHEN LOWER(strata) LIKE '%magister%' THEN 'S2'
    //                             WHEN LOWER(strata) LIKE '%doktor%' THEN 'S3'
    //                             ELSE strata
    //                         END
    //                     ";
    //                         $pGroup->orWhereRaw("CONCAT($aliasStrataSql, ' ', nama_pr) LIKE ?", [$searchTerm]);
    //                     })
    //                         ->orWhereHas('dp_rel', function ($j) use ($searchTerm) {
    //                             $j->where(function ($jGroup) use ($searchTerm) {
    //                                 $jGroup->where('nama_dp', 'like', $searchTerm)
    //                                     ->orWhereRaw("CONCAT('Departemen ', nama_dp) LIKE ?", [$searchTerm])
    //                                     ->orWhere('kode_dp', 'like', $searchTerm);
    //                             })
    //                                 ->orWhereHas('fk_rel', function ($f) use ($searchTerm) {
    //                                     $f->where(function ($fGroup) use ($searchTerm) {
    //                                         $fGroup->where('nama_fk', 'like', $searchTerm)
    //                                             ->orWhereRaw("CONCAT('Fakultas ', nama_fk) LIKE ?", [$searchTerm])
    //                                             ->orWhere('kode_fk', 'like', $searchTerm);
    //                                     });
    //                                 });
    //                         });
    //                 });
    //                 if (str_contains($searchLower, $role)) {
    //                     $q->orWhereHas($role);
    //                 }
    //             }

    //         } else {
    //             $q->whereHas('mahasiswa', fn ($q) => $q->where('angkatan', 'like', [$searchTerm]));
    //         }
    //     });
    // }
}
