<?php

namespace App\Jobs;

use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSesiExpiredNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $noWA;

    public function __construct(User $user, $noWA)
    {
        $this->user = $user;
        $this->noWA = $noWA;
    }

    public function handle()
    {
        $urlBase = config('services.nodejs.url');
        $token = config('services.nodejs.token');

        if (blank($urlBase) || blank($token)) {
            Log::error('❌ [Job] URL atau Token Node.JS belum dikonfigurasi!');

            return;
        }

        $url = rtrim($urlBase, '/');
        if (! str_contains($url, '/wa-api')) {
            $url = $url.'/wa-api';
        }

        Log::info("🚀 [Job] Mengirim notifikasi expired ke: {$url}");
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Bypass-Tunnel-Reminder' => 'true',
            ])
                ->timeout(10)
                ->post($url, [
                    'whatsapp_number' => $this->noWA,
                    'whatsapp_message' => "🔒 *Sesi Input Nilai Ditutup*\n\nBatas waktu pengunggahan nilai *10 menit* telah habis. Sesi input otomatis Anda telah dinonaktifkan demi keamanan!",
                ]);

            if ($response->successful()) {
                Log::info('✅ [Job] Notifikasi expired sukses terkirim ke Node.js.');
                Cache::forget('wa_excel_job_'.$this->user->id);
            } else {
                Log::error('❌ [Job] Node.js menolak request. Status: '.$response->status().' | Response: '.$response->body());
            }

        } catch (\Throwable $e) {
            Log::error('❌ [Job] Gagal menghubungi API Node.js: '.$e->getMessage());
        }
    }
}
