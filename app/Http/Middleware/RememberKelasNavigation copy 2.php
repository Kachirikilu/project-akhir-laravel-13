<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RememberKelasNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if ($routeName === 'jadwal-management') {

            $currentKode = $request->route('kode');

            $kelasHistory = session('kelas.history', []);

            unset($kelasHistory[$currentKode]);

            $kelasHistory[$currentKode] = [
                'kode' => $currentKode,
                'url' => route('jadwal-management', [
                    'kode' => $currentKode,
                ]),
            ];

            // ambil 2 TERBARU
            $kelasHistory = array_slice($kelasHistory, -3, null, true);

            // urut berdasarkan kode
            uasort($kelasHistory, function ($a, $b) {
                return strcmp($a['kode'], $b['kode']);
            });

            session([
                'kelas.history' => $kelasHistory,
            ]);
        }

        if ($routeName === 'sesi-management') {

            $currentKode = $request->route('kode');
            $currentKodeJadwal = $request->route('kode_jadwal');
            $currentIdJadwal = $request->route('jadwal_id');

            $sesiHistory = session('jadwal.history', []);

            $compositeKey =
                $currentKode.'_'.$currentKodeJadwal;

            // kalau sudah ada, refresh posisi
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

            // urut berdasarkan kode lalu kode_jadwal
            uasort($sesiHistory, function ($a, $b) {

                $kodeCompare =
                    strcmp($a['kode'], $b['kode']);

                if ($kodeCompare !== 0) {
                    return $kodeCompare;
                }

                return strcmp(
                    $a['kode_jadwal'],
                    $b['kode_jadwal']
                );
            });

            session([
                'jadwal.history' => $sesiHistory,
            ]);
        }

        return $next($request);
    }
}
