<?php

namespace Database\Seeders;

use App\Models\Akademik\CPL;
use App\Models\Kelas\KelasJadwal;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting NilaiSeeder...');

        $chunkSize = 25;

        $totalJadwal = KelasJadwal::count();
        $processedJadwal = 0;
        $processedMahasiswa = 0;

        $this->command->info(
            "Found {$totalJadwal} jadwal. Chunk size: {$chunkSize}"
        );

        KelasJadwal::with([
            'mahasiswas',
            'sesis.override',
            'kelas_rel.rps_rel.cpmks.scpmks',
            'kelas_rel.rps_rel.cpmks.cpls',
        ])
            ->chunk($chunkSize, function ($jadwals) use (
                &$processedJadwal,
                &$processedMahasiswa,
                $totalJadwal
            ) {

                $chunkStart = microtime(true);

                foreach ($jadwals as $jadwal) {

                    DB::transaction(function () use (
                        $jadwal,
                        &$processedMahasiswa
                    ) {

                        $sesis = $jadwal->sesis
                            ->sortBy('pertemuan_ke')
                            ->values();

                        foreach ($jadwal->mahasiswas as $mahasiswa) {

                            $processedMahasiswa++;

                            // =====================================
                            // PROFIL NILAI MAHASISWA (realistis)
                            // =====================================
                            $baseNilai = rand(55, 95);

                            $nilaiMahasiswa = NilaiMahasiswa::create([
                                'mahasiswa_id' => $mahasiswa->id,
                                'kj_id' => $jadwal->id,
                                'nilai' => null,
                                'is_locked' => rand(1, 100) <= 25,
                            ]);

                            $totalNilaiBobot = 0;
                            $totalBobot = 0;

                            $rekapCpl = [];

                            foreach ($sesis as $sesi) {

                                $scpmk = $sesi->scpmk;

                                // =====================================
                                // Ambil bobot sesi
                                // =====================================
                                $bobot = $scpmk?->bobot
                                    ?? $sesi->override?->bobot
                                    ?? rand(3, 10);

                                // =====================================
                                // Generate nilai realistis
                                // =====================================
                                $noise = rand(-15, 10);

                                $nilai = max(
                                    0,
                                    min(100, $baseNilai + $noise)
                                );

                                // UTS/UAS sedikit lebih sulit
                                $metode = strtolower(
                                    $scpmk?->metode
                                    ?? $sesi->override?->metode
                                    ?? ''
                                );

                                if (in_array($metode, [
                                    'uts',
                                    'uas',
                                    'evaluasi awal',
                                    'evaluasi akhir',
                                ])) {
                                    $nilai -= rand(3, 12);
                                }

                                $nilai = round($nilai, 2);

                                $nilaiBobot =
                                    ($nilai * $bobot) / 100;

                                $nilaiMahasiswa->details()->create([
                                    'sesi_id' => $sesi->id,
                                    'nilai' => $nilai,
                                    'bobot' => $bobot,
                                    'nilai_bobot' => $nilaiBobot,
                                    'is_generated' => true,
                                ]);

                                $totalNilaiBobot += $nilaiBobot;
                                $totalBobot += $bobot;

                                // =====================================
                                // Rekap CPL
                                // =====================================
                                if ($scpmk) {

                                    $cpls = $scpmk->cpmks
                                        ->flatMap(fn ($cpmk) => $cpmk->cpls)
                                        ->unique('id');

                                    foreach ($cpls as $cpl) {

                                        $rekapCpl[$cpl->id] ??= [
                                            'total' => 0,
                                            'jumlah' => 0,
                                        ];

                                        $rekapCpl[$cpl->id]['total'] += $nilai;
                                        $rekapCpl[$cpl->id]['jumlah']++;
                                    }
                                }
                            }

                            // =====================================
                            // Final nilai
                            // =====================================
                            $nilaiAkhir =
                                $totalBobot > 0
                                    ? round(
                                        ($totalNilaiBobot /
                                            $totalBobot)
                                        * 100,
                                        2
                                    )
                                    : 0;

                            $nilaiMahasiswa->update([
                                'nilai' => $nilaiAkhir,
                            ]);

                            // =====================================
                            // Simpan rekap CPL
                            // =====================================
                            foreach ($rekapCpl as $cplId => $data) {

                                $persentase =
                                    $data['jumlah'] > 0
                                        ? round(
                                            $data['total']
                                            / $data['jumlah'],
                                            2
                                        )
                                        : 0;

                                DB::table(
                                    'rekap_cpl_mahasiswa'
                                )->insert([
                                    'nilai_id' => $nilaiMahasiswa->id,
                                    'mahasiswa_id' => $mahasiswa->id,
                                    'cpl_id' => $cplId,
                                    'persentase' => $persentase,
                                    'jumlah_pertemuan' => $data['jumlah'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    });

                    $processedJadwal++;
                }

                $duration = round(
                    microtime(true) - $chunkStart,
                    2
                );

                $percent = round(
                    ($processedJadwal / $totalJadwal) * 100,
                    2
                );

                $this->command->info(
                    "[{$processedJadwal}/{$totalJadwal}] ".
                    "{$percent}% | ".
                    "Mahasiswa: {$processedMahasiswa} | ".
                    "Chunk selesai {$duration}s"
                );
            });

        $this->command->info(
            'NilaiSeeder finished successfully!'
        );
    }
}
