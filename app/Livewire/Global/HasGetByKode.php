<?php

namespace App\Livewire\Global;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
use App\Models\Akademik\RPS;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;

trait HasGetByKode
{
    use WithJadwalFilters;
    use WithKelasFilters;
    use WithRPSFilters;

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
}
