<?php

namespace Database\Seeders;

use App\Models\Akademik\MataKuliah;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $allProdiIds = Prodi::pluck('id')->toArray();
            $digitCounter = 1;

            $generateDigitSemester = function ($semester, $isTA = false) {
                $group = ceil($semester / 2);
                if ($isTA) {
                    return $group.'0';
                }
                $parity = $semester % 2 === 0 ? 2 : 1;

                return $group.$parity;
            };

            // 1. DATA REAL (TEKNIK ELEKTRO - ID 1)
            // Masukkan 131 data Anda di sini
            $elektroData = [
                ['nama' => 'FISIKA TEKNIK', 'smt' => 1, 'sks' => 3, 'level' => 1, 'is_ta' => false],
                // ... isi sisa data
            ];

            foreach ($elektroData as $data) {
                $mk = MataKuliah::create([
                    'level_mk' => $data['level'],
                    'nama_mk' => $data['nama'],
                    'digit_semester' => $generateDigitSemester($data['smt'], $data['is_ta']),
                    'digit_mk' => str_pad($digitCounter++, 2, '0', STR_PAD_LEFT),
                    'semester' => $data['smt'],
                    'sks_kuliah' => $data['sks'],
                    'tipe_sks' => 1,
                    'is_wajib' => true,
                    'deskripsi' => 'Deskripsi '.$data['nama'],
                    'bahan_kajian' => 'Bahan kajian '.$data['nama'],
                ]);

                // Relasi sesuai level dengan logika FK/DP ID Anda
                $this->attachProdis($mk, $data['level'], Prodi::find(1), $allProdiIds);
            }

            // 2. DATA DUMMY
            $prodis = Prodi::where('id', '!=', 1)->get();
            foreach ($prodis as $prodi) {
                for ($i = 1; $i <= 5; $i++) {
                    $level = rand(1, 4);
                    $smt = rand(1, 8);

                    $digit = ($digitCounter++) % 100;
                    if ($digit === 0) {
                        $digit = 1;
                    }

                    $mk = MataKuliah::create([
                        'level_mk' => $level,
                        'nama_mk' => 'MK '.($level == 4 ? 'Univ' : ($level == 3 ? 'Fak' : 'Dept'))." $i",
                        'digit_semester' => $generateDigitSemester($smt, false),
                        'digit_mk' => str_pad($digit, 2, '0', STR_PAD_LEFT),
                        'semester' => $smt,
                        'sks_kuliah' => rand(2, 4),
                        'tipe_sks' => rand(1, 4),
                        'is_wajib' => rand(0, 1),
                        'deskripsi' => 'Deskripsi...',
                        'bahan_kajian' => 'Bahan...',
                    ]);

                    $this->attachProdis($mk, $level, $prodi, $allProdiIds);
                }
            }
        });
    }

    private function attachProdis($mk, $level, $prodi, $allProdiIds)
    {
        if ($level == 4) {
            $mk->prodis()->attach($allProdiIds);
        } elseif ($level == 3) {
            $targetFakultasId = $prodi->dp_rel->fk_id;

            $mk->prodis()->attach(
                Prodi::whereHas('dp_rel', function ($query) use ($targetFakultasId) {
                    $query->where('fk_id', $targetFakultasId);
                })->pluck('id')
            );
        } elseif ($level == 2) {
            $mk->prodis()->attach(
                Prodi::where('dp_id', $prodi->dp_id)->pluck('id')
            );
        } else {
            $mk->prodis()->attach($prodi->id);
        }
    }
}
