<?php

namespace App\Models\Penilaian;

use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'kj_id',
        'nilai',
        'is_locked',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'is_locked' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function jadwal_rel(): BelongsTo
    {
        return $this->belongsTo(KelasJadwal::class, 'kj_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(NilaiDetail::class, 'nilai_id');
    }

    public function rekap_cpl(): HasMany
    {
        return $this->hasMany(RekapCPLMahasiswa::class, 'nilai_id');
    }
}