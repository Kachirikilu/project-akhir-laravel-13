<?php

namespace App\Models\Auth;

use App\Models\Kelas\KelasJadwal;
use App\Models\Kelas\MahasiswaKehadiran;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Penilaian\RekapNilaiMahasiswa;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'user_id',
        'pr_id',
        'kode_wilayah',
        'name',
        'nim',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'no_hp',
        'is_wa_active',
        'wa_limit',
        'angkatan',
        'tanggal_yudisium',
        'tanggal_wisuda',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_yudisium' => 'date',
        'tanggal_wisuda' => 'date',
        'angkatan' => 'integer',
    ];

    // protected $appends = [
    //     'status_full',
    // ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id')->withTrashed();
    }

    public function jadwals(): BelongsToMany
    {
        return $this->belongsToMany(KelasJadwal::class, 'mahasiswa_kelas', 'mahasiswa_id', 'kj_id')
            ->withTimestamps();
    }

    public function kehadirans(): HasMany
    {
        return $this->hasMany(MahasiswaKehadiran::class, 'mahasiswa_id');
    }

    public function nilai_mahasiswas(): HasMany
    {
        return $this->hasMany(
            NilaiMahasiswa::class,
            'mahasiswa_id'
        );
    }

    public function rekap_nilai(): HasOne
    {
        return $this->hasOne(
            RekapNilaiMahasiswa::class,
            'mahasiswa_id',
            'id'
        )->withTrashed();
    }

    public function rekap_cpl()
    {
        return $this->hasMany(
            RekapCPLMahasiswa::class,
            'mahasiswa_id'
        );
    }

    protected function rekapMhs(): Attribute
    {
        return Attribute::get(fn () => number_format($this->rekap_nilai?->nilai ?? 0, 2, '.', ''));
    }

    protected function ipkMhs(): Attribute
    {
        return Attribute::get(fn () => number_format($this->rekap_nilai?->nilai_ipk ?? 0, 2, '.', ''));
    }

    protected function mutuMhs(): Attribute
    {
        return Attribute::get(fn () => $this->rekap_nilai?->nilai_mutu ?? 'E');
    }

    protected function countRps(): Attribute
    {
        return Attribute::get(fn () => $this->rekap_nilai?->count_rps ?? 0);
    }

    protected function totalSks(): Attribute
    {
        return Attribute::get(fn () => $this->rekap_nilai?->total_sks ?? 0);
    }

    protected function angkatanFull(): Attribute
    {
        return Attribute::get(fn () => 'Angkatan: '.$this->angkatan);
    }

    protected function statusFull(): Attribute
    {
        return Attribute::get(fn () => 'Status: '.$this->status);
    }

    protected function wilayah(): Attribute
    {
        return Attribute::get(function () {

            if ($this->kode_wilayah == 'IDL') {
                return 'Indralaya';
            } elseif ($this->kode_wilayah == 'PLG') {
                return 'Bukit';
            } else {
                return null;
            }
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

            return $phone;
        });
    }

    protected function waAktif(): Attribute
    {
        return Attribute::get(function () {
            return $this->is_wa_active;
        });
    }

    protected function noWaFull(): Attribute
    {
        return Attribute::get(function () {
            $phone = $this->no_hp;
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (empty($phone)) {
                return null;
            }
            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }

            $countryCode = '62';
            $body = substr($phone, 2);
            $firstThree = substr($body, 0, 3);
            $rest = substr($body, 3);
            if ($rest !== false && $rest !== '') {
                $chunks = str_split($rest, 4);

                return '+'.$countryCode.'-'.$firstThree.'-'.implode('-', $chunks);
            }

            return '+'.$countryCode.'-'.$firstThree;
        });
    }

    public function scopeSearchMahasiswa($query, $search)
    {
        if (empty(trim($search))) {
            return $query;
        }

        $search = trim($search);
        $searchLower = '%'.strtolower($search).'%';
        $searchTerm = '%'.$search.'%';

        return $query->where(function ($q) use ($search, $searchTerm) {
            $fields = ['name', 'nim', 'nik', 'status', 'angkatan', 'kode_wilayah'];
            foreach ($fields as $field) {
                $q->orWhere("mahasiswas.$field", 'like', $searchTerm);
            }

            if (is_numeric($search)) {
                $q->orWhere('mahasiswas.id', $search);
            }
            $q->orWhereHas('user', function ($u) use ($searchTerm) {
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

    // public function scopeSearchMahasiswa($query, $search)
    // {
    //     if (empty(trim($search))) {
    //         return $query;
    //     }

    //     $search = trim($search);
    //     $searchLower = '%'.strtolower($search).'%';
    //     $searchTerm = '%'.$search.'%';

    //     return $query->where(function ($q) use ($search, $searchTerm, $searchLower) {
    //         // 1. Pencarian Identitas Langsung di Tabel Dosens
    //         $fields = ['name', 'nim', 'status', 'angkatan'];
    //         foreach ($fields as $field) {
    //             $q->orWhere("mahasiswas.$field", 'like', $searchTerm);
    //         }

    //         if (is_numeric($search)) {
    //             $q->orWhere('mahasiswas.id', $search);
    //         }

    //         // 2. Pencarian ke Tabel User Terkait (Email & Timestamps)
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

    //         // 3. Pencarian Berdasarkan Lokasi (Prodi, Departemen, Fakultas)
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
