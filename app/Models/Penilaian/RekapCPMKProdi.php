<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPMK;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapCPMKProdi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpmk_prodi';

    protected $fillable = [
        'cpmk_id',
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

    public function cpmk_rel(): BelongsTo
    {
        return $this->belongsTo(
            CPMK::class,
            'cpmk_id'
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
