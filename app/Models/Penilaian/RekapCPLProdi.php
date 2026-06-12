<?php

namespace App\Models\Penilaian;

use App\Models\Akademik\CPL;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapCPLProdi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_cpl_prodi';

    protected $fillable = [
        'cpl_id',
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

    public function cpl_rel(): BelongsTo
    {
        return $this->belongsTo(
            CPL::class,
            'cpl_id'
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
