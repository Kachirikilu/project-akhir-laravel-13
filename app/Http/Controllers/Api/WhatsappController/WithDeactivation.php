<?php

namespace App\Http\Controllers\Api\WhatsappController;

use Illuminate\Support\Facades\Log;

trait WithDeactivation
{
    private function processDeactivateToken(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== PROSES DEAKTIVASI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
            ], 442);
        }

        $profile = null;
        $roleClean = strtolower($user->role);

        if ($roleClean === 'mahasiswa') {
            $profile = $user->mahasiswa;
        } elseif ($roleClean === 'dosen') {
            $profile = $user->dosen;
        } elseif ($roleClean === 'admin') {
            $profile = $user->admin;
        }

        if ($profile) {
            $profile->update([
                'is_wa_active' => false,
                'wa_limit' => 0,
            ]);

            Log::warning('WhatsApp Akun '.strtoupper($roleClean)." dengan nomor {$noWA} telah DINONAKTIFKAN secara mandiri!");

            return response()->json([
                'status' => true,
                'head' => '*✅ Penonaktifan Sukses!*',
                'message' => "Halo *_{$nameWA}_*, nomor {$noWA} telah ```DINONAKTIFKAN``` secara mandiri! Untuk mengaktifkannya kembali, silakan kirim ulang pesan format *VERIFIKASI [{$user->label_id1}]*.".
                            "\n\n- Nama: {$user->name}".
                            "\n- Role: {$user->role}".
                            "\n- $user->label_id1: {$user->identity1}".
                            "\n- WA: {$user->no_wa_full}"
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Gagal memproses penonaktifan.'], 500);
    }
}
