<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Model;

class LockNilai extends Model
{
    protected $table = 'lock_nilai';

    protected $fillable = [
        'pr_id',
        'ganjil_unlock',
        'genap_unlock',
    ];
}