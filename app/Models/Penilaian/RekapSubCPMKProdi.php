<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\SubCPMK;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapSubCPMKProdi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_scpmk_prodi';

    protected $fillable = [
        'scpmk_id',
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

    public function scpmk_rel(): BelongsTo
    {
        return $this->belongsTo(
            SubCPMK::class,
            'scpmk_id'
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
