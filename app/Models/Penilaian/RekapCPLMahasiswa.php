<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapCPLMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpl_mahasiswa';

    protected $fillable = [
        'cpl_id',
        'nilai',
        'persentase',
        'jumlah_pertemuan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'persentase' => 'decimal:2',
        'jumlah_pertemuan' => 'integer',
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
}
