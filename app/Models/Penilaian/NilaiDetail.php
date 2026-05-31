<?php

namespace App\Models\Penilaian;

use App\Models\Kelas\KelasSesi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiDetail extends Model
{
    use SoftDeletes;

    protected $table = 'nilai_detail';

    protected $fillable = [
        'nilai_id',
        'sesi_id',
        'nilai',
        'bobot',
        'nilai_bobot',
        'is_generated',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'bobot' => 'decimal:2',
        'nilai_bobot' => 'decimal:2',
        'is_generated' => 'boolean',
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

    public function sesi_rel(): BelongsTo
    {
        return $this->belongsTo(
            KelasSesi::class,
            'sesi_id'
        );
    }
}