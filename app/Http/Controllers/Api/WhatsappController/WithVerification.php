<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Log;

trait WithVerification
{
    private function processVerification(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== PROSES VERIFIKASI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $pecahPesan = explode(' ', $pesan);
        $identifier = $pecahPesan[1] ?? null;

        if (! $identifier) {
            return response()->json([
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Format salah! Silahkan menggunakan: \n`LOGIN [ID_IDENTITAS]`"],
                400);
        }

        $user = User::where(function ($mainQuery) use ($identifier) {
            $mainQuery->where('email', $identifier)
                ->orWhereHas('mahasiswa', function ($query) use ($identifier) {
                    $query->where('nim', $identifier);
                })
                ->orWhereHas('dosen', function ($query) use ($identifier) {
                    $query->where('nip', $identifier)
                        ->orWhere('nidn', $identifier)
                        ->orWhere('nidk', $identifier);
                })
                ->orWhereHas('admin', function ($query) use ($identifier) {
                    $query->where('nip', $identifier)
                        ->orWhere('nitk', $identifier);
                });
        })->first();

        $profile = null;

        if ($user) {
            $roleClean = strtolower($user->role);
            if ($roleClean === 'mahasiswa' && $user->mahasiswa) {
                $profile = $user->mahasiswa;
            } elseif ($roleClean === 'dosen' && $user->dosen) {
                $profile = $user->dosen;
            } elseif ($roleClean === 'admin' && $user->admin) {
                $profile = $user->admin;
            }
        }

        Log::info('=== DATA USER / PENGGUNA ===', $user ? $user->toArray() : ['status' => 'User Tidak Ditemukan!']);

        if (! $user || ! $profile) {
            return response()->json([
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => '`[ID_IDENTITAS]` tidak ditemukan di sistem Data Akademik!'],
                444);
        }

        if ($profile->is_wa_active && $profile->no_hp !== $noWA) {
            return response()->json([
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "`{$user->label_id1}` ini sudah terverifikasi dengan akun WhatsApp lain!"],
                400);
        }
        if (! $profile->is_wa_active && ! $this->isSuffixMatched($noWA, $profile->no_hp)) {
            return response()->json([
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Gagal Verifikasi! Nomor WhatsApp Anda tidak cocok dengan Nomor HP yang terdaftar pada `{$user->label_id1}` ini!"],
                403);
        }

        Log::info("Membersihkan session aktif lain untuk nomor: {$noWA} sebelum mengaktifkan akun baru.");

        Mahasiswa::where('no_hp', $noWA)
            ->when($roleClean === 'mahasiswa', fn ($q) => $q->where('id', '!=', $profile->id))
            ->update(['is_wa_active' => false, 'wa_limit' => 0]);
        Dosen::where('no_hp', $noWA)
            ->when($roleClean === 'dosen', fn ($q) => $q->where('id', '!=', $profile->id))
            ->update(['is_wa_active' => false, 'wa_limit' => 0]);
        Admin::where('no_hp', $noWA)
            ->when($roleClean === 'admin', fn ($q) => $q->where('id', '!=', $profile->id))
            ->update(['is_wa_active' => false, 'wa_limit' => 0]);

        $limitValue = in_array($roleClean, ['admin', 'dosen']) ? 100 : 50;
        $profile->update([
            'is_wa_active' => true,
            'wa_limit' => $limitValue,
        ]);

        $profile->refresh();

        Log::info('WhatsApp '.ucfirst($user->role)." Berhasil Diverifikasi. ID: {$identifier}, No: {$noWA}");

        return response()->json([
            'status' => true,
            'head' => '*✅ Verifikasi Sukses!*',
            'message' => "Halo *_{$nameWA}_*, verifikasi kamu berhasil disinkronkan! Anda login sebagai ```{$user->role}```.".
                        "\n\n- Nama: {$user->name}".
                        "\n- {$user->label_id1}: {$user->identity1}".
                        "\n- WA: {$user->no_wa_full}".
                        ($user->mahasiswa ? "\n- Token: {$user->mahasiswa->wa_limit}" : ''),
        ]);
    }

    private function isNumberAlreadyUsedGlobally(string $noWA): bool
    {
        return Mahasiswa::where('no_hp', $noWA)->where('is_wa_active', true)->exists() ||
               Dosen::where('no_hp', $noWA)->where('is_wa_active', true)->exists() ||
               Admin::where('no_hp', $noWA)->where('is_wa_active', true)->exists();
    }

    private function isSuffixMatched(string $noWAInput, ?string $noHPDb): bool
    {
        if (! $noHPDb) {
            return false;
        }
        $noHpDbBersih = preg_replace('/[^0-9]/', '', $noHPDb);
        $sufiksInput = substr($noWAInput, -9);
        $sufiksDb = substr($noHpDbBersih, -9);

        return $sufiksInput === $sufiksDb;
    }
}
