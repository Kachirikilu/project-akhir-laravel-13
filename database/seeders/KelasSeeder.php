<?php

namespace Database\Seeders;

use App\Models\Akademik\RPS;
use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasSesi;
use App\Models\Kelas\MahasiswaKehadiran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $batchSize = 50;
        $kelasPerRPS = 4;

        $kelasPerMahasiswaMin = 6;
        $kelasPerMahasiswaMax = 10;

        $totalProcessed = 0;

        $this->command->info(
            "Starting KelasSeeder with chunk size {$batchSize}..."
        );

        $targetKelasMahasiswa = Mahasiswa::pluck('id')
            ->mapWithKeys(fn ($id) => [
                $id => rand(
                    $kelasPerMahasiswaMin,
                    $kelasPerMahasiswaMax
                ),
            ])
            ->toArray();

        $kelasDiambilMahasiswa = [];

        RPS::with([
            'cpmks.scpmks',
            'mk_rel.prodis',
        ])
            ->chunk($batchSize, function (
                $allRps
            ) use (
                &$totalProcessed,
                $kelasPerRPS,
                $targetKelasMahasiswa,
                &$kelasDiambilMahasiswa
            ) {

                foreach ($allRps as $rps) {

                    $scpmkList = $rps->cpmks
                        ->flatMap(function ($cpmk) {
                            return $cpmk->scpmks;
                        })
                        ->unique('id')
                        ->sortBy('pivot.sort_order')
                        ->values();

                    $totalScpmk =
                        $scpmkList->count();

                    if (
                        $totalScpmk < 14 ||
                        $totalScpmk > 16
                    ) {
                        continue;
                    }

                    $prodis = $rps->mk_rel?->prodis;

                    if ($prodis?->isEmpty()) {
                        continue;
                    }

                    // ===================================
                    // kelas per RPS (TOTAL)
                    // ===================================
                    for (
                        $kelasIndex = 1;
                        $kelasIndex <= $kelasPerRPS;
                        $kelasIndex++
                    ) {

                        // pilih salah satu prodi random
                        $prodi = $prodis->random();

                        DB::transaction(function () use (
                            $rps,
                            $scpmkList,
                            $totalScpmk,
                            $prodi,
                            $kelasIndex,
                            $targetKelasMahasiswa,
                            &$kelasDiambilMahasiswa
                        ) {
                            $kelas = Kelas::create([
                                'kode_kelas' => $this->generateUniqueKode(
                                    Kelas::class,
                                    'kode_kelas'
                                ),

                                'rps_id' => $rps->id,

                                'pr_id' => $prodi->id,

                                'nama_kelas' => 'Kelas '.
                                    $rps->deskripsi.
                                    ' - '.
                                    $prodi->nama_pr.
                                    ' - '.
                                    chr(64 + $kelasIndex),
                            ]);

                            // ==========================
                            // Jadwal
                            // ==========================
                            $wilayah =
                                ['IDL', 'PLG'][rand(0, 1)];

                            $labels =
                                ['A', 'B', 'C', 'D'];

                            $jumlahJadwal =
                                rand(2, 4);

                            for (
                                $i = 0;
                                $i < $jumlahJadwal;
                                $i++
                            ) {

                                $hari = [
                                    'Senin',
                                    'Selasa',
                                    'Rabu',
                                    'Kamis',
                                    'Jumat',
                                ][rand(0, 4)];

                                $tglMulai =
                                    now()->addDays(
                                        rand(1, 30)
                                    );

                                $jadwal =
                                    $kelas
                                        ->jadwals()
                                        ->create([
                                            'kode_wilayah' => $wilayah,
                                            'password' => strtoupper(
                                                str()->random(6)
                                            ),
                                            'label_kelas' => $labels[$i],
                                            'tanggal_mulai' => $tglMulai,
                                            'tanggal_berakhir' => (clone $tglMulai)
                                                ->addMonths(4),
                                            'hari_pelaksanaan' => $hari,
                                            'jam_mulai' => '08:00:00',
                                            'jam_berakhir' => '10:30:00',
                                            'kapasitas' => rand(30, 40),
                                        ]);
                                // ==========================
                                // FILTER mahasiswa yang masih bisa ambil kelas
                                // ==========================
                                $candidateIds = collect($targetKelasMahasiswa)
                                    ->filter(function ($target, $mhsId) use ($kelasDiambilMahasiswa) {

                                        return (
                                            $kelasDiambilMahasiswa[$mhsId] ?? 0
                                        ) < $target;
                                    })
                                    ->keys()
                                    ->shuffle();

                                $maxMahasiswa = min(
                                    $jadwal->kapasitas,
                                    $candidateIds->count()
                                );

                                if ($maxMahasiswa === 0) {
                                    continue;
                                }

                                $jumlahMahasiswa = rand(
                                    min(20, $maxMahasiswa),
                                    $maxMahasiswa
                                );

                                $mhsIds = $candidateIds
                                    ->take($jumlahMahasiswa)
                                    ->values();

                                // update counter mahasiswa
                                foreach ($mhsIds as $mhsId) {

                                    $kelasDiambilMahasiswa[$mhsId] =
                                        ($kelasDiambilMahasiswa[$mhsId] ?? 0)
                                        + 1;
                                }

                                $jadwal
                                    ->mahasiswas()
                                    ->attach($mhsIds);

                                $scpmkIndex = 0;

                                for (
                                    $pertemuan = 1;
                                    $pertemuan <= 16;
                                    $pertemuan++
                                ) {

                                    $sesi =
                                        KelasSesi::create([
                                            'kj_id' => $jadwal->id,
                                            'pertemuan_ke' => $pertemuan,
                                            'tanggal' => (clone $tglMulai)
                                                ->addWeeks(
                                                    $pertemuan - 1
                                                ),
                                            'catatan' => "Sesi rutin pertemuan ke-{$pertemuan}",
                                        ]);

                                    // 4. Generate 16 Sesi
                                    // Seeder absensi mahasiswa
                                    $statuses = [
                                        'Hadir',
                                        'Terlambat',
                                        'Absen',
                                        'Sakit',
                                        'Izin',
                                        'Dispensasi',
                                    ];

                                    // bobot probabilitas agar realistis
                                    $weights = [
                                        'Hadir' => 65,
                                        'Terlambat' => 10,
                                        'Absen' => 8,
                                        'Sakit' => 5,
                                        'Izin' => 7,
                                        'Dispensasi' => 5,
                                    ];

                                    foreach ($mhsIds as $mhsId) {

                                        // random weighted status
                                        $random = rand(1, array_sum($weights));
                                        $current = 0;
                                        $selectedStatus = 'Absen';

                                        foreach ($weights as $status => $weight) {
                                            $current += $weight;

                                            if ($random <= $current) {
                                                $selectedStatus = $status;
                                                break;
                                            }
                                        }

                                        MahasiswaKehadiran::create([
                                            'sesi_id' => $sesi->id,
                                            'mahasiswa_id' => $mhsId,
                                            'status' => $selectedStatus,

                                            'waktu_presensi' => in_array($selectedStatus, [
                                                'Hadir',
                                                'Terlambat',
                                                'Izin',
                                                'Sakit',
                                                'Dispensasi',
                                            ])
                                                ? $sesi->tanggal
                                                    ->copy()
                                                    ->setTime(8, rand(0, 45))
                                                : null,

                                            'keterangan' => match ($selectedStatus) {
                                                'Sakit' => 'Sakit demam',
                                                'Izin' => 'Ada keperluan keluarga',
                                                'Dispensasi' => 'Dispensasi akademik',
                                                default => null,
                                            },
                                        ]);
                                    }

                                    if ($pertemuan == 8 && $totalScpmk < 16 && $rps->bobot_uts) {
                                        $sesi->override()->create([
                                            'deskripsi' => 'Ujian Tengah Semester',
                                            'metode' => 'UTS',
                                            'bobot' => $rps->bobot_uts,
                                        ]);

                                        continue;
                                    }

                                    if ($pertemuan == 16 && $totalScpmk < 16 && $rps->bobot_uas) {
                                        $sesi->override()->create([
                                            'deskripsi' => 'Ujian Akhir Semester',
                                            'metode' => 'UAS',
                                            'bobot' => $rps->bobot_uas,
                                        ]);

                                        continue;
                                    }

                                    if (isset($scpmkList[$scpmkIndex])) {
                                        $scpmkIndex++;
                                    }

                                    if (
                                        isset(
                                            $scpmkList[
                                                $scpmkIndex
                                            ]
                                        )
                                    ) {
                                        $scpmkIndex++;
                                    }
                                }
                            }
                        });
                    }
                    $totalProcessed++;
                }

                $this->command->info(
                    "Processed {$totalProcessed} RPS records..."
                );
            });

        $this->command->info(
            "KelasSeeder finished. Total RPS processed: {$totalProcessed}"
        );
    }

    private function generateKode($prefixMin = 3, $prefixMax = 4, $numMin = 2, $numMax = 3)
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
