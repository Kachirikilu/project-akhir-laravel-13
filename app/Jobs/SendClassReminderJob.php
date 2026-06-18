<?php

namespace App\Jobs;

use App\Models\Kelas\KelasSesi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendClassReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $sesiId;

    public $tries = 3;
    public $timeout = 300;

    public function __construct(KelasSesi $sesi)
    {
        $this->sesiId = $sesi->id;
    }

    public function handle(): void
    {
        Log::info('SendClassReminderJob START', [
            'sesi_id' => $this->sesiId,
        ]);

        $sesi = KelasSesi::with([
            'jadwal_rel.mahasiswas',
            'override',
        ])->find($this->sesiId);

        if (! $sesi) {
            Log::warning('Sesi tidak ditemukan', [
                'sesi_id' => $this->sesiId,
            ]);

            return;
        }

        $jadwal = $sesi->jadwal_rel;

        if (! $jadwal) {
            Log::warning('Jadwal tidak ditemukan', [
                'sesi_id' => $this->sesiId,
            ]);

            return;
        }

        if (! $jadwal->mahasiswas || $jadwal->mahasiswas->isEmpty()) {
            Log::info('Tidak ada mahasiswa pada jadwal', [
                'sesi_id' => $this->sesiId,
            ]);

            return;
        }

        $token = config('services.fonnte.token');

        if (blank($token)) {
            throw new \Exception('Token Fonnte belum dikonfigurasi.');
        }

        $jamMulai = $sesi->override?->jam_mulai ?? $jadwal->jam_mulai;
        $namaKelas = $jadwal->label_kelas ?? 'Kelas';
        $pertemuan = $sesi->pertemuan_ke ?? '-';

        $berhasil = 0;
        $gagal = 0;

        foreach ($jadwal->mahasiswas as $mahasiswa) {

            try {

                $noHp = $mahasiswa->whatsapp_number ?? null;

                if (blank($noHp)) {
                    continue;
                }

                $nama = trim($mahasiswa->name ?? '');

                if ($nama === '') {
                    $nama = 'Mahasiswa';
                }

                Log::info('Mahasiswa Queue', [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->name,
                    'whatsapp' => $mahasiswa->whatsapp_number,
                ]);

                $pesan = "Halo {$nama}, pengingat untuk kelas *{$namaKelas}* (Pertemuan ke-{$pertemuan}) akan dimulai hari ini pukul *{$jamMulai}*. Jangan lupa hadir tepat waktu ya!";

                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])
                    ->timeout(30)
                    ->asForm()
                    ->post('https://api.fonnte.com/send', [
                        'target' => $noHp,
                        'message' => $pesan,
                        'delay' => 5,
                        'typing' => false,
                    ]);

                if (! $response->successful()) {

                    Log::error('Fonnte HTTP Error', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'nomor' => $noHp,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);

                    $gagal++;
                    continue;
                }

                $result = $response->json();

                if (
                    isset($result['status']) &&
                    $result['status'] === false
                ) {
                    Log::error('Fonnte Reject', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'nomor' => $noHp,
                        'response' => $result,
                    ]);

                    $gagal++;
                    continue;
                }

                Log::info('Reminder terkirim', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'nomor' => $noHp,
                ]);

                $berhasil++;

                usleep(500000);

            } catch (\Throwable $e) {

                Log::error('Gagal kirim reminder', [
                    'mahasiswa_id' => $mahasiswa->id ?? null,
                    'message' => $e->getMessage(),
                ]);

                $gagal++;
            }
        }

        Log::info('SendClassReminderJob FINISH', [
            'sesi_id' => $this->sesiId,
            'berhasil' => $berhasil,
            'gagal' => $gagal,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendClassReminderJob FAILED', [
            'sesi_id' => $this->sesiId,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}