<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapProdi extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_prodi';

    protected $fillable = ['pr_id', 'nilai'];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'pr_id');
    }
}
