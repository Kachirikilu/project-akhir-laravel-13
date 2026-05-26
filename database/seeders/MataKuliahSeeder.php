<?php

namespace Database\Seeders;

use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\MataKuliah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $allProdiIds = Prodi::pluck('id')->toArray();
            $digitCounter = 1;

            // =========================
            // HELPER DIGIT SEMESTER
            // =========================
            $generateDigitSemester = function ($semester, $isTA = false) {
                $group = ceil($semester / 2);

                if ($isTA) {
                    return $group . '0';
                }

                $parity = $semester % 2 === 0 ? 2 : 1;

                return $group . $parity;
            };

            // =========================
            // 1. UNIVERSITAS (8 MK)
            // =========================
            for ($i = 1; $i <= 8; $i++) {

                $semester = rand(1, 2);

                $mk = MataKuliah::create([
                    'level_mk' => 4,
                    'nama_mk' => "MK Universitas $i",
                    'kode_mk' => null,
                    'digit_semester' => $generateDigitSemester($semester),
                    'digit_mk' => str_pad($digitCounter++, 2, '0', STR_PAD_LEFT),
                    'semester' => $semester,
                    'sks_kuliah' => rand(2, 3),
                    'tipe_sks' => 1,
                    'is_wajib' => true,
                    'deskripsi' => "Deskripsi MK Universitas $i",
                    'bahan_kajian' => "Bahan kajian $i",
                ]);

                $mk->prodis()->attach($allProdiIds);
            }

            // =========================
            // 2. FAKULTAS (16 MK)
            // =========================
            $prodiTeknikIds = Prodi::pluck('id');

            for ($i = 1; $i <= 16; $i++) {

                $semester = rand(2, 4);

                $mk = MataKuliah::create([
                    'level_mk' => 3,
                    'nama_mk' => "MK Fakultas $i",
                    'digit_semester' => $generateDigitSemester($semester),
                    'digit_mk' => str_pad($digitCounter++, 2, '0', STR_PAD_LEFT),
                    'semester' => $semester,
                    'sks_kuliah' => rand(2, 3),
                    'tipe_sks' => rand(1, 2),
                    'is_wajib' => true,
                    'deskripsi' => "Deskripsi MK Fakultas $i",
                    'bahan_kajian' => "Bahan kajian $i",
                ]);

                $mk->prodis()->attach($prodiTeknikIds);
            }

            // =========================
            // 3. JURUSAN (20 MK)
            // =========================
            $departemen = Departemen::first();

            if ($departemen) {

                for ($i = 1; $i <= 20; $i++) {

                    $semester = rand(3, 6);

                    $mk = MataKuliah::create([
                        'level_mk' => 2,
                        'nama_mk' => "MK Departemen $i",
                        'digit_semester' => $generateDigitSemester($semester),
                        'digit_mk' => str_pad($digitCounter++, 2, '0', STR_PAD_LEFT),
                        'semester' => $semester,
                        'sks_kuliah' => rand(2, 4),
                        'tipe_sks' => rand(1, 2),
                        'is_wajib' => rand(0, 1),
                        'deskripsi' => "Deskripsi MK Departemen $i",
                        'bahan_kajian' => "Bahan kajian $i",
                    ]);

                    $mk->prodis()->attach($departemen->prodis->pluck('id'));
                }
            }

            // =========================
            // 4. PRODI (20 MK)
            // =========================
            $prodi = Prodi::first();

            if ($prodi) {

                for ($i = 1; $i <= 20; $i++) {

                    $semester = rand(5, 8);

                    $isTA = $i % 5 === 0; // tiap 5 MK = TA/KP

                    $mk = MataKuliah::create([
                        'level_mk' => 1,
                        'nama_mk' => $isTA ? "Tugas Akhir / KP $i" : "MK Prodi $i",
                        'digit_semester' => $generateDigitSemester($semester, $isTA),
                        'digit_mk' => str_pad($digitCounter++, 2, '0', STR_PAD_LEFT),
                        'semester' => $semester,
                        'sks_kuliah' => rand(2, 4),
                        'tipe_sks' => rand(1, 4),
                        'is_wajib' => rand(0, 1),
                        'deskripsi' => "Deskripsi MK Prodi $i",
                        'bahan_kajian' => "Bahan kajian $i",
                    ]);

                    $mk->prodis()->attach($prodi->id);
                }
            }
        });
    }
}