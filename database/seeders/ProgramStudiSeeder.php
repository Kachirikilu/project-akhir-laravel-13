<?php

namespace Database\Seeders;

use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            
            // --- DATA STRUKTUR ---
            $data = [
                [
                    'nama' => 'Teknik',
                    'kode' => 'TEK',
                    'departemen' => [
                        ['nama' => 'Teknik Elektro', 'kode' => 'TKE'],
                        ['nama' => 'Teknik Kimia', 'kode' => 'TKK'],
                        ['nama' => 'Teknik Sipil', 'kode' => 'TKS'],
                        ['nama' => 'Teknik Arsitektur', 'kode' => 'TAA'],
                    ]
                ],
                [
                    'nama' => 'Ilmu Komputer',
                    'kode' => 'FIK',
                    'departemen' => [
                        ['nama' => 'Sistem Informasi', 'kode' => 'FSI'],
                        ['nama' => 'Teknik Informatika', 'kode' => 'FTI'],
                        ['nama' => 'Ilmu Komputer', 'kode' => 'FMK'],
                    ]
                ],
            ];

            $strataOptions = ['Sarjana', 'Magister', 'Doktor'];

            foreach ($data as $f) {
                // 1. Create Fakultas
                $fakultas = Fakultas::create([
                    'nama_fk' => $f['nama'],
                    'kode_fk' => $f['kode'],
                ]);

                foreach ($f['departemen'] as $j) {
                    // 2. Create Departemen
                    $departemen = Departemen::create([
                        'fk_id' => $fakultas->id,
                        'nama_dp' => $j['nama'],
                        'kode_dp' => $j['kode'],
                    ]);

                    foreach ($strataOptions as $strata) {
                        // 3. Create Prodi (S1, S2, S3)
                        Prodi::create([
                            'dp_id' => $departemen->id,
                            'nama_pr' => $j['nama'], // Nama prodi sama dengan nama departemen sesuai request
                            'strata' => $strata,
                        ]);
                    }
                }
            }
        });
    }
}