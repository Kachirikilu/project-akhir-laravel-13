<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPL;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapCPLMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpl_mahasiswa';

    protected $fillable = [
        'cpl_id',
        'mahasiswa_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function cpl_rel(): BelongsTo
    {
        return $this->belongsTo(
            CPL::class,
            'cpl_id'
        );
    }
    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }
}
