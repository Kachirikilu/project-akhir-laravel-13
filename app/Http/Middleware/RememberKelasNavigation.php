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
        if ($routeName === 'jadwal-management' || $routeName === 'jadwal-kelas') {
            $currentKode = $request->route('kode_kelas');
            $currentKodeDb = str_replace('-', '', $currentKode);

            $kelasExists = Kelas::query()
                ->whereRaw("REPLACE(kode_kelas, '-', '') = ?", [$currentKodeDb])
                ->exists();

            if (! $kelasExists) {
                return $next($request);
            }

            // Tentukan key session berdasarkan scope user
            $sessionKey = ($routeName === 'jadwal-kelas') ? 'kelas_mahasiswa.history' : 'kelas.history';
            $kelasHistory = session($sessionKey, []);
            
            unset($kelasHistory[$currentKode]);

            $kelasHistory[$currentKode] = [
                'kode_kelas' => $currentKode,
                'kode_kelas_url' => $currentKode,
                'url' => route($routeName, ['kode_kelas' => $currentKode]),
            ];

            $kelasHistory = array_slice($kelasHistory, -3, null, true);
            uasort($kelasHistory, fn ($a, $b) => strcmp($a['kode_kelas'], $b['kode_kelas']));

            session([$sessionKey => $kelasHistory]);
        }

        // 2. HANDLER SESI (LEVEL 2 MANAGEMENT / LEVEL 1 MAHASISWA)
        if ($routeName === 'sesi-management' || $routeName === 'sesi-jadwal-kelas') {
            $currentKode = $request->route('kode_kelas');
            $currentKodeJadwal = $request->route('kode_jadwal_short');
            $currentIdJadwal = $request->route('kj_id');
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
            $sessionKey = ($routeName === 'sesi-jadwal-kelas') ? 'jadwal_mahasiswa.history' : 'jadwal.history';
            $sesiHistory = session($sessionKey, []);

            $compositeKey = $currentKode.'-'.$currentKodeJadwal;
            unset($sesiHistory[$compositeKey]);

            $sesiHistory[$compositeKey] = [
                'kode_kelas' => $currentKode,
                'kode_kelas_url' => $currentKode,
                'kode_jadwal_short' => $currentKodeJadwal,
                'kode_jadwal_short_url' => $currentKodeJadwal,
                'kj_id' => $currentIdJadwal,
                'switchTable' => $request->route('switchTable'),
                'url' => route($routeName, array_filter([
                    'kode_kelas' => $currentKode,
                    'kode_jadwal_short' => $currentKodeJadwal,
                    'kj_id' => $currentIdJadwal,
                    'switchTable' => $request->route('switchTable'),
                ])),
            ];

            $sesiHistory = array_slice($sesiHistory, -12, null, true);
            
            uasort($sesiHistory, function ($a, $b) {
                $kodeCompare = strcmp($a['kode_kelas'], $b['kode_kelas']);
                return ($kodeCompare !== 0) ? $kodeCompare : strcmp($a['kode_jadwal_short'], $b['kode_jadwal_short']);
            });

            session([$sessionKey => $sesiHistory]);
        }

        return $next($request);
    }
}