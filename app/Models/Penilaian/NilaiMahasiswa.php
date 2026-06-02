<?php

namespace App\Models\Penilaian;

use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'kj_id',
        'nilai',
        'nilai_array',
        'bobot_array',
        'is_locked',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',

        // otomatis jadi array PHP
        'nilai_array' => 'array',
        'bobot_array' => 'array',

        'is_locked' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function mahasiswa_rel(): BelongsTo
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'mahasiswa_id'
        );
    }

    public function jadwal_rel(): BelongsTo
    {
        return $this->belongsTo(
            KelasJadwal::class,
            'kj_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function hitungNilaiAkhir(): float
    {
        $nilai = $this->nilai_array ?? [];
        $bobot = $this->bobot_array ?? [];

        $total = 0;

        foreach ($nilai as $index => $n) {
            $n = (float) ($n ?? 0);
            $b = (float) ($bobot[$index] ?? 0);

            $total += $n * $b;
        }

        return round($total, 2);
    }
}
