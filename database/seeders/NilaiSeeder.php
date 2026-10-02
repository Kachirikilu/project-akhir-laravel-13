<?php

namespace Database\Seeders;

use App\Models\Kelas\KelasJadwal;
use App\Models\Akademik\RPS;
use App\Models\Auth\Mahasiswa;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedByJadwal();
        $this->seedByRps();
    }

    private function seedByJadwal()
    {
        $nilaiCount = (int) config('seeder.count_nilai_per_rps', 1000);

        KelasJadwal::with(['mahasiswas', 'kelas_rel.rps_rel'])->chunk(config('seeder.batch_nilai', 128), function ($jadwals) use ($nilaiCount) {
            foreach ($jadwals as $jadwal) {
                $rps = $jadwal->kelas_rel?->rps_rel;
                if (!$rps) continue;

                $existingCount = NilaiMahasiswa::where('rps_id', $rps->id)->count();
                if ($existingCount >= $nilaiCount) {
                    continue;
                }

                $sisaKuota = $nilaiCount - $existingCount;
                $data = $this->getMappingFromRps($rps);

                $mahasiswas = $jadwal->mahasiswas->take($sisaKuota);

                foreach ($mahasiswas as $mahasiswa) {
                    $this->saveNilai($mahasiswa, $rps, $data, $jadwal->id, $jadwal->ganjil_genap, $jadwal->akademik);
                }
            }
        });
    }

    private function seedByRps()
    {
        // 1. Ambil count_nilai_per_rps & count_nilai_per_prodi_rps
        $nilaiCount = (int) config('seeder.count_nilai_per_rps', 1000);
        $configuredProdiCount = (int) config('seeder.count_nilai_per_prodi_rps', 50);

        // Max per prodi 1:1 dengan count_nilai_per_rps (aturan 1/3 dicabut)
        $limitProdi = min($configuredProdiCount, $nilaiCount);

        $rpsList = RPS::with(['mk_rel.prodis'])->get();

        foreach ($rpsList as $rps) {
            $mk = $rps->mk_rel;

            if (! $mk || ! $mk->semester || $mk->prodis->isEmpty()) {
                continue;
            }

            $semesterMk = (int) $mk->semester;
            if ($semesterMk < 1 || $semesterMk > 8) {
                continue;
            }

            $data = $this->getMappingFromRps($rps);

            foreach ($mk->prodis as $prodi) {
                // Total nilai saat ini untuk RPS ini
                $currentTotalRps = NilaiMahasiswa::where('rps_id', $rps->id)->count();
                if ($currentTotalRps >= $nilaiCount) {
                    break; // Total nilai RPS sudah penuh
                }

                // Cek jumlah nilai pada RPS ini khusus untuk Mahasiswa yang terdaftar di prodi terkait
                // Menggunakan relasi mahasiswa_rel -> pr_id
                $existingProdiCount = NilaiMahasiswa::where('rps_id', $rps->id)
                    ->whereHas('mahasiswa_rel', function ($q) use ($prodi) {
                        $q->where('pr_id', $prodi->id);
                    })->count();

                if ($existingProdiCount >= $limitProdi) {
                    continue; // Kuota untuk prodi ini pada RPS ini sudah penuh
                }

                // Hitung sisa target yang harus diisi untuk prodi ini
                $sisaKuotaProdi = $limitProdi - $existingProdiCount;
                $sisaKuotaRps = $nilaiCount - $currentTotalRps;
                $targetTake = min($sisaKuotaProdi, $sisaKuotaRps);

                // Ambil mahasiswa prodi secara konsisten berdasarkan order ID
                $mahasiswas = Mahasiswa::where('pr_id', $prodi->id)
                    ->orderBy('id', 'asc')
                    ->get();

                $insertedForThisProdi = 0;

                foreach ($mahasiswas as $mahasiswa) {
                    if ($insertedForThisProdi >= $targetTake) {
                        break;
                    }

                    $angkatan = (int) $mahasiswa->angkatan;

                    $tahunOffset = intdiv($semesterMk - 1, 2);
                    $tahunAwal = $angkatan + $tahunOffset;
                    $tahunAkhir = $tahunAwal + 1;

                    $akademik = "{$tahunAwal}/{$tahunAkhir}";
                    $ganjilGenap = $semesterMk % 2 === 1 ? 'Ganjil' : 'Genap';

                    $exists = NilaiMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                        ->where('rps_id', $rps->id)
                        ->where('akademik', $akademik)
                        ->where('ganjil_genap', $ganjilGenap)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $this->saveNilai(
                        $mahasiswa,
                        $rps,
                        $data,
                        null,
                        $ganjilGenap,
                        $akademik
                    );

                    $insertedForThisProdi++;
                }
            }
        }
    }

    private function getMappingFromRps(RPS $rps)
    {
        $scpmkCollection = $rps->scpmkAtr;
        $nilaiArray = [];
        $bobotArray = [];

        foreach ($scpmkCollection as $index => $item) {
            $bobot = (float)($item->bobot_normalisasi ?? 0) / 100;
            $nilaiArray[$index] = rand(60, 95);
            $bobotArray[$index] = $bobot;
        }

        return ['nilai_array' => $nilaiArray, 'bobot_array' => $bobotArray];
    }

    private function saveNilai($mahasiswa, $rps, $data, $kjId, $gg, $ta)
    {
        $nilaiAkhir = 0;
        foreach ($data['nilai_array'] as $index => $nilai) {
            $nilaiAkhir += ($nilai * $data['bobot_array'][$index]);
        }

        NilaiMahasiswa::updateOrCreate(
            [
                'mahasiswa_id'   => $mahasiswa->id,
                'rps_id'         => $rps->id,
                'ganjil_genap'   => $gg,
                'akademik'       => $ta,
            ],
            [
                'kj_id'          => $kjId,
                'nilai'          => round($nilaiAkhir, 2),
                'nilai_array'    => $data['nilai_array'],
                'bobot_array'    => $data['bobot_array'],
            ]
        );
    }
}