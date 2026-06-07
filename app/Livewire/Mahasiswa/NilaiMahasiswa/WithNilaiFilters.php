<?php

namespace App\Livewire\Mahasiswa\NilaiMahasiswa;

use App\Livewire\Global\HasSortir;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithNilaiFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterNilai = '';

    public $filterNilaigg = '';

    public $totalGanjil = '';

    public $totalGenap = '';

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
        // Mengambil seluruh data nilai milik mahasiswa terkait beserta relasi yang dibutuhkan
        return NilaiMahasiswa::query()
            ->with(['rps_rel'])
            ->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
                $q->where('mahasiswas.id', $mhsId);
            });
    }

    public function indexIPK($allNilai, $angkatan)
    {
        $groupedBySemester = $allNilai->groupBy(function ($item) {
            return $item->tahun_akademik.'|'.strtolower($item->ganjil_genap);
        });

        $processedPeriode = collect();

        foreach ($groupedBySemester as $key => $items) {
            [$tahunAkademik, $ganjilGenap] = explode('|', $key);
            $uniqueRpsItems = $items->groupBy('rps_id')->map(function ($group) {
                return $group->sortByDesc('nilai')->first();
            });

            $totalSks = 0;
            $totalBobotPerkalian = 0;

            foreach ($uniqueRpsItems as $item) {
                $sks = $item->sks ?? ($item->rps_rel?->sks ?? 0);
                $totalSks += $sks;

                // Konversi nilai angka ke bobot skala 4.00
                $nilaiAngka = $item->nilai ?? 0;
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

                $totalBobotPerkalian += ($bobot * $sks);
            }

            // 4. Hitung Nilai IPS Akhir Sesi
            $ips = $totalSks > 0 ? round($totalBobotPerkalian / $totalSks, 2) : 0.00;

            // 5. Hitung Konversi Angka Semester (1, 2, 3, dst) berdasarkan Angkatan
            $semesterAngka = '---';
            if ($angkatan && $tahunAkademik) {
                $tahunMulaiSesi = (int) explode('/', $tahunAkademik)[0];
                $selisihTahun = $tahunMulaiSesi - $angkatan;

                if ($selisihTahun >= 0) {
                    $baseSemester = ($selisihTahun * 2) + 1;
                    $semesterAngka = ($ganjilGenap === 'genap') ? $baseSemester + 1 : $baseSemester;
                }
            }

            // Masukkan hasil kalkulasi ke dalam struktur objek/stdClass baru
            $processedPeriode->push((object) [
                'akademik' => $tahunAkademik,
                'ganjil_genap' => $ganjilGenap,
                'total_sks' => $totalSks,
                'ips' => $ips,
                'semester' => $semesterAngka,
            ]);
        }

        // Urutkan dari semester paling terbaru
        return $processedPeriode->sortByDesc(function ($p) {
            return $p->akademik.$p->ganjil_genap;
        });
    }

    public function buttonNilaiFilter($queryNilai)
    {
        if ($this->filterNilai === '') {
            $totalMKSaya = $queryNilai->whereHas('prodis', function ($q) {
                $q->where('prodis.id', Auth::user()->pr_id);
            });
        } elseif ($this->filterNilai === 'mk-wajib') {
            $queryNilai->where('is_wajib', true);
        } elseif ($this->filterNilai === 'mk-pilihan') {
            $queryNilai->where('is_wajib', false);
        } elseif ($this->filterNilai === 'mk-universitas') {
            $queryNilai->where('level_mk', 4);
        }

        $this->totalGanjil = (clone $queryNilai)->whereRaw('semester % 2 = 1')->count();
        $this->totalGenap = (clone $queryNilai)->whereRaw('semester % 2 = 0')->count();

        if ($this->filterNilaigg === 'mk-ganjil') {
            $queryNilai->whereRaw('semester % 2 = 1');
        } elseif ($this->filterNilaigg === 'mk-genap') {
            $queryNilai->whereRaw('semester % 2 = 0');
        }
    }

    public function buttonNilaiSwitch($queryNilai)
    {
        $mapTipe = [
            'tatap-muka' => 1,
            'praktikum' => 2,
            'praktek-lapangan' => 3,
            'simulasi' => 4,
        ];

        $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

        if ($currentTabTipe) {
            $queryNilai->where('tipe_sks', $currentTabTipe);
        }
    }

    public function filterByNilai($nilai)
    {
        $this->filterNilai = $nilai;
        $this->resetPage();
    }

    public function filterByNilaigg($nilai)
    {
        $this->filterNilaigg = $nilai;
        $this->resetPage();
    }
}
