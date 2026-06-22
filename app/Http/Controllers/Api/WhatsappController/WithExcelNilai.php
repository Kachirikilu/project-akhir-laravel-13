<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Jobs\SendSesiExpiredNotification;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait WithExcelNilai
{
    use WithNilaiExcel;

    private function processGateAwayExcelNilai(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== PROSES GATEAWAY EXCEL NILAI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);
        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_IDENTITAS]`",
            ], 442);
        }

        if ($user->mahasiswa) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Akses Gagal!*',
                'message' => 'Mahasiswa tidak memiliki akses untuk menginput File Excel!',
            ], 403);
        }

        $oldJobId = Cache::get('wa_excel_job_'.$user->id);
        if ($oldJobId) {
            \DB::table('jobs')->where('id', $oldJobId)->delete();
        }
        $job = (new SendSesiExpiredNotification($user, $noWA))->delay(now()->addSeconds(10));
        $jobId = app(Dispatcher::class)->dispatch($job);
        if (is_object($jobId)) {
            $jobId = uniqid('sync_');
        }
        Cache::put('wa_excel_'.$user->id, true, now()->addSeconds(10));
        Cache::put('wa_excel_job_'.$user->id, (string) $jobId, now()->addSeconds(10));

        return response()->json([
            'status' => true,
            'head' => '*✅ Silahkan Upload File Excel Nilai!*',
            'message' => 'Mode Input Nilai `Aktif` selama *10 menit*. Silakan kirim ```File Excel``` lagi!',
        ]);
    }

    private function processExcelNilai(string $noWA, string $nameWA, string $pesan, $request)
    {
        Log::info('=== PROSES EXCEL NILAI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_IDENTITAS]`",
            ], 442);
        }
        if ($user->mahasiswa) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Akses Gagal!*',
                'message' => 'Mahasiswa tidak memiliki akses untuk menginput File Excel!',
            ], 403);
        }

        $allowUpload = Cache::get('wa_excel_'.$user->id);

        if (! $allowUpload) {
            return response()->json([
                'status' => false,
                'head' => '*⚠️ Menghentikan Proses File Excel!*',
                'message' => "Sebelum mengirim file Excel, silahkan ketik:\n`UPLOAD NILAI`\n\nAkses upload ```File Excel``` berlaku selama 10 menit.",
            ], 403);
        }

        $fileExcel = $request->file('excel_file');
        $result = $this->directImportFromWhatsApp($fileExcel, $user);

        return response()->json($result);
    }
}
