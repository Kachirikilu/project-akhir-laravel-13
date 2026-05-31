<?php

namespace App\Http\Middleware;

use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Closure;
use Illuminate\Http\Request;

class RememberKelasNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if ($routeName === 'jadwal-management') {
            $currentKode = $request->route('kode');
            $currentKodeDb = str_replace('-', '', $currentKode);

            $kelasExists = Kelas::query()
                ->whereRaw(
                    "REPLACE(kode_kelas, '-', '') = ?",
                    [$currentKodeDb]
                )
                ->exists();

            if (! $kelasExists) {
                return $next($request);
            }

            $kelasHistory = session('kelas.history', []);

            unset($kelasHistory[$currentKode]);

            $kelasHistory[$currentKode] = [
                'kode' => $currentKode,
                'url' => route('jadwal-management', [
                    'kode' => $currentKode,
                ]),
            ];

            $kelasHistory = array_slice(
                $kelasHistory,
                -3,
                null,
                true
            );

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
            $currentKodeDb = str_replace('-', '', $currentKode);

            $jadwalExists = KelasJadwal::query()
                ->whereRelation(
                    'kelas_rel',
                    'kode_kelas',
                    $currentKodeDb
                )
                ->whereRaw(
                    "
                    CONCAT(
                        label_kelas,
                        '-',
                        kode_wilayah,
                        '-',
                        CASE
                            WHEN YEAR(tanggal_mulai) >= 3000
                                THEN YEAR(tanggal_mulai)

                            WHEN YEAR(tanggal_mulai) >= 2100
                                THEN RIGHT(YEAR(tanggal_mulai), 3)

                            WHEN YEAR(tanggal_mulai) >= 2000
                                THEN RIGHT(YEAR(tanggal_mulai), 2)

                            ELSE YEAR(tanggal_mulai)
                        END
                    ) = ?
                    ",
                    [$currentKodeJadwal]
                )
                ->when(
                    $currentIdJadwal,
                    fn ($q) => $q->where('id', $currentIdJadwal)
                )
                ->exists();

            if (! $jadwalExists) {
                return $next($request);
            }

            $compositeKey =
                $currentKode.'_'.$currentKodeJadwal;

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

            // max 12 history
            $sesiHistory = array_slice(
                $sesiHistory,
                -12,
                null,
                true
            );

            // urut
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
