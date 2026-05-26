<?php

namespace App\Models\Kelas;

use App\Models\Auth\Dosen;
use App\Models\Kelas\KelasSesi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasSesiOverride extends Model
{
    protected $table = 'kelas_sesi_overrides';
    protected $guarded = ['id'];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(KelasSesi::class, 'sesi_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}