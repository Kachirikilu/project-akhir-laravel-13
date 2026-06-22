<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Exports\MultiJadwalNilaiExport;
use App\Jobs\SendSesiExpiredNotification;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
        $job = (new SendSesiExpiredNotification($user, $noWA))->delay(now()->addMinutes(10));
        $jobId = app(Dispatcher::class)->dispatch($job);
        if (is_object($jobId)) {
            $jobId = uniqid('sync_');
        }
        Cache::put('wa_excel_'.$user->id, true, now()->addMinutes(10));
        Cache::put('wa_excel_job_'.$user->id, (string) $jobId, now()->addMinutes(10));

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

    private function processGetExcelNilai(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== PROSES GET EXCEL NILAI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        // 1. Validasi Pengguna & Hak Akses
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
                'message' => 'Mahasiswa tidak memiliki akses untuk mengambil File Excel Nilai!',
            ], 403);
        }

        // 2. Ekstraksi Parameter Kode dari Teks Pesan
        // Menghapus trigger kata pemicu untuk menyisakan kodenya saja
        $cleanMessage = strtoupper(trim($pesan));
        foreach ($this->excelGetNilaiKey as $trigger) {
            if (str_starts_with($cleanMessage, $trigger)) {
                $cleanMessage = trim(substr($cleanMessage, strlen($trigger)));
                break;
            }
        }

        if (empty($cleanMessage)) {
            return response()->json([
                'status' => false,
                'head' => '*⚠️ Parameter Kurang!*',
                'message' => "Format salah. Gunakan:\n`GET NILAI [KODE_KELAS/KODE_JADWAL]`",
            ], 400);
        }

        // 3. Normalisasi & Parsing Logika Kode Kelas / Kode Jadwal
        $kodeKelas = null;
        $kodeJadwal = null;

        // 1. Inisialisasi default agar tidak undefined
        $kodeKelas = trim($cleanMessage);
        $kodeJadwal = null;

        // 2. Normalisasi: Ubah ke huruf besar, buang spasi, strip (-), dan underscore (_)
        $targetCode = strtoupper(str_replace([' ', '-', '_'], '', $cleanMessage));

        // 3. ANALISIS STRUKTUR STRING YANG SUDAH BERSIH
        if (preg_match('/^([A-Z]{3})(\d{3})([A-Z])(IDL|PLG)(\d{2})$/', $targetCode, $matches)) {
            $kodeKelas = "{$matches[1]}-{$matches[2]}";
            $kodeJadwal = "{$matches[3]}-{$matches[4]}-{$matches[5]}";
        }

        // Skenario B: Jika hanya input Kode Kelas Panjang/Pendek (Contoh murni: ABF900)
        else {
            if (strlen($targetCode) === 6) {
                $kodeKelas = substr($targetCode, 0, 3).'-'.substr($targetCode, 3, 3);
            } else {
                // Jika format acak lainnya, biarkan ke fallback asli hasil pembersihan atau pesan awal
                $kodeKelas = $targetCode;
            }
        }

        // 4. Proses Query Database Menggunakan Method Bawaan Anda
        try {
            $this->getKelasAndJadwal($kodeKelas, $kodeJadwal);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Data Tidak Ditemukan!*',
                'message' => "Kode Kelas/Jadwal `{$cleanMessage}` tidak terdaftar di sistem.",
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal Memuat Data!*',
                'message' => $th->getMessage(),
            ], 400);
        }
        // 5. Eksekusi Export Dokumen & Penentuan Output File (SELALU EXCEL SINGLE)
        try {
            $nowStr = now()->format('Y-m-d');

            // 1. Pastikan folder storage/app/temp sudah dibuat
            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            // KONDISI A: Jika yang dicari adalah Kode Jadwal Spesifik
            if (! empty($this->jadwal)) {
                $fileName = $this->jadwal->kode.'_'.$this->jadwal->kode_rps.'_'.$this->jadwal->mk.'_'.$nowStr.'.xlsx';
                $fileNameSafe = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $fileName);
                $path = $tempDir.DIRECTORY_SEPARATOR.$fileNameSafe;

                // 🌟 TRIK TERBAIK: Simpan lewat download handler murni ke path temporary murni
                Excel::download(new NilaiExport($this->jadwal->id), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
                    ->getFile()
                    ->move($tempDir, $fileNameSafe);

                $base64Data = base64_encode(file_get_contents($path));
                @unlink($path); // Bersihkan file temporary

                return response()->json([
                    'status' => true,
                    'head' => '*✅ File Excel Berhasil Dibuat!*',
                    'message' => "Berikut berkas Excel nilai untuk Jadwal: *{$cleanMessage}*",
                    'file_type' => 'excel',
                    'file_name' => $fileNameSafe,
                    'file_base64' => $base64Data,
                ]);
            }

            // KONDISI B: Jika yang dicari adalah Kode Kelas
            if (! empty($this->kelas)) {
                $jadwals = $this->kelas->jadwals()->get();

                if ($jadwals->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'head' => '*⚠️ Jadwal Kosong!*',
                        'message' => "Kelas `{$kodeKelas}` tidak memiliki Jadwal Aktif!",
                    ], 442);
                }

                $fileName = $this->kelas->kode.'_'.$this->kelas->kode_rps.'_'.$this->kelas->mk.'_'.$nowStr.'.xlsx';
                $fileNameSafe = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $fileName);
                $path = $tempDir.DIRECTORY_SEPARATOR.$fileNameSafe;

                if ($jadwals->count() === 1) {
                    // 🌟 TRIK TERBAIK: Simpan lewat download handler untuk single sheet
                    Excel::download(new NilaiExport($jadwals->first()->id), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
                        ->getFile()
                        ->move($tempDir, $fileNameSafe);

                    $msgTeks = "Berikut berkas Excel nilai tunggal untuk Kelas *{$kodeKelas}*";
                } else {
                    // 🌟 TRIK TERBAIK: Simpan lewat download handler untuk multi sheet
                    Excel::download(new MultiJadwalNilaiExport($jadwals), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
                        ->getFile()
                        ->move($tempDir, $fileNameSafe);

                    $msgTeks = "Berikut berkas Excel (*Multi-Sheet*) nilai untuk semua jadwal di Kelas *{$kodeKelas}*";
                }

                $base64Data = base64_encode(file_get_contents($path));
                @unlink($path); // Bersihkan file temporary

                return response()->json([
                    'status' => true,
                    'head' => '*✅ File Excel Berhasil Dibuat!*',
                    'message' => $msgTeks,
                    'file_type' => 'excel',
                    'file_name' => $fileNameSafe,
                    'file_base64' => $base64Data,
                ]);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal Generate Berkas!*',
                'message' => 'Terjadi kendala internal: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getKelasAndJadwal(
        $kode = null,
        $kode_jadwal = null,
    ) {
        $this->kode = $kode;

        // 🌟 PERBAIKAN 1: Gunakan ->first() alih-alih firstOrFail() agar kita bisa handle manual jika kosong
        $this->kelas = Kelas::query()
            ->where('kode_kelas', $kode)
            ->orWhereRaw(
                "REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')",
                [$kode]
            )
            ->first();

        // Jika kelas tidak ditemukan, langsung lempar exception agar ditangkap catch-block di luar
        if (! $this->kelas) {
            throw new ModelNotFoundException("Kelas dengan kode {$kode} tidak ditemukan.");
        }

        $this->kode_jadwal = $kode_jadwal;

        // 🌟 PERBAIKAN 2: Jalankan pencarian jadwal HANYA JIKA $kode_jadwal diisi oleh user
        if (! empty($kode_jadwal)) {
            $parts = explode('-', $kode_jadwal);

            if (count($parts) < 3) {
                throw new \Exception('Format susunan komponen Kode Jadwal tidak valid (Harus mengandung Label-Wilayah-Tahun)!');
            }

            $labelKelas = $parts[0];
            $kodeWilayah = $parts[1];
            $tahunBlok = $parts[2];

            $this->jadwal = KelasJadwal::query()
                ->where('kelas_id', $this->kelas->id)
                ->where('label_kelas', $labelKelas)
                ->where('kode_wilayah', $kodeWilayah)
                ->whereRaw(
                    '
            CASE
                WHEN YEAR(tanggal_mulai) >= 3000
                    THEN YEAR(tanggal_mulai)

                WHEN YEAR(tanggal_mulai) >= 2100
                    THEN RIGHT(YEAR(tanggal_mulai), 3)

                WHEN YEAR(tanggal_mulai) >= 2000
                    THEN RIGHT(YEAR(tanggal_mulai), 2)

                ELSE YEAR(tanggal_mulai)
            END = ?
            ',
                    [$tahunBlok]
                )
                ->first();

            if (! $this->jadwal) {
                throw new ModelNotFoundException("Jadwal spesifik '{$kode_jadwal}' tidak ditemukan untuk Kelas ini.");
            }

            $this->jadwal_id = $this->jadwal->id;
        } else {
            // Kosongkan properti jadwal jika request murni pencarian tingkat kelas
            $this->jadwal = null;
            $this->jadwal_id = null;
        }
    }
}
