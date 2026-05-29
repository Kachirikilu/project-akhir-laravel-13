<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 👈 Gunakan DB atau Model Anda

class RememberKelasNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        // ==========================================
        // SINKRONISASI JADWAL MANAGEMENT
        // ==========================================
        if ($routeName === 'jadwal-management') {
            $currentKode = $request->route('kode');
            $kelasHistory = session('kelas.history', []);

            // 1. Bersihkan histori dari kode-kode yang sudah tidak ada di database
            $activeKodes = DB::table('kelas') // ⚠️ Ganti 'kelas' dengan nama tabel kelas Anda
                ->whereIn('kode_kelas', array_keys($kelasHistory))
                ->pluck('kode_kelas')
                ->toArray();
                
            $kelasHistory = array_intersect_key($kelasHistory, array_flip($activeKodes));

            // 2. Cek apakah kode saat ini valid, jika valid baru masukkan histori
            $isValid = DB::table('kelas')->where('kode_kelas', $currentKode)->exists();

            if ($isValid) {
                unset($kelasHistory[$currentKode]);
                $kelasHistory[$currentKode] = [
                    'kode' => $currentKode,
                    'url' => route('jadwal-management', ['kode' => $currentKode]),
                ];

                // ambil 3 terbaru (di komen tertulis 2, tapi di code -3, sesuaikan kebutuhan)
                $kelasHistory = array_slice($kelasHistory, -3, null, true);

                uasort($kelasHistory, function ($a, $b) {
                    return strcmp($a['kode'], $b['kode']);
                });
            }

            session(['kelas.history' => $kelasHistory]);
        }

        // ==========================================
        // SINKRONISASI SESI MANAGEMENT
        // ==========================================
        if ($routeName === 'sesi-management') {
            $currentKode = $request->route('kode');
            $currentKodeJadwal = $request->route('kode_jadwal');
            $currentIdJadwal = $request->route('jadwal_id');

            $sesiHistory = session('jadwal.history', []);

            // 1. Bersihkan histori sesi dari jadwal_id yang sudah dihapus di DB
            $activeJadwalIds = DB::table('kelas_jadwals') // ⚠️ Ganti dengan nama tabel jadwal Anda
                ->whereIn('id', array_column($sesiHistory, 'jadwal_id'))
                ->pluck('id')
                ->toArray();

            $sesiHistory = array_filter($sesiHistory, function($item) use ($activeJadwalIds) {
                return in_array($item['jadwal_id'], $activeJadwalIds);
            });

            // 2. Cek apakah ID jadwal saat ini valid sebelum disimpan
            $isValidJadwal = DB::table('kelas_jadwals')->where('id', $currentIdJadwal)->exists();

            if ($isValidJadwal) {
                $compositeKey = $currentKode.'_'.$currentKodeJadwal;
                unset($sesiHistory[$compositeKey]);

                $sesiHistory[$compositeKey] = [
                    'kode' => $currentKode,
                    'kode_jadwal' => $currentKodeJadwal,
                    'jadwal_id' => $currentIdJadwal,
                    'switchTable' => $request->route('switchTable'),
                    'url' => route('sesi-management', [
                        'kode' => $currentKode,
                        'kode_jadwal' => $currentKodeJadwal,
                        'jadwal_id' => $currentIdJadwal,
                        'switchTable' => $request->route('switchTable'),
                    ]),
                ];

                $sesiHistory = array_slice($sesiHistory, -12, null, true);

                uasort($sesiHistory, function ($a, $b) {
                    $kodeCompare = strcmp($a['kode'], $b['kode']);
                    if ($kodeCompare !== 0) return $kodeCompare;
                    return strcmp($a['kode_jadwal'], $b['kode_jadwal']);
                });
            }

            session(['jadwal.history' => $sesiHistory]);
        }

        return $next($request);
    }
}