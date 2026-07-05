<?php

namespace App\Models\ProgramStudi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapFakultas extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_fakultas';

    protected $fillable = ['fk_id', 'nilai'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fk_id');
    }
}
