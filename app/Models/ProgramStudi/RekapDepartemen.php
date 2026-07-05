<?php

namespace App\Models\ProgramStudi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RekapDepartemen extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_departemen';

    protected $fillable = ['dp_id', 'nilai'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'dp_id');
    }
}
