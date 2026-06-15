<?php

namespace App\Models\Penilaian;

use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapNilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_nilai_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'nilai',
        'index',
        'huruf',
        'count_rps',
        'total_sks',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'index' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }
}
