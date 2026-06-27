<?php

namespace Database\Seeders;

use App\Models\Akademik\TimDosen;
use App\Models\Akademik\MataKuliah;
use App\Models\ProgramStudi\Prodi;
use App\Models\Auth\Dosen;
use Illuminate\Database\Seeder;

class TimDosenSeeder extends Seeder
{
    public function run(): void
    {
        $namaMks = MataKuliah::pluck('nama_mk')->toArray();

        Prodi::with('dosens')->get()->each(function ($prodi) use ($namaMks) {
            $dosens = $prodi->dosens;

            if ($dosens->isEmpty()) return;
            
            $jumlahTim = rand(2, 4);

            for ($i = 1; $i <= $jumlahTim; $i++) {
                // Ambil anggota secara acak untuk menentukan ketua
                $anggota = $dosens->shuffle()->take(min(rand(2, 5), $dosens->count()))->values();
                $ketua = $anggota->first();
                
                $namaProdi = $prodi->nama; 
                $mkRandom = count($namaMks) > 0 ? $namaMks[array_rand($namaMks)] : 'MK';
                $namaDepanKetua = $ketua ? explode(' ', $ketua->name)[0] : 'Ketua';
                $namaTim = "Tim {$namaDepanKetua} {$mkRandom} {$namaProdi}";

                $tim = TimDosen::create([
                    'kode_tim_dosen' => $this->generateUniqueKode(TimDosen::class, 'kode_tim_dosen'),
                    'pr_id'      => $prodi->id, 
                    'nama_tim'   => $namaTim,
                    'sort_order' => $i,
                ]);

                // 2. Attach anggota
                foreach ($anggota as $index => $dosen) {
                    $tim->dosens()->attach($dosen->id, [
                        'peran'        => $index == 0 ? 'Koordinator' : 'Pengajar',
                        'is_ketua'     => $index == 0,
                        'pertemuan_ke' => null,
                        'sort_order'   => $index + 1,
                    ]);
                }
            }
        });
    }
    
    private function generateKode($prefixMin = 3, $prefixMax = 4, $numMin = 2, $numMax = 6)
    {
        $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, rand($prefixMin, $prefixMax)));

        $min = pow(10, $numMin - 1);
        $max = pow(10, $numMax) - 1;

        $numbers = rand($min, $max);

        return $letters.$numbers;
    }

    private function generateUniqueKode($model, $column)
    {
        do {
            $kode = $this->generateKode();
        } while ($model::where($column, $kode)->exists());

        return $kode;
    }
}