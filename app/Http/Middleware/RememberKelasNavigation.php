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

        // 1. HANDLER JADWAL (LEVEL 0 MANAGEMENT / LEVEL 0 MAHASISWA)
        if ($routeName === 'jadwal-management' || $routeName === 'jadwal-mahasiswa') {
            $currentKode = $request->route('kode');
            $currentKodeDb = str_replace('-', '', $currentKode);

            $kelasExists = Kelas::query()
                ->whereRaw("REPLACE(kode_kelas, '-', '') = ?", [$currentKodeDb])
                ->exists();

            if (! $kelasExists) {
                return $next($request);
            }

            // Tentukan key session berdasarkan scope user
            $sessionKey = ($routeName === 'jadwal-mahasiswa') ? 'kelas_mahasiswa.history' : 'kelas.history';
            $kelasHistory = session($sessionKey, []);
            
            unset($kelasHistory[$currentKode]);

            $kelasHistory[$currentKode] = [
                'kode' => $currentKode,
                'url' => route($routeName, ['kode' => $currentKode]),
            ];

            $kelasHistory = array_slice($kelasHistory, -3, null, true);
            uasort($kelasHistory, fn ($a, $b) => strcmp($a['kode'], $b['kode']));

            session([$sessionKey => $kelasHistory]);
        }

        // 2. HANDLER SESI (LEVEL 2 MANAGEMENT / LEVEL 1 MAHASISWA)
        if ($routeName === 'sesi-management' || $routeName === 'sesi-mahasiswa') {
            $currentKode = $request->route('kode');
            $currentKodeJadwal = $request->route('kode_jadwal_url');
            $currentIdJadwal = $request->route('jadwal_id');
            $currentKodeDb = str_replace('-', '', $currentKode);

            $jadwalExists = KelasJadwal::query()
                ->whereRelation('kelas_rel', 'kode_kelas', $currentKodeDb)
                ->whereRaw("
                    CONCAT(
                        label_kelas, '-', kode_wilayah, '-',
                        CASE
                            WHEN YEAR(tanggal_mulai) >= 3000 THEN YEAR(tanggal_mulai)
                            WHEN YEAR(tanggal_mulai) >= 2100 THEN RIGHT(YEAR(tanggal_mulai), 3)
                            WHEN YEAR(tanggal_mulai) >= 2000 THEN RIGHT(YEAR(tanggal_mulai), 2)
                            ELSE YEAR(tanggal_mulai)
                        END
                    ) = ?
                ", [$currentKodeJadwal])
                ->when($currentIdJadwal, fn ($q) => $q->where('id', $currentIdJadwal))
                ->exists();

            if (! $jadwalExists) {
                return $next($request);
            }

            // Tentukan key session berdasarkan scope user
            $sessionKey = ($routeName === 'sesi-mahasiswa') ? 'jadwal_mahasiswa.history' : 'jadwal.history';
            $sesiHistory = session($sessionKey, []);

            $compositeKey = $currentKode.'_'.$currentKodeJadwal;
            unset($sesiHistory[$compositeKey]);

            $sesiHistory[$compositeKey] = [
                'kode' => $currentKode,
                'kode_jadwal_url' => $currentKodeJadwal,
                'jadwal_id' => $currentIdJadwal,
                'switchTable' => $request->route('switchTable'),
                'url' => route($routeName, array_filter([
                    'kode' => $currentKode,
                    'kode_jadwal_url' => $currentKodeJadwal,
                    'jadwal_id' => $currentIdJadwal,
                    'switchTable' => $request->route('switchTable'),
                ])),
            ];

            $sesiHistory = array_slice($sesiHistory, -12, null, true);
            
            uasort($sesiHistory, function ($a, $b) {
                $kodeCompare = strcmp($a['kode'], $b['kode']);
                return ($kodeCompare !== 0) ? $kodeCompare : strcmp($a['kode_jadwal_url'], $b['kode_jadwal_url']);
            });

            session([$sessionKey => $sesiHistory]);
        }

        return $next($request);
    }
}