<?php

namespace App\Livewire\Global;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSFilters;
use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\TimDosen;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;

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

    protected function findProdiByKode(string $kodePr): ?Prodi
    {
        $strataMap = [
            'SARJANA' => 'Sarjana', 'S1' => 'Sarjana',
            'MAGISTER' => 'Magister', 'S2' => 'Magister',
            'DOKTOR' => 'Doktor', 'S3' => 'Doktor',
        ];

        $inputUpper = strtoupper($kodePr);
        $detectedStrata = null;

        foreach ($strataMap as $keyword => $value) {
            if (str_contains($inputUpper, $keyword)) {
                $detectedStrata = $value;
                break;
            }
        }

        $searchNormalized = preg_replace('/[^A-Za-z0-9]/', '', $inputUpper);

        $query = Prodi::query()
            ->leftJoin('departemens', 'prodis.dp_id', '=', 'departemens.id')
            ->leftJoin('fakultas', 'departemens.fk_id', '=', 'fakultas.id')
            ->whereRaw("
                REPLACE(REPLACE(UPPER(CONCAT(
                    CASE 
                        WHEN prodis.strata = 'Sarjana' THEN 'S1'
                        WHEN prodis.strata = 'Magister' THEN 'S2'
                        WHEN prodis.strata = 'Doktor' THEN 'S3'
                        ELSE prodis.strata 
                    END,
                    COALESCE(NULLIF(prodis.kode_pr, ''), NULLIF(departemens.kode_dp, ''), NULLIF(fakultas.kode_fk, ''), 'UNI')
                )), '-', ''), ' ', '') LIKE ?", ['%' . $searchNormalized . '%']);

        if ($detectedStrata) {
            $query->where('prodis.strata', $detectedStrata);
        }

        return $query->first();
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
        $search = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            strtolower(trim($kodeRPS))
        );

        return $this->inputRPSSearch()
            ->get()
            ->first(function ($r) use ($search) {
                $kode = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    strtolower($r->kode)
                );

                return $kode === $search;
            });
    }

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

    protected function getTimDosenByKelas($rps_id, $pr_id) {
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
