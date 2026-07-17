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
        KelasJadwal::with(['mahasiswas', 'kelas_rel.rps_rel'])->chunk(100, function ($jadwals) {
            foreach ($jadwals as $jadwal) {
                $rps = $jadwal->kelas_rel?->rps_rel;
                if (!$rps) continue;

                $data = $this->getMappingFromRps($rps);
                foreach ($jadwal->mahasiswas as $mahasiswa) {
                    $this->saveNilai($mahasiswa, $rps, $data, $jadwal->id, $jadwal->ganjil_genap, $jadwal->akademik);
                }
            }
        });
    }

    private function seedByRps()
    {
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
                $mahasiswas = Mahasiswa::where('pr_id', $prodi->id)->get();

                foreach ($mahasiswas as $mahasiswa) {
                    $angkatan = (int) $mahasiswa->angkatan;

                    // Semester 1-2 = offset 0
                    // Semester 3-4 = offset 1
                    // Semester 5-6 = offset 2
                    // Semester 7-8 = offset 3
                    $tahunOffset = intdiv($semesterMk - 1, 2);

                    $tahunAwal = $angkatan + $tahunOffset;
                    $tahunAkhir = $tahunAwal + 1;

                    $akademik = "{$tahunAwal}/{$tahunAkhir}";

                    $ganjilGenap = $semesterMk % 2 === 1
                        ? 'Ganjil'
                        : 'Genap';

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