<?php

namespace App\Http\Controllers\Api\WhatsappController;

use Illuminate\Support\Facades\Log;
use App\Models\Auth\Mahasiswa;

trait WithDataDiri
{
    private function processDataDiri(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== SEDANG MENCARI TOKEN ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if ($user) {
            return response()->json([
                'status' => true,
                'head' => '*👤 Informasi Data Diri*',
                'message' => "- Nama: {$user->name}".
                            "\n- Role: {$user->role}".
                            "\n- {$user->label_id1}: {$user->identity1}".
                            "\n- WA: {$user->no_wa_full}".
                            ($user->mahasiswa ? "\n- Token: {$user->mahasiswa->wa_limit}" : ''),
            ]);
        }

        $userNonAktif = $this->searchUserWhatsApp($sufiksNomor, false);

        if ($userNonAktif) {
            return response()->json([
                'status' => false,
                'message' => "Nomor Anda [{$noWA}] sudah terdaftar di sistem, tetapi *AKUN BELUM AKTIF*. Silakan ketik *VERIFIKASI [ID Akademik]* terlebih dahulu untuk mengaktifkan fitur bot!",
            ], 403);
        }

        return response()->json([
            'status' => false,
            'message' => "Nomor WhatsApp [{$noWA}] *BELUM TERDAFTAR* pada akun mana pun di Sistem Akademik! Silakan hubungi admin untuk mendaftarkan nomor HP Anda!",
        ], 404);
    }

    private function processResetToken(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== PROSES RESET TOKEN ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal!*',
                'message' => "Gagal memperbarui token! Nomor WhatsApp [{$noWA}] belum aktif atau belum terdaftar di sistem!",
            ], 404);
        }

        $head = '*👤 Informasi Data Diri*';
        if ($user->mahasiswa) {
        Mahasiswa::where('id', $user->mahasiswa->id)
                ->update([
                    'is_wa_active' => true,
                    'wa_limit' => 50,
                ]);

            $head = '*✅ Update Token Berhasil!*';
        }
        return response()->json([
            'status' => true,
            'head' => $head,
            'message' => "- Nama: {$user->name}".
                        "\n- Role: {$user->role}".
                        "\n- {$user->label_id1}: {$user->identity1}".
                        "\n- WA: {$user->no_wa_full}".
                        ($user->mahasiswa ? "\n- Token: {$user->mahasiswa->wa_limit}" : ''),
        ]);

        return response()->json(['status' => false, 'head' => '*❌ Gagal!*', 'message' => 'Gagal memproses permintaan!'], 500);
    }
}
