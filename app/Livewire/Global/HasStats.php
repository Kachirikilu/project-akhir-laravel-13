<?php

namespace App\Livewire\Global;

trait HasStats
{
    /**
     * 1. Mengambil statistik RPS
     */
    private function getStatsRps($query, $currentYear, $fiveYearsAgo): array
    {
        $stats['rps-prodi'] = (clone $query)
            ->whereHas('mk_rel.prodis', function ($q) {
                $q->where('prodis.id', auth()->user()->pr_id);
            })->count();

        $stats['rps-akademik'] = (clone $query)->where('akademik', 'like', "%$currentYear%")->count();
        $stats['rps-rev-new'] = (clone $query)->whereYear('revisi', $currentYear)->count();
        $stats['rps-aktif'] = (clone $query)->where('is_draf', false)->count();
        $stats['rps-draf'] = (clone $query)->where('is_draf', true)->count();

        $stats['rps-older-5'] = (clone $query)
            ->whereRaw('CAST(SUBSTRING(akademik,1,4) AS UNSIGNED) < ?', [$fiveYearsAgo->year])
            ->count();

        return $stats;
    }

    /**
     * 2. Mengambil statistik CPL, CPMK, dan Sub-CPMK
     * (Disatukan karena struktur logikanya 100% sama, hanya beda prefix key array)
     */
    private function getStatsKurikulum($query, string $prefix, $currentYear, $now, $sixMonthsAgo, $fiveYearsAgo): array
    {
        $stats["{$prefix}-month"] = (clone $query)->whereMonth('created_at', $now->month)->whereYear('created_at', $currentYear)->count();
        $stats["{$prefix}-6-months"] = (clone $query)->where('created_at', '>=', $sixMonthsAgo)->count();
        $stats["{$prefix}-year"] = (clone $query)->whereYear('created_at', $currentYear)->count();
        $stats["{$prefix}-older-5"] = (clone $query)->where('created_at', '<', $fiveYearsAgo)->count();

        return $stats;
    }

    /**
     * 3. Mengambil statistik Referensi
     */
    private function getStatsReferensi($query, $currentYear): array
    {
        $stats['ref-year'] = (clone $query)->where('tahun', $currentYear)->count();
        $stats['ref-2-3-years'] = (clone $query)->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])->count();
        $stats['ref-4-5-years'] = (clone $query)->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])->count();
        $stats['ref-6-10-years'] = (clone $query)->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])->count();
        $stats['ref-older-10'] = (clone $query)->where('tahun', '<', $currentYear - 10)->count();

        return $stats;
    }

    /**
     * 4. Mengambil statistik Dosen
     */
    private function getStatsDosen($query): array
    {
        $stats['dosen-rps'] = (clone $query)->whereHas('dosen.rps')->count();
        $stats['dosen-non-rps'] = (clone $query)->whereDoesntHave('dosen.rps')->count();

        $stats['dosen-prodi'] = (clone $query)->whereHas('dosen.pr_rel', function ($q) {
            $q->where('prodis.id', auth()->user()->pr_id);
        })->count();

        $stats['dosen-all'] = (clone $query)->whereHas('dosen')->count();
        $stats['dosen-aktif'] = (clone $query)->whereHas('dosen', function ($q) {
            $q->where('status', 'aktif');
        })->count();
        $stats['dosen-non-aktif'] = (clone $query)->whereHas('dosen', function ($q) {
            $q->where('status', '!=', 'aktif');
        })->count();

        return $stats;
    }

    private function getStatsMahasiswa($query): array
    {
        $stats['mahasiswa-aktif'] = (clone $query)->whereHas('mahasiswa', function ($q) {
            $q->where('status', 'aktif');
        })->count();
        $stats['mahasiswa-non-aktif'] = (clone $query)->whereHas('mahasiswa', function ($q) {
            $q->where('status', '!=', 'aktif');
        })->count();

        return $stats;
    }
}
