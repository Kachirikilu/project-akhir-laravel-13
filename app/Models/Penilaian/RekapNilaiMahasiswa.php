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
        'nilai_ipk',
        // 'mutu',
        'count_rps',
        'total_sks',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_ipk' => 'decimal:2',
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

    // protected function nilaiIpk(): Attribute
    // {
    //     return Attribute::get(function () {
    //         $nilai = (float) ($this->nilai ?? 0);
    //         $indeksRasio = ($nilai / 100) * 4;
    //         return number_format($indeksRasio, 2);
    //     });
    // }

    protected function nilaiMutu(): Attribute
    {
        return Attribute::get(function () {

            $nilai = $this->nilai;

            return match (true) {
                $nilai >= 85 => 'A', // 4.00
                $nilai >= 80 => 'A-', // 3.70
                $nilai >= 75 => 'B+', // seterusnya
                $nilai >= 70 => 'B',
                $nilai >= 65 => 'B-',
                $nilai >= 60 => 'C+',
                $nilai >= 55 => 'C',
                $nilai >= 40 => 'D',
                default => 'E',
            };
        });
    }
}
