<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\SubCPMK;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapSubCPMKMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_scpmk_mahasiswa';

    protected $fillable = [
        'scpmk_id',
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

    public function scpmk_rel(): BelongsTo
    {
        return $this->belongsTo(
            SubCPMK::class,
            'scpmk_id'
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
