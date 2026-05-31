<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPL;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapCPLMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpl_mahasiswa';

    protected $fillable = [
        'nilai_id',
        'mahasiswa_id',
        'cpl_id',
        'persentase',
        'jumlah_pertemuan',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'jumlah_pertemuan' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function nilai_rel(): BelongsTo
    {
        return $this->belongsTo(
            NilaiMahasiswa::class,
            'nilai_id'
        );
    }

    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }

    public function cpl_rel(): BelongsTo
    {
        return $this->belongsTo(
            CPL::class,
            'cpl_id'
        );
    }
}