<?php

namespace App\Http\Middleware;

use App\Models\Kelas\Kelas;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanAccessKelas
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if ($user->admin) {
            return $next($request);
        }

        $kodeKelas = $request->route('kode');
        $kodeJadwal = $request->route('kode_jadwal');

        $kelas = Kelas::query()
            ->where('kode_kelas', $kodeKelas)
            ->first();

        if (! $kelas) {
            abort(404);
        }

        // ==========================
        // DOSEN
        // ==========================
        if ($user->dosen) {

            $hasAccess =
                $kelas->rps_rel()
                    ->whereHas('dosens', function ($q) use ($user) {
                        $q->where('dosens.id', $user->dosen->id);
                    })
                    ->exists()

                ||

                $kelas->jadwals()
                    ->whereHas('sesis.dosens', function ($q) use ($user) {
                        $q->where('dosens.id', $user->dosen->id);
                    })
                    ->exists();

            abort_unless($hasAccess, 403);

            return $next($request);
        }

        // ==========================
        // MAHASISWA
        // ==========================
        if ($user->mahasiswa) {

            $kelas = Kelas::where('kode_kelas', $kodeKelas)->firstOrFail();

            $jadwal = $kelas->jadwals()
                ->whereHas('mahasiswas', function ($q) use ($user) {
                    $q->where('mahasiswas.id', $user->mahasiswa->id);
                })
                ->when($kodeJadwal, function ($q) use ($kodeJadwal) {
                    $parts = explode('-', $kodeJadwal);

                    if (count($parts) >= 2) {
                        $q->where('label_kelas', $parts[0])
                            ->where('kode_wilayah', $parts[1]);
                    }
                })
                ->exists();

            abort_unless($jadwal, 403);

            return $next($request);
        }

        abort(403);
    }
}
