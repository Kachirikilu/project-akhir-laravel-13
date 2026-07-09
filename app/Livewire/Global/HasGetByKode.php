<?php

namespace App\Livewire\Global;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Models\Akademik\RPS;
use App\Models\Akademik\TimDosen;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;

trait HasGetByKode
{
    use WithJadwalFilters;
    use WithKelasFilters;
    use WithRPSFilters;

    protected function getProdiByKode(?string $kodePr): ?Prodi
    {
        if (blank($kodePr)) {
            return null;
        }

        return $this->findProdiByKode($kodePr);
    }

    // protected function findProdiByKode(string $kodePr): ?Prodi
    // {
    //     $strataMap = [
    //         'SARJANA' => 'Sarjana', 'S1' => 'Sarjana',
    //         'MAGISTER' => 'Magister', 'S2' => 'Magister',
    //         'DOKTOR' => 'Doktor', 'S3' => 'Doktor',
    //     ];

    //     $inputUpper = strtoupper($kodePr);
    //     $detectedStrata = null;

    //     foreach ($strataMap as $keyword => $value) {
    //         if (str_contains($inputUpper, $keyword)) {
    //             $detectedStrata = $value;
    //             break;
    //         }
    //     }

    //     $searchNormalized = preg_replace('/[^A-Za-z0-9]/', '', $inputUpper);

    //     $query = Prodi::query()
    //         ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
    //         ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
    //         ->whereRaw("
    //             REPLACE(REPLACE(UPPER(CONCAT(
    //                 CASE
    //                     WHEN prodis.strata = 'Sarjana' THEN 'S1'
    //                     WHEN prodis.strata = 'Magister' THEN 'S2'
    //                     WHEN prodis.strata = 'Doktor' THEN 'S3'
    //                     ELSE prodis.strata
    //                 END,
    //                 COALESCE(NULLIF(prodis.kode_pr, ''), NULLIF(departemens.kode_dp, ''), NULLIF(fakultas.kode_fk, ''), 'UNI')
    //             )), '-', ''), ' ', '') LIKE ?", ['%'.$searchNormalized.'%']);

    //     if ($detectedStrata) {
    //         $query->where('prodis.strata', $detectedStrata);
    //     }

    //     return $query->first();
    // }

    protected function findProdiByKode(string $kodePr): ?Prodi
    {
        $strataMap = [
            'SARJANA' => 'Sarjana', 'S1' => 'Sarjana',
            'MAGISTER' => 'Magister', 'S2' => 'Magister',
            'DOKTOR' => 'Doktor', 'S3' => 'Doktor',
        ];
        $normalizedInput = strtoupper(str_replace([' ', '-'], '', $kodePr));
        $detectedStrata = null;

        foreach ($strataMap as $keyword => $value) {
            if (str_starts_with($normalizedInput, $keyword)) {
                $detectedStrata = $value;
                $searchNormalized = substr($normalizedInput, strlen($keyword));
                break;
            }
        }

        if (! $detectedStrata) {
            $searchNormalized = $normalizedInput;
        }

        $query = Prodi::query()
            ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
            ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
            ->where(function ($q) use ($searchNormalized) {
                $q->whereRaw("REPLACE(REPLACE(UPPER(prodis.kode_pr), '-', ''), ' ', '') LIKE ?", ["%{$searchNormalized}%"])
                    ->orWhereRaw("REPLACE(REPLACE(UPPER(departemens.kode_dp), '-', ''), ' ', '') LIKE ?", ["%{$searchNormalized}%"])
                    ->orWhereRaw("REPLACE(REPLACE(UPPER(fakultas.kode_fk), '-', ''), ' ', '') LIKE ?", ["%{$searchNormalized}%"]);
            });

        if ($detectedStrata) {
            $query->where('prodis.strata', $detectedStrata);
        } else {
            $query->whereNotIn('prodis.strata', ['Sarjana', 'Magister', 'Doktor']);
        }

        return $query->first();
    }

    protected function getDepartemenByKode(?string $kodeDp): ?Departemen
    {
        if (blank($kodeDp)) {
            return null;
        }

        return $this->findDepartemenByKode($kodeDp);
    }

