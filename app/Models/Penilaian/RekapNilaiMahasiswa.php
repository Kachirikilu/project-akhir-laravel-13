<?php

namespace App\Models\Penilaian;

use App\Models\Auth\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RekapNilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $table = 'rekap_nilai_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'nilai',
        // 'nilai_ipk',
        // 'huruf',
        'count_rps',
        'total_sks',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        // 'nilai_ipk' => 'decimal:2',
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

    protected function nilaiIpk(): Attribute
    {
        return Attribute::get(function () {
            $nilai = (float) ($this->nilai ?? 0);
            $indeksRasio = ($nilai / 100) * 4;
            return number_format($indeksRasio, 2);
        });
    }

    protected function nilaiHuruf(): Attribute
    {
        return Attribute::get(function () {

            $nilai = $this->nilai;

            return match (true) {
                $nilai >= config('rps.min_a', 86) => 'A', // 4.00
                $nilai >= config('rps.min_a_min', 80) => 'A-', // 3.70
                $nilai >= config('rps.min_b_plus', 75) => 'B+', // seterusnya
                $nilai >= config('rps.min_b', 70) => 'B',
                $nilai >= config('rps.min_b_min', 65) => 'B-',
                $nilai >= config('rps.min_c_plus', 60) => 'C+',
                $nilai >= config('rps.min_c', 56) => 'C',
                $nilai >= config('rps.min_d', 40) => 'D',
                default => 'E',
            };
        });
    }
}
