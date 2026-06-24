<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithNilaiMahasiswaFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterNilai = '';

    public $filterNilaigg = '';

    public $totalGanjilNilai = '';

    public $totalGenapNilai = '';

    public function inputNilaiSearch($mhsId)
    {
        $queryNilai = NilaiMahasiswa::query()
            ->with(['rps_rel', 'mahasiswa_rel']);

        $queryNilai->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
            $q->where('mahasiswas.id', $mhsId);
        });

        return $queryNilai;
    }

    public function inputNilaiSemesterSearch($mhsId)
    {
        return NilaiMahasiswa::query()
            ->with(['rps_rel'])
            ->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
                $q->where('mahasiswas.id', $mhsId);
            });
    }

    public function addIPSemester($allNilai, $angkatan)
    {
        $groupedBySemester = $allNilai->groupBy(function ($item) {
            return $item->tahun_akademik.'|'.$item->ganjil_genap;
        });

        $processedPeriode = collect();

        foreach ($groupedBySemester as $key => $items) {
            [$tahunAkademik, $ganjilGenap] = explode('|', $key);
            $uniqueRpsItems = $items->groupBy('rps_id')->map(function ($group) {
                return $group->sortByDesc('nilai')->first();
            });

            $totalSks = 0;
            $totalNilaiSksPerkalian = 0; // Langkah 1: Akumulasi Nilai Skala 100 * SKS
            $totalBobotPerkalian = 0;

            foreach ($uniqueRpsItems as $item) {
                $sks = $item->sks ?? ($item->rps_rel?->sks ?? 0);
                $totalSks += $sks;

                $nilaiAngka = (float) ($item->nilai ?? 0);

                // 1. Hitung perkalian nilai matakuliah dengan SKS-nya
                $totalNilaiSksPerkalian += ($nilaiAngka * $sks);

                // 2. Tentukan bobot skala 4.00 berdasarkan range nilai_angka
                if ($nilaiAngka >= 86) {
                    $bobot = 4.00;
                } elseif ($nilaiAngka >= 80) {
                    $bobot = 3.70;
                } elseif ($nilaiAngka >= 75) {
                    $bobot = 3.30;
                } elseif ($nilaiAngka >= 70) {
                    $bobot = 3.00;
                } elseif ($nilaiAngka >= 65) {
                    $bobot = 2.70;
                } elseif ($nilaiAngka >= 60) {
                    $bobot = 2.30;
                } elseif ($nilaiAngka >= 56) {
                    $bobot = 2.00;
                } elseif ($nilaiAngka >= 40) {
                    $bobot = 1.00;
                } else {
                    $bobot = 0.00;
                }
                // $bobot = round((floatval($nilaiAngka) / 100) * 4, 2);

                // Akumulasi bobot perkalian SKS untuk IP Semester
                $totalBobotPerkalian += ($bobot * $sks);
            }

            // CARI 1: Nilai Rata-Rata Semester (Skala 100)
            $nilaiSemester = $totalSks > 0 ? round($totalNilaiSksPerkalian / $totalSks, 2) : 0.00;

            // CARI 2: Huruf Mutu Semester berdasarkan hasil Nilai Rata-Rata Semester
            if ($nilaiSemester >= 86) {
                $mutuSemester = 'A';
            } elseif ($nilaiSemester >= 80) {
                $mutuSemester = 'A-';
            } elseif ($nilaiSemester >= 75) {
                $mutuSemester = 'B+';
            } elseif ($nilaiSemester >= 70) {
                $mutuSemester = 'B';
            } elseif ($nilaiSemester >= 65) {
                $mutuSemester = 'B-';
            } elseif ($nilaiSemester >= 60) {
                $mutuSemester = 'C+';
            } elseif ($nilaiSemester >= 56) {
                $mutuSemester = 'C';
            } elseif ($nilaiSemester >= 40) {
                $mutuSemester = 'D';
            } else {
                $mutuSemester = 'E';
            }

            // CARI 3: IP Semester (Skala 4) yang diganti dari variabel 'ips'
            $ipSemester = $totalSks > 0 ? round($totalBobotPerkalian / $totalSks, 2) : 0.00;

            // 4. Hitung Konversi Angka Semester
            $semesterAngka = '---';
            if ($angkatan && $tahunAkademik) {
                $tahunMulaiSesi = (int) explode('/', $tahunAkademik)[0];
                $selisihTahun = $tahunMulaiSesi - $angkatan;

                if ($selisihTahun >= 0) {
                    $baseSemester = ($selisihTahun * 2) + 1;
                    $semesterAngka = ($ganjilGenap === 'Genap') ? $baseSemester + 1 : $baseSemester;
                }
            }

            // Simpan ke collection hasil olahan data
            $processedPeriode->push((object) [
                'akademik' => $tahunAkademik,
                'ganjil_genap' => $ganjilGenap,
                'total_sks' => $totalSks,
                'nilai_semester' => number_format($nilaiSemester, 2, '.', ''),
                'mutu_semester' => $mutuSemester,
                'ip_semester' => number_format($ipSemester, 2, '.', ''),
                'semester' => $semesterAngka,
            ]);
        }

        return $processedPeriode->sortByDesc(function ($p) {
            return $p->akademik.$p->ganjil_genap;
        });
    }

    // public function buttonNilaiFilter($queryNilai)
    // {
    //     if ($this->filterNilai === '') {
    //         $totalMKSaya = $queryNilai->whereHas('prodis', function ($q) {
    //             $q->where('prodis.id', Auth::user()->pr_id);
    //         });
    //     } elseif ($this->filterNilai === 'mk-wajib') {
    //         $queryNilai->where('is_wajib', true);
    //     } elseif ($this->filterNilai === 'mk-pilihan') {
    //         $queryNilai->where('is_wajib', false);
    //     } elseif ($this->filterNilai === 'mk-universitas') {
    //         $queryNilai->where('level_mk', 4);
    //     }

    //     $this->totalGanjilNilai = (clone $queryNilai)->whereRaw('semester % 2 = 1')->count();
    //     $this->totalGenapNilai = (clone $queryNilai)->whereRaw('semester % 2 = 0')->count();

    //     if ($this->filterNilaigg === 'mk-ganjil') {
    //         $queryNilai->whereRaw('semester % 2 = 1');
    //     } elseif ($this->filterNilaigg === 'mk-genap') {
    //         $queryNilai->whereRaw('semester % 2 = 0');
    //     }
    // }

    // public function buttonNilaiSwitch($queryNilai)
    // {
    //     $mapTipe = [
    //         'tatap-muka' => 1,
    //         'praktikum' => 2,
    //         'praktek-lapangan' => 3,
    //         'simulasi' => 4,
    //     ];

    //     $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

    //     if ($currentTabTipe) {
    //         $queryNilai->where('tipe_sks', $currentTabTipe);
    //     }
    // }

    // public function filterByNilai($nilai)
    // {
    //     $this->filterNilai = $nilai;
    //     $this->resetPage();
    // }

    // public function filterByNilaigg($nilai)
    // {
    //     $this->filterNilaigg = $nilai;
    //     $this->resetPage();
    // }
}
