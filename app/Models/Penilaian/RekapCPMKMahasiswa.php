<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPMK;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapCPMKMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpmk_mahasiswa';

    protected $fillable = [
        'cpmk_id',
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

    public function cpmk_rel(): BelongsTo
    {
        return $this->belongsTo(
            CPMK::class,
            'cpmk_id'
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
