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
        // 'index',
        // 'mutu',
        'count_rps',
        'total_sks',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        // 'index' => 'decimal:2',
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

    protected function nilaiIndex(): Attribute
    {
        return Attribute::get(function () {

            return match ($this->nilai_mutu) {
                'A' => '4.00',
                'A-' => '3.70',
                'B+' => '3.30',
                'B' => '3.00',
                'B-' => '2.70',
                'C+' => '2.30',
                'C' => '2.00',
                'D' => '1.00',
                default => '0.00',
            };
        });
    }

    protected function nilaiMutu(): Attribute
    {
        return Attribute::get(function () {

            $nilai = $this->nilai;

            return match (true) {
                $nilai >= 85 => 'A',
                $nilai >= 80 => 'A-',
                $nilai >= 75 => 'B+',
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
