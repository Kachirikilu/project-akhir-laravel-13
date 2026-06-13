<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapRPSProdi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_rps_prodi';

    protected $fillable = [
        'rps_id',
        'pr_id',
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

    public function rps_rel(): BelongsTo
    {
        return $this->belongsTo(
            RPS::class,
            'rps_id'
        );
    }
    public function pr_rel(): BelongsTo
    {
        return $this->belongsTo(
            Prodi::class,
            'pr_id'
        );
    }
}
