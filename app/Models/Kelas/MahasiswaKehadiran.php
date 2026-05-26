<?php

namespace App\Models\Kelas;

use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\KelasSesi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MahasiswaKehadiran extends Model
{
    protected $table = 'mahasiswa_kehadiran';
    protected $guarded = ['id'];

    protected $casts = [
        'waktu_presensi' => 'datetime',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(KelasSesi::class, 'sesi_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}