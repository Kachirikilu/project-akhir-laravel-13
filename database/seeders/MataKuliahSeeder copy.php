<?php

namespace Database\Seeders;

use App\Models\Akademik\MataKuliah;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $allProdiIds = Prodi::pluck('id')->toArray();

            $counters = [];

            $generateDigitSemester = function ($semester, $isTA = false) {
                $group = ceil($semester / 2);
                if ($isTA) {
                    return $group.'0';
                }
                $parity = $semester % 2 === 0 ? 2 : 1;

                return $group.$parity;
            };

            // Fungsi helper untuk mendapatkan & menaikkan counter
            $getNextDigitMk = function ($prodiId, $semester) use (&$counters) {
                if (! isset($counters[$prodiId][$semester])) {
                    $counters[$prodiId][$semester] = 1;
                }

                return $counters[$prodiId][$semester]++;
            };

            // $elektroData = [ /* ... isi 131 data Anda ... */ ];

            // foreach ($elektroData as $data) {
            //     $prodiId = 1;
            //     $digit = $getNextDigitMk($prodiId, $data['smt']);

            //     $mk = MataKuliah::create([
            //         'level_mk'       => $data['level'],
            //         'nama_mk'        => $data['nama'],
            //         'digit_semester' => $generateDigitSemester($data['smt'], $data['is_ta']),
            //         'digit_mk'       => str_pad($digit, 2, '0', STR_PAD_LEFT),
            //         'semester'       => $data['smt'],
            //         'sks_kuliah'     => $data['sks'],
            //         'tipe_sks'       => 1,
            //         'is_wajib'       => true,
            //         'deskripsi'      => 'Deskripsi ' . $data['nama'],
            //         'bahan_kajian'   => 'Bahan kajian ' . $data['nama'],
            //     ]);

            //     $this->attachProdis($mk, $data['level'], Prodi::find($prodiId), $allProdiIds);
            // }

            // 2. DATA DUMMY
            $prodis = Prodi::where('id', '!=', 1)->get();
            foreach ($prodis as $prodi) {
                for ($i = 1; $i <= 5; $i++) {
                    $level = rand(1, 4);
                    $smt = rand(1, 8);
                    $digit = $getNextDigitMk($prodi->id, $smt);

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
            $fakultasIdAsli = $prodi->dp_rel->fk_id;
            $fakultas = Fakultas::find($fakultasIdAsli);

            if ($fakultas) {
                $prodiIds = [];
                foreach ($fakultas->departemens as $dept) {
                    foreach ($dept->prodis as $p) {
                        if ($p->dp_rel->fk_id != $fakultasIdAsli) {
                            throw new \Exception("DATA KORUP! Prodi ID: {$p->id} ada di Departemen yang FK_ID-nya adalah {$p->dp_rel->fk_id}, tapi masuk ke Fakultas ID: {$fakultasIdAsli}");
                        }

                        $prodiIds[] = $p->id;
                    }
                }
                $mk->prodis()->attach(array_unique($prodiIds));
            }
        } elseif ($level == 2) {
            $departemen = Departemen::find($prodi->dp_id);

            if ($departemen) {
                $prodiIds = $departemen->prodis->pluck('id')->toArray();
                $mk->prodis()->attach($prodiIds);
            }
        } else {
            $mk->prodis()->attach($prodi->id);
        }
    }
}
