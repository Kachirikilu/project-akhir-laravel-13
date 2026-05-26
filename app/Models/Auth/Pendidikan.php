<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikans';

    protected $fillable = [
        'user_id',
        'institusi',
        'negara',
        'tahun_lulus',
        'jenjang_pendidikan',
        'bidang_ilmu',
        'gelar',
        'is_pendidikan_blu',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOrderByJenjang($query, $direction = 'asc')
    {
        $levels = [
            'SMA' => 1, 'SMK' => 1, 'MAN' => 1,
            'D1' => 2,
            'D2' => 3,
            'D3' => 4,
            'D4' => 5, 'S1' => 5,
            'Profesi' => 6,
            'Spesialis' => 7,
            'S2' => 8,
            'S3' => 9,
        ];

        $cases = [];
        foreach ($levels as $jenjang => $rank) {
            $cases[] = "WHEN jenjang_pendidikan = '{$jenjang}' THEN {$rank}";
        }

        $orderSql = 'CASE ' . implode(' ', $cases) . ' ELSE 10 END';

        return $query->orderByRaw("{$orderSql} {$direction}");
    }
}
