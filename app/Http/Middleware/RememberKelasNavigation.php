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
            // Ambil kode kelas/jadwal yang sebelumnya tersimpan di session
            $lastSavedKode = session('kelas.last.kode');

            // 🚀 KUNCI PERBAIKAN: Hanya hapus sesi jika user membuka jadwal/kelas yang BERBEDA
            if ($lastSavedKode && $lastSavedKode !== $currentKode) {
                session()->forget('kelas.last_sesi');
            }

            session([
                'kelas.last' => [
                    'kode' => $currentKode,
                    'url' => route('jadwal-management', [
                        'kode' => $currentKode,
                    ]),
                ],
            ]);
        }

        if ($routeName === 'sesi-management') {
            session([
                'kelas.last_sesi' => [
                    'kode' => $request->route('kode'),
                    'kode_jadwal' => $request->route('kode_jadwal'),
                    'id_jadwal' => $request->route('id_jadwal'),
                    'switchTable' => $request->route('switchTable'),
                    'url' => route('sesi-management', [
                        'kode' => $request->route('kode'),
                        'kode_jadwal' => $request->route('kode_jadwal'),
                        'id_jadwal' => $request->route('id_jadwal'),
                        'switchTable' => $request->route('switchTable'),
                    ]),
                ],
            ]);
        }

        return $next($request);
    }
}