    protected function findDepartemenByKode(string $kodeDp): ?Departemen
    {
        $search = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kodeDp));

        return Departemen::query()
            ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
            ->where(function ($query) use ($search) {
                $query->whereRaw("REPLACE(REPLACE(UPPER(departemens.kode_dp), '-', ''), ' ', '') LIKE ?", ['%'.$search.'%']);
            })
            ->first();
    }

    protected function getFakultasByKode(?string $kodeFk): ?Fakultas
    {
        if (blank($kodeFk)) {
            return null;
        }

        return $this->findFakultasByKode($kodeFk);
    }

    protected function findFakultasByKode(string $kodeFk): ?Fakultas
    {
        $search = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kodeFk));

        return Fakultas::query()
            ->whereRaw("REPLACE(REPLACE(UPPER(kode_fk), '-', ''), ' ', '') LIKE ?", ['%'.$search.'%'])
            ->first();
    }

    protected function getRPSByKode(?string $kodeRPS): ?RPS
    {
        $rps = $this->findRPSByKode($kodeRPS);
        if (! $rps) {
            return null;
        }

        return $rps;
    }

    protected function findRPSByKode(?string $kodeRPS): ?RPS
    {
        if (blank($kodeRPS)) {
            return null;
        }

        $search = strtoupper(
            preg_replace('/[^A-Za-z0-9]/', '', trim($kodeRPS))
        );

        if (! preg_match('/^(\d{2})(\d{2})(01|02)([A-Z0-9]+)$/', $search, $m)) {
            return null;
        }

        [$full, $y1, $y2, $semester, $mkSearch] = $m;

        $cleanSearchUpper = strtoupper($mkSearch);
        $prefixPart = preg_replace('/[^A-Z]/', '', $cleanSearchUpper);
        $digitPart = preg_replace('/[^0-9]/', '', $cleanSearchUpper);

        return RPS::query()
            ->where('akademik', 'like', "%20{$y1}%")
            ->where('akademik', 'like', "%20{$y2}%")
            ->whereHas('mk_rel', function ($q) use ($semester, $prefixPart, $digitPart) {

                // Semester
                if ($semester === '01') {
                    $q->whereRaw('semester % 2 = 1');
                } else {
                    $q->whereRaw('semester % 2 = 0');
                }

                // Prefix kode MK
                if ($prefixPart !== '') {

                    $q->where(function ($low) use ($prefixPart) {

                        $low->where('mata_kuliahs.kode_mk', 'like', $prefixPart.'%')

                            ->orWhere(function ($q) use ($prefixPart) {
                                $q->where('mata_kuliahs.level_mk', 1)
                                    ->whereHas('prodis', function ($pro) use ($prefixPart) {
                                        $pro->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
                                            ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                            ->whereRaw(
                                                "COALESCE(prodis.kode_pr, departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?",
                                                [$prefixPart.'%']
                                            );
                                    });
                            })

                            ->orWhere(function ($q) use ($prefixPart) {
                                $q->where('mata_kuliahs.level_mk', 2)
                                    ->whereHas('prodis.dp_rel', function ($jur) use ($prefixPart) {
                                        $jur->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
                                            ->whereRaw(
                                                "COALESCE(departemens.kode_dp, fakultas.kode_fk, 'UNI') LIKE ?",
                                                [$prefixPart.'%']
                                            );
                                    });
                            })

                            ->orWhere(function ($q) use ($prefixPart) {
                                $q->where('mata_kuliahs.level_mk', 3)
                                    ->whereHas('prodis.dp_rel.fk_rel', function ($fak) use ($prefixPart) {
                                        $fak->whereRaw(
                                            "COALESCE(fakultas.kode_fk, 'UNI') LIKE ?",
                                            [$prefixPart.'%']
                                        );
                                    });
                            });

                        if ($prefixPart === 'UNI') {
                            $low->orWhere('mata_kuliahs.level_mk', 4);
                        }
                    });
                }

                // Digit MK
                if ($digitPart !== '') {

                    if (strlen($digitPart) <= 2) {
                        $q->where('mata_kuliahs.digit_semester', 'like', $digitPart.'%');
                    } else {
                        $q->where('mata_kuliahs.digit_semester', 'like', substr($digitPart, 0, 2).'%')
                        ->where('mata_kuliahs.digit_mk', 'like', substr($digitPart, 2).'%');
                    }
                }
            })
            ->first();
    }

    // protected function findRPSByKode(?string $kodeRPS): ?RPS
    // {
    //     if (blank($kodeRPS)) {
    //         return null;
    //     }
    //     $search = preg_replace(
    //         '/[^A-Za-z0-9]/',
    //         '',
    //         strtolower(trim($kodeRPS))
    //     );

    //     return $this->inputRPSSearch()
    //         ->get()
    //         ->first(function ($r) use ($search) {
    //             $kode = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 strtolower($r->kode)
    //             );

    //             return $kode === $search;
    //         });
    // }

    // protected function findRPSByKode(?string $kodeRPS): ?RPS
    // {
    //     if (blank($kodeRPS)) {
    //         return null;
    //     }
    //     $search = preg_replace(
    //         '/[^A-Za-z0-9]/',
    //         '',
    //         strtolower(trim($kodeRPS))
    //     );

    //     return $this->inputRPSSearch()
    //         ->get()
    //         ->first(function ($r) use ($search) {
    //             $kode = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 strtolower($r->kode)
    //             );

    //             return $kode === $search;
    //         });
    // }

    // protected function findRPSByKode(?string $kodeRPS): ?RPS
    // {
    //     if (blank($kodeRPS)) {
    //         return null;
    //     }

    //     $search = strtoupper(
    //         preg_replace('/[^A-Za-z0-9]/', '', trim($kodeRPS))
    //     );

    //     if (! preg_match('/^(\d{2})(\d{2})(01|02)([A-Z0-9]+)$/', $search, $m)) {
    //         return null;
    //     }

    //     [$full, $y1, $y2, $semester, $kodeMK] = $m;

    //     return RPS::query()
    //         ->where('akademik', 'like', "%20{$y1}%")
    //         ->where('akademik', 'like', "%20{$y2}%")
    //         ->whereHas('mk_rel', function ($q) use ($semester, $kodeMK) {

    //             if ($semester === '01') {
    //                 $q->whereRaw('semester % 2 = 1');
    //             } else {
    //                 $q->whereRaw('semester % 2 = 0');
    //             }

    //             $q->searchMK($kodeMK, true);
    //         })
    //         ->first();
    // }

    protected function getKelasByKode(?string $kodeKelas): ?Kelas
    {
        $kelas = $this->findKelasByKode($kodeKelas);
        if (! $kelas) {
            return null;
        }

        return $kelas;
    }

    protected function findKelasByKode(?string $kodeKelas): ?Kelas
    {
        if (blank($kodeKelas)) {
            return null;
        }

        $search = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($kodeKelas)));

        return Kelas::query()
            ->whereRaw("LOWER(REGEXP_REPLACE(kode_kelas, '[^A-Za-z0-9]', '')) = ?", [$search])
            ->first();
    }
    // protected function findKelasByKode(?string $kodeKelas): ?Kelas
    // {
    //     if (blank($kodeKelas)) {
    //         return null;
    //     }
    //     $search = preg_replace(
    //         '/[^A-Za-z0-9]/',
    //         '',
    //         strtolower(trim($kodeKelas))
    //     );
    //     return $this->inputKelasSearch()
    //         ->get()
    //         ->first(function ($j) use ($search) {
    //             $kode = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 strtolower($j->kode)
    //             );

    //             return $kode === $search;
    //         });
    // }

    protected function getJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    {
        $jadwal = $this->findJadwalByKode($kodeJadwal);
        if (! $jadwal) {
            return null;
        }

        return $jadwal;
    }

    protected function findJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    {
        if (blank($kodeJadwal)) {
            return null;
        }
        $search = preg_replace('/[^A-Za-z0-9]/', '', strtolower(trim($kodeJadwal)));
        $sqlTahunBlok = '
        CASE 
            WHEN YEAR(kelas_jadwals.tanggal_mulai) >= 3000 THEN CAST(YEAR(kelas_jadwals.tanggal_mulai) AS CHAR)
            WHEN YEAR(kelas_jadwals.tanggal_mulai) >= 2100 THEN RIGHT(YEAR(kelas_jadwals.tanggal_mulai), 3)
            WHEN YEAR(kelas_jadwals.tanggal_mulai) >= 2000 THEN RIGHT(YEAR(kelas_jadwals.tanggal_mulai), 2)
            ELSE CAST(YEAR(kelas_jadwals.tanggal_mulai) AS CHAR)
        END
    ';
        $sqlFullKode = "CONCAT(kelas.kode_kelas, '-', kelas_jadwals.label_kelas, '-', kelas_jadwals.kode_wilayah, '-', {$sqlTahunBlok})";

        return KelasJadwal::query()
            ->join('kelas', 'kelas.id', '=', 'kelas_jadwals.kelas_id')
            ->select('kelas_jadwals.*')
            ->whereRaw("LOWER(REGEXP_REPLACE({$sqlFullKode}, '[^A-Za-z0-9]', '')) = ?", [$search])
            ->first();
    }

    // protected function getJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    // {
    //     $jadwal = $this->findJadwalByKode($kodeJadwal);
    //     if (! $jadwal) {
    //         return null;
    //     }

    //     return $jadwal;
    // }
    // protected function findJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    // {
    //     if (blank($kodeJadwal)) {
    //         return null;
    //     }
    //     $search = preg_replace(
    //         '/[^A-Za-z0-9]/',
    //         '',
    //         strtolower(trim($kodeJadwal))
    //     );

    //     return $this->inputJadwalSearch()
    //         ->get()
    //         ->first(function ($r) use ($search) {
    //             $kode = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 strtolower($r->kode)
    //             );

    //             return $kode === $search;
    //         });
    // }

    // protected function findJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    // {
    //     if (blank($kodeJadwal)) {
    //         return null;
    //     }
    //     $search = preg_replace(
    //         '/[^A-Za-z0-9]/',
    //         '',
    //         strtolower(trim($kodeJadwal))
    //     );
    //     return $this->inputJadwalSearch()
    //         ->get()
    //         ->first(function ($j) use ($search) {
    //             $kode = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 strtolower($j->kode)
    //             );

    //             return $kode === $search;
    //         });
    // }

    protected function getTimDosenByKelas($rps_id, $pr_id)
    {
        return TimDosen::whereHas('rps', function ($query) use ($rps_id) {
            $query->where('rps.id', $rps_id);
        })
            ->whereHas('pr_rel', function ($query) use ($pr_id) {
                $query->where('prodis.id', $pr_id);
            })
            ->with(['dosens' => function ($query) {
                $query->withPivot('is_ketua', 'pertemuan_ke');
            }])
            ->get() ?? collect();
    }
}
