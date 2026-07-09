<?php

namespace App\Models\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use App\Concerns\HasTeams;
use Carbon\Carbon;
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
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['email', 'password', 'current_team_id'])] // Menggunakan PHP Attribute ala Laravel 13 (Name sengaja dilepas/di-comment seperti versi lama)
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [
    //     'profile_photo_url',
    //     'name',
    //     'role',
    //     'identity1',
    //     'identity2',
    //     'identity3',
    //     'nik',
    //     'status',
    //     'status_full',
    //     'kode_wilayah',
    //     'wilayah',
    // ];

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

    public function wallpapers()
    {
        return $this->hasMany(Wallpaper::class);
        }

    public function pendidikans(): HasMany
    {
        return $this->hasMany(Pendidikan::class);
    }

    private function getProfile()
    {
        return data_get($this, 'admin')
            ?? data_get($this, 'dosen')
            ?? data_get($this, 'mahasiswa');
    }

    protected function name(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'name');
        });
    }

    // public function role(): Attribute
    // {
    //     return Attribute::get(fn () => match (true) {
    //         $this->admin()->exists() => 'Admin',
    //         $this->dosen()->exists() => 'Dosen',
    //         $this->mahasiswa()->exists() => 'Mahasiswa',
    //         default => 'User',
    //     });
    // }

    public function role(): Attribute
    {
        return Attribute::get(function () {
            if ($this->relationLoaded('admin') && $this->admin !== null) {
                return 'Admin';
            }
            if ($this->relationLoaded('dosen') && $this->dosen !== null) {
                return 'Dosen';
            }
            if ($this->relationLoaded('mahasiswa') && $this->mahasiswa !== null) {
                return 'Mahasiswa';
            }

            if (array_key_exists('admin', $this->attributes) || isset($this->admin)) {
                if (data_get($this, 'admin') !== null) {
                    return 'Admin';
                }
            }
            if (array_key_exists('dosen', $this->attributes) || isset($this->dosen)) {
                if (data_get($this, 'dosen') !== null) {
                    return 'Dosen';
                }
            }
            if (array_key_exists('mahasiswa', $this->attributes) || isset($this->mahasiswa)) {
                if (data_get($this, 'mahasiswa') !== null) {
                    return 'Mahasiswa';
                }
            }

            $profile = $this->getProfile();
            if ($profile) {
                if (data_get($profile, 'nim')) {
                    return 'Mahasiswa';
                }
                if (data_get($profile, 'nidn') || data_get($profile, 'nidk')) {
                    return 'Dosen';
                }
                if (data_get($profile, 'nitk') || data_get($profile, 'nip')) {
                    return 'Admin';
                }
            }

            return 'User';
        });
    }

    protected function roleId(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'id');
        });
    }

    protected function nik(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'nik');
        });
    }

    protected function identity1(): Attribute
    {
        return Attribute::get(function () {
            $value = data_get($this->admin, 'nip')
                ?? data_get($this->dosen, 'nip')
                ?? data_get($this->mahasiswa, 'nim');

            return empty($value) ? null : $value;
        });
    }

    protected function labelId1(): Attribute
    {
        return Attribute::get(function () {
            if (data_get($this, 'admin') || data_get($this, 'dosen')) {
                return 'NIP';
            } elseif (data_get($this, 'mahasiswa')) {
                return 'NIM';
            }

            return null;
        });
    }

    protected function identity2(): Attribute
    {
        return Attribute::get(function () {
            $value = data_get($this->admin, 'nitk')
                ?? data_get($this->dosen, 'nidn');

            return empty($value) ? null : $value;
        });
    }

    protected function labelId2(): Attribute
    {
        return Attribute::get(function () {
            if (data_get($this, 'admin')) {
                return 'NITK';
            } elseif (data_get($this, 'dosen')) {
                return 'NIDN';
            }

            return null;
        });
    }

    protected function identity3(): Attribute
    {
        return Attribute::get(function () {
            $value = data_get($this->dosen, 'nidk');

            return $value ?: null;
        });
    }

    protected function kodeWilayah(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'kode_wilayah') ?? null;
        });
    }

    protected function wilayah(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'wilayah') ?? null;
        });
    }

    protected function tmtLahir(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'tempat_lahir');
        });
    }

    protected function tanggalLahir(): Attribute
    {
        return Attribute::make(
            get: function () {
                $value = data_get($this->getProfile(), 'tanggal_lahir');
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
                $tanggal = data_get($this, 'tanggal_lahir');
                if (empty($tanggal)) {
                    return null;
                }

                return Carbon::parse($tanggal)->translatedFormat('j F Y');
            }
        );
    }

    protected function gender(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'jenis_kelamin');
        });
    }

    protected function agama(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'agama');
        });
    }

    protected function noHp(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'no_hp');
        });
    }

    protected function noWa(): Attribute
    {
        return Attribute::get(function () {
            $phone = $this->no_hp;

            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }

            return $phone ?? null;
        });
    }

    protected function noWaFull(): Attribute
    {
        return Attribute::get(function () {
            $phone = preg_replace('/[^0-9]/', '', $this->no_hp);
            
            if (empty($phone)) return null;

            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }
            $countryCode = '62';
            $body = substr($phone, 2); 
            
            $firstPart = substr($body, 0, 3);
            $secondPart = substr($body, 3, 4);
            $thirdPart = substr($body, 7);
            $result = '+' . $countryCode . '-' . $firstPart;
            
            if ($secondPart !== false && $secondPart !== '') {
                $result .= '-' . $secondPart;
            }
            
            if ($thirdPart !== false && $thirdPart !== '') {
                $result .= '-' . $thirdPart;
            }

            return $result;
        });
    }

    protected function waAktif(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'is_wa_active');
        });
    }

    protected function noHpBack(): Attribute
    {
        return Attribute::get(function () {
            $value = data_get($this->getProfile(), 'no_hp');

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
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'status');
        });
    }

    protected function statusFull(): Attribute
    {
        return Attribute::get(fn () => 'Status: '.$this->status);
    }

    protected function prId(): Attribute
    {
        return Attribute::get(function () {
            $profile = $this->getProfile();

            return data_get($profile, 'pr_id', 0);
        });
    }

    protected function prodi(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.prodi');
        });
    }

    protected function prodiPr(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.prodi_pr');
        });
    }

    protected function prodiStrata(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.prodi_strata');
        });
    }

    protected function kodePr(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.kode');
        });
    }

    protected function dpId(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_id');
        });
    }

    protected function kodeDp(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.kode');
        });
    }

    protected function departemen(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.departemen');
        });
    }

    protected function departemenDp(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.departemen_dp');
        });
    }

    protected function fkId(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.fk_id');
        });
    }

    protected function kodeFk(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.fk_rel.kode_fk');
        });
    }

    protected function fakultas(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.fk_rel.fakultas');
        });
    }

    protected function fakultasFk(): Attribute
    {
        return Attribute::get(function () {
            return data_get($this->getProfile(), 'pr_rel.dp_rel.fk_rel.fakultas_fk');
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
                $q->whereHas('mahasiswa', function ($mhs) use ($searchTerm) {
                    $mhs->where('angkatan', 'like', $searchTerm);
                });
            }
        });
    }

    public function scopeSearchUserSmart($query, $search, $withTahun = false)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm, $searchLower, $withTahun) {

            if ($withTahun == false) {
                $q->where('email', 'like', $searchTerm);

                if (is_numeric($search)) {
                    $q->orWhere('users.id', $search);
                }

                $q->orWhere(function ($dq) use ($searchTerm, $searchLower) {
                    $numericFormats = ['%d/%m/%Y', '%Y-%m-%d'];
                    $textFormats = ['%a, %d %b %Y', '%W, %d %M %Y', '%a %d %b %Y', '%W %d %M %Y'];

                    foreach ($numericFormats as $format) {
                        $dq->orWhereRaw("DATE_FORMAT(users.created_at, '$format') LIKE ?", [$searchTerm])
                        ->orWhereRaw("DATE_FORMAT(users.updated_at, '$format') LIKE ?", [$searchTerm]);
                    }

                    foreach ($textFormats as $format) {
                        $dq->orWhereRaw("LOWER(DATE_FORMAT(users.created_at, '$format')) LIKE ?", [$searchLower])
                        ->orWhereRaw("LOWER(DATE_FORMAT(users.updated_at, '$format')) LIKE ?", [$searchLower]);
                    }
                });

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

                    $q->orWhereHas('admin.pr_rel', function ($pr) use ($search) {
                        $pr->searchProdiSmart($search);
                    });

                    $q->orWhereHas('dosen.pr_rel', function ($pr) use ($search) {
                        $pr->searchProdiSmart($search);
                    });

                    $q->orWhereHas('mahasiswa.pr_rel', function ($pr) use ($search) {
                        $pr->searchProdiSmart($search);
                    });

                    if (str_contains($searchLower, $role)) {
                        $q->orWhereHas($role);
                    }
                }

            } else {
                $q->whereHas('mahasiswa', fn ($q) => $q->where('angkatan', 'like', [$searchTerm]));
            }
        });
    }
}
