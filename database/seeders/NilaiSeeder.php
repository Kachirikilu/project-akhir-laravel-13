<?php

namespace Database\Seeders;

use App\Models\Kelas\KelasJadwal;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Penilaian\RekapCPLProdi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting NilaiSeeder...');

        $chunkSize = 512;

        $totalJadwal = KelasJadwal::count();
        $processedJadwal = 0;
        $processedMahasiswa = 0;

        $this->command->info(
            "Found {$totalJadwal} jadwal. Chunk size: {$chunkSize}"
        );

        KelasJadwal::with([
            'mahasiswas',
            'sesis.jadwal_rel.kelas_rel.rps_rel.cpmks.scpmks',
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

                        if ($sesis->isEmpty()) {
                            return;
                        }

                        // =====================================
                        // NORMALISASI BOBOT
                        // =====================================
                        $rawBobots = [];

                        foreach ($sesis as $sesi) {

                            $scpmk = $sesi->scpmk_atr;

                            $rawBobots[] =
                                $scpmk?->bobot
                                ?? $sesi->override?->bobot
                                ?? rand(3, 10);
                        }

                        $totalBobot = collect($rawBobots)->sum();

                        $normalizedBobots = collect($rawBobots)
                            ->map(fn ($bobot) => $totalBobot > 0
                                    ? round($bobot / $totalBobot, 6)
                                    : 0
                            )
                            ->values()
                            ->toArray();

                        foreach ($jadwal->mahasiswas as $mahasiswa) {

                            $processedMahasiswa++;

                            // =====================================
                            // PROFIL NILAI MAHASISWA
                            // =====================================
                            $baseNilai = rand(55, 95);

                            $nilaiArray = [];
                            $nilaiAkhir = 0;

                            $rekapCpl = [];

                            foreach ($sesis as $index => $sesi) {

                                $scpmk = $sesi->scpmk_atr;

                                // =====================================
                                // GENERATE NILAI REALISTIS
                                // =====================================
                                $noise = rand(-15, 10);

                                $nilai = max(
                                    0,
                                    min(100, $baseNilai + $noise)
                                );

                                $metode = strtolower(
                                    $scpmk?->metode
                                    ?? $sesi->override?->metode
                                    ?? ''
                                );

                                // UTS/UAS sedikit lebih sulit
                                if (in_array($metode, [
                                    'uts',
                                    'uas',
                                    'evaluasi awal',
                                    'evaluasi akhir',
                                ])) {
                                    $nilai -= rand(3, 12);
                                }

                                $nilai = round($nilai, 2);

                                $nilaiArray[] = $nilai;

                                // =====================================
                                // HITUNG NILAI AKHIR
                                // =====================================
                                $nilaiAkhir +=
                                    $nilai *
                                    ($normalizedBobots[$index] ?? 0);

                                // =====================================
                                // REKAP CPL
                                // =====================================
                                if (
                                    $scpmk &&
                                    isset($scpmk->cpmks) &&
                                    filled($scpmk->cpmks)
                                ) {

                                    $cpls = collect($scpmk->cpmks)
                                        ->flatMap(fn ($cpmk) => $cpmk->cpls ?? [])
                                        ->unique('id');

                                    foreach ($cpls as $cpl) {

                                        $rekapCpl[$cpl->id] ??= [
                                            'total' => 0,
                                            'jumlah' => 0,
                                        ];

                                        $rekapCpl[$cpl->id]['total']
                                            += $nilai;

                                        $rekapCpl[$cpl->id]['jumlah']++;
                                    }
                                }
                            }

                            // =====================================
                            // SIMPAN NILAI MAHASISWA
                            // =====================================
                            $rps = $jadwal->kelas_rel?->rps_rel;
                            NilaiMahasiswa::updateOrCreate(
                                [
                                    'mahasiswa_id' => $mahasiswa->id,
                                    'rps_id' => $rps->id,
                                    'ganjil_genap' => $jadwal->ganjil_genap,
                                    'tahun_akademik' => $jadwal->tahun_akademik,
                                ],
                                [
                                    'kj_id' => $jadwal->id,

                                    'nilai' => round($nilaiAkhir, 2),

                                    'nilai_array' => $nilaiArray,
                                    'bobot_array' => $normalizedBobots,

                                    'is_loocked' => false,
                                ]
                            );

                            // =====================================
                            // SIMPAN REKAP CPL
                            // =====================================
                            // foreach (
                            //     $rekapCpl as $cplId => $data
                            // ) {

                            //     $persentase =
                            //         $data['jumlah'] > 0
                            //             ? round(
                            //                 $data['total']
                            //                 / $data['jumlah'],
                            //                 2
                            //             )
                            //             : 0;

                            //     RekapCPLProdi::create([
                            //         'cpl_id' => $cplId,

                            //         'nilai' => round(
                            //             $persentase,
                            //             2
                            //         ),

                            //         'persentase' => $persentase,

                            //         'jumlah_pertemuan' => $data['jumlah'],
                            //     ]);
                            // }
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
