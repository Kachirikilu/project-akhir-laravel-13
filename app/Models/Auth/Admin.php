<?php

namespace App\Models\Auth;

use App\Traits\ValidatesGlobalIdentity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\ProgramStudi\Prodi;

class Admin extends Model
{
    use HasFactory;
    
    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'pr_id',
        'kode_wilayah',
        'nip',
        'nitk',
        'nik',
        'name',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'no_hp',
        'pangkat',
        'golongan_awal',
        'golongan_akhir',
        'tmt_cp_blu',
        'tmt_blu',
        'status',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'pr_id')->withTrashed();
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

    protected static function booted()
    {
        static::saving(function ($admin) {
            if ($admin->nip && \DB::table('dosens')->where('nip', $admin->nip)->exists()) {
                throw new \Exception("NIP ini sudah terdaftar sebagai Dosen!");
            }
        });
    }
}