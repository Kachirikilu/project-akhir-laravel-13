<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Penilaian\LockNilai;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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

    // public function inputNilaiSearch($mhsId)
    // {
    //     $queryNilai = NilaiMahasiswa::query()
    //         ->with(['rps_rel', 'mahasiswa_rel']);

    //     $queryNilai->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
    //         $q->where('mahasiswas.id', $mhsId);
    //     });

    //     return $queryNilai;
    // }

    // public function inputNilaiSemesterSearch($mhsId)
    // {
    //     return NilaiMahasiswa::query()
    //         ->with(['rps_rel'])
    //         ->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
    //             $q->where('mahasiswas.id', $mhsId);
    //         });
    // }

    public function inputNilaiSearch($mhsId, $ganjil_genap = null, $akademik_url = null)
    {
        $queryNilai = NilaiMahasiswa::query()
            ->with(['rps_rel', 'mahasiswa_rel'])
            ->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
                $q->where('mahasiswas.id', $mhsId);
            });

        if ($ganjil_genap) {
            $queryNilai->where('ganjil_genap', $ganjil_genap);
        }
        if ($akademik_url) {
            $fixAkademik = str_replace('-', '/', $akademik_url);
            $queryNilai->where('akademik', $fixAkademik);
        }

        if (Auth::user()->mahasiswa) {
            $now = Carbon::now();
            $tahunAktif = $now->year;
            $bulanAktif = $now->month;

            $lock = LockNilai::where('pr_id', Auth::user()->pr_id)->first();

            if ($lock) {
                $tahunGanjil = ($bulanAktif == 1) ? $tahunAktif - 1 : $tahunAktif;
                $tglGanjilUnlock = Carbon::parse($tahunGanjil . '-' . $lock->ganjil_unlock);

                $tglGenapUnlock = Carbon::parse($tahunAktif . '-' . $lock->genap_unlock);

                $queryNilai->where(function ($q) use ($now, $tglGanjilUnlock, $tglGenapUnlock) {
                    $q->where(function ($sub) use ($now, $tglGanjilUnlock) {
                        if ($now->lessThan($tglGanjilUnlock)) {
                            $sub->where('ganjil_genap', '!=', 'Ganjil');
                        }
                    })
                    ->where(function ($sub) use ($now, $tglGenapUnlock) {
                        if ($now->lessThan($tglGenapUnlock)) {
                            $sub->where('ganjil_genap', '!=', 'Genap');
                        }
                    });
                });
            }
        }

        return $queryNilai;
    }

    public function addIPSemester($allNilai, $angkatan)
    {
        $groupedBySemester = $allNilai->groupBy(function ($item) {
            return $item->akademik . '|' . $item->ganjil_genap;
        });

        $processedPeriode = collect();

        foreach ($groupedBySemester as $key => $items) {
            [$tahunAkademik, $ganjilGenap] = explode('|', $key);
            
            // 1. Group by RPS untuk mendapatkan data unik per MK
            $uniqueRpsItems = $items->groupBy('rps_id')->map(function ($group) {
                return $group->sortByDesc('nilai')->first();
            });

            $totalSks = 0;
            $totalNilaiSksPerkalian = 0;
            $totalBobotPerkalian = 0;
            
            // Ambil semester dari item pertama yang ada di grup RPS (asumsi semua MK di periode ini punya semester yang sama atau Anda ingin ambil dari yang pertama ditemukan)
            $semesterAngka = $uniqueRpsItems->first()?->rps_rel?->mk_rel?->semester ?? '---';

            foreach ($uniqueRpsItems as $item) {
                $sks = $item->sks ?? ($item->rps_rel?->sks ?? 0);
                $totalSks += $sks;

                $nilaiAngka = (float) ($item->nilai ?? 0);
                $totalNilaiSksPerkalian += ($nilaiAngka * $sks);

                // Konversi ke Bobot (Skala 4)
                if ($nilaiAngka >= 86) { $bobot = 4.00; }
                elseif ($nilaiAngka >= 80) { $bobot = 3.70; }
                elseif ($nilaiAngka >= 75) { $bobot = 3.30; }
                elseif ($nilaiAngka >= 70) { $bobot = 3.00; }
                elseif ($nilaiAngka >= 65) { $bobot = 2.70; }
                elseif ($nilaiAngka >= 60) { $bobot = 2.30; }
                elseif ($nilaiAngka >= 56) { $bobot = 2.00; }
                elseif ($nilaiAngka >= 40) { $bobot = 1.00; }
                else { $bobot = 0.00; }

                $totalBobotPerkalian += ($bobot * $sks);
            }

            // Hitung Nilai Semester
            $nilaiSemester = $totalSks > 0 ? round($totalNilaiSksPerkalian / $totalSks, 2) : 0.00;
            $ipSemester = $totalSks > 0 ? round($totalBobotPerkalian / $totalSks, 2) : 0.00;

            // Mutu Semester
            if ($nilaiSemester >= 86) $mutuSemester = 'A';
            elseif ($nilaiSemester >= 80) $mutuSemester = 'A-';
            elseif ($nilaiSemester >= 75) $mutuSemester = 'B+';
            elseif ($nilaiSemester >= 70) $mutuSemester = 'B';
            elseif ($nilaiSemester >= 65) $mutuSemester = 'B-';
            elseif ($nilaiSemester >= 60) $mutuSemester = 'C+';
            elseif ($nilaiSemester >= 56) $mutuSemester = 'C';
            elseif ($nilaiSemester >= 40) $mutuSemester = 'D';
            else $mutuSemester = 'E';

            $semesterAngka = '---';

            $itemContoh = $items->first(); 
            if ($itemContoh && $itemContoh->rps_rel && $itemContoh->rps_rel->mk_rel) {
                $semesterAngka = $itemContoh->rps_rel->mk_rel->semester;
            } else {
                if ($angkatan && $tahunAkademik) {
                    $tahunMulaiSesi = (int) explode('/', $tahunAkademik)[0];
                    $selisihTahun = $tahunMulaiSesi - $angkatan;
                    $semesterAngka = ($selisihTahun * 2) + ($ganjilGenap === 'Ganjil' ? 1 : 2);
                }
            }

            // Simpan ke collection
            $processedPeriode->push((object) [
                'akademik' => $tahunAkademik,
                'ganjil_genap' => $ganjilGenap,
                'total_mk' => $uniqueRpsItems->count(), // Menghitung jumlah unik RPS
                'total_sks' => $totalSks,
                'nilai_semester' => number_format($nilaiSemester, 2, '.', ''),
                'mutu_semester' => $mutuSemester,
                'ip_semester' => number_format($ipSemester, 2, '.', ''),
                'semester' => $semesterAngka, // Mengambil langsung dari relasi
            ]);
        }

        return $processedPeriode->sortByDesc(function ($p) {
            return $p->akademik . $p->ganjil_genap;
        });
    }
}
