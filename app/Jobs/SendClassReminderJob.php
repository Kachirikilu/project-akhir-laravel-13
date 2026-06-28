<?php

namespace App\Jobs;

use App\Models\Auth\Mahasiswa;
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

        $url = config('services.nodejs.url');
        if (blank($url)) {
            throw new \Exception('URL Node.JS belum dikonfigurasi!');
        }
        $token = config('services.nodejs.token');
        if (blank($token)) {
            throw new \Exception('Token Node.JS belum dikonfigurasi!');
        }

        $jamMulai = $sesi->jam_mulai;
        $jamBerakhir = $sesi->jam_berakhir;
        $kodeKelas = $jadwal->kode_kelas ?? 'KLS-121104';
        $kodeJadwal = $jadwal->kode_jadwal ?? 'A Indralaya';
        $labelJadwal = $jadwal->label_extra ?? 'A-IDL-26';
        $pertemuan = $sesi->pertemuan_ke ?? '1';

        $kelas = $jadwal->kelas_rel;
        $prId = $kelas->pr_id;
        $rps = $kelas->rps_rel;
        $kodeRps = $rps->kode;

        $timDosen = $rps->tim_dosens; // ambil tim doseen berdasarkan $prId

        $mk = $rps->mk_rel;
        $namaMk = $mk->nama_mk ?? 'Rangkaian Listrik';
        $sks = $mk->sks_kuliah ?? 2;
        $sksText = $mk->sks_text ?? 'Tatap Muka';

        $kodeScpmk = $sesi->kode_scpmk;
        $metode = $sesi->metode ?? 'Teori';
        $bobot = $sesi->bobot_normalisasi ?? 0;

        $appUrl = env('APP_URL');
        $linkKelas = "$appUrl/kelas-management/kelas/{$kodeKelas}/jadwal/{$kodeJadwal}/sesi";

        $berhasil = 0;
        $gagal = 0;

        $dosenDiProdi = $rps->tim_dosens->filter(function ($timDosen) use ($prId) {
            return $timDosen->pr_id == $prId;
        });

        foreach ($dosenDiProdi as $tim) {

            foreach ($tim->dosens as $dosen) {

                if (! isset($dosen->is_wa_active) || ! $dosen->is_wa_active || blank($dosen->no_wa)) {
                    continue;
                }

                try {
                    $namaDosen = trim($dosen->name ?? 'Dosen');
                    $gender = $dosen->jenis_kelamin ?? '';
                    $sapaan = ($gender === 'Laki-laki') ? 'Bapak' : (($gender === 'Perempuan') ? 'Ibu' : 'Bapak/Ibu');

                    $pesanDosen = "Halo {$sapaan} _{$namaDosen}_, pengingat Kelas untuk hari ini.\n\n";
                    $pesanDosen .= "Pukul *{$jamMulai} WIB* akan dilaksanakan Pertemuan ke-{$pertemuan} dari Kelas:\n";
                    $pesanDosen = $this->formatNotifTeks($pesanDosen, $kodeKelas, $labelJadwal, $kodeRps, $namaMk, $sks, $sksText, $kodeScpmk, $metode, $bobot, $linkKelas);

                    $responseDosen = Http::withHeaders([
                        'Authorization' => $token,
                        'Bypass-Tunnel-Reminder' => 'true',
                    ])
                        ->timeout(30)
                        ->asForm()
                        ->post($url, [
                            'whatsapp_number' => $dosen->no_wa,
                            'whatsapp_message' => $pesanDosen,
                        ]);

                    if ($responseDosen->successful()) {
                        Log::info("Reminder Dosen Terkirim: {$dosen->name}");
                    }

                    usleep(500000);

                } catch (\Throwable $e) {
                    Log::error("Gagal Kirim Reminder Dosen: {$dosen->id}", ['message' => $e->getMessage()]);
                }
            }
        }

        foreach ($jadwal->mahasiswas->unique('id')->values() as $mahasiswa) {

            try {
                $noHp = $mahasiswa->no_wa ?? null;
                $waAktif = $mahasiswa->is_wa_active ?? null;
                $waToken = $mahasiswa->wa_limit ?? 0;

                if (blank($noHp) || ! $waAktif || $waToken < 1) {
                    continue;
                }

                $nama = trim($mahasiswa->name ?? '');
                if ($nama === '') {
                    $nama = 'Mahasiswa';
                }

                Log::info('Mahasiswa Queue', [
                    'id' => $mahasiswa->id,
                    'name' => $mahasiswa->name,
                    'whatsapp' => $mahasiswa->no_wa,
                ]);

                $pesan = "Halo _{$nama}_, hari ini ada Kelas!\n\n";
                $pesan .= "Pukul *{$jamMulai} WIB* akan dilaksanakan Pertemuan ke-{$pertemuan} dari Kelas:\n";
                $pesan = $this->formatNotifTeks($pesan, $kodeKelas, $labelJadwal, $kodeRps, $namaMk, $sks, $sksText, $kodeScpmk, $metode, $bobot, $linkKelas);

                $response = Http::withHeaders([
                    'Authorization' => $token,
                    'Bypass-Tunnel-Reminder' => 'true',
                ])
                    ->timeout(30)
                    ->asForm()
                    ->post($url, [
                        'whatsapp_number' => $noHp,
                        'whatsapp_message' => $pesan,
                        // 'delay' => 5,
                        // 'typing' => false,
                    ]);

                // $response = Http::withHeaders([
                //     'Authorization'           => $token,
                //     'Bypass-Tunnel-Reminder'  => 'true',
                // ])->post($url, [
                //     'no_wa'  => $noHp,
                //     'whatsapp_message' => $pesanCustom,
                // ]);

                Mahasiswa::where('id', $mahasiswa->id)
                    ->where('wa_limit', '>', 0)
                    ->decrement('wa_limit', 1, [
                        'is_wa_active' => true,
                    ]);

                if (! $response->successful()) {
                    Log::error('Node.JS HTTP Error!', [
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
                    Log::error('Node.JS Reject!', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'nomor' => $noHp,
                        'response' => $result,
                    ]);

                    $gagal++;

                    continue;
                }

                Log::info('Reminder Terkirim!', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'nomor' => $noHp,
                ]);

                $berhasil++;

                usleep(500000);

            } catch (\Throwable $e) {

                Log::error('Gagal Kirim Reminder!', [
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

    public function formatNotifTeks($pesan, $kodeKelas, $labelJadwal, $kodeRps, $namaMk, $sks, $sksText, $kodeScpmk, $metode, $bobot, $linkKelas) {
        $pesan .= "- `{$kodeKelas} {$labelJadwal}`\n\n";
        
        $pesan .= "Informasi Kelas:\n";
        $pesan .= "- RPS: ```{$kodeRps}```\n";
        $pesan .= "- {$namaMk}\n";
        $pesan .= "- {$sks} SKS – *{$sksText}*\n";
        $pesan .= "- ```{$kodeScpmk}```\n";
        $pesan .= "- Metode: *{$metode}*\n";
        $pesan .= "- Bobot: {$bobot}%\n\n";

        $pesan .= "Link Kelas:\n";
        $pesan .= $linkKelas;

        return $pesan;
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
