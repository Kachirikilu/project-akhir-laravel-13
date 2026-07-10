<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Exports\MultiNilaiExport;
use App\Exports\NilaiExport;
use App\Jobs\SendSesiExpiredNotification;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithCpmkGrafikShow;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasNilaiAbsensi;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

trait WithExcelCapaianNilai
{
    use HasGetByKode;
    use HasNilaiAbsensi;
    use WithCpmkGrafikShow;
    use WithNilaiExcel;
    use WithUserFilters;

    public $selectedPrId;

    public $selectedDpId;

    public $selectedFkId;

    public $switchTable;

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
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
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
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
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
                'message' => "Sebelum mengirim file Excel, silahkan ketik:\n`UPLOAD NILAI`\n\nAkses upload ```File Excel``` berlaku selama 10 menit!",
            ], 403);
        }

        $fileExcel = $request->file('excel_file');
        $result = $this->directImportFromWhatsApp($fileExcel, $user);

        return response()->json($result);
    }

    private function processGetExcelNilai(string $noWA, string $nameWA, string $pesan, array $excelGetNilaiKey)
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
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
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
        foreach ($excelGetNilaiKey as $trigger) {
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

        [$kodeKelas, $kodeJadwal] = $this->parseKodeJadwal($cleanMessage);

        try {
            $this->getKelasAndJadwalByKode($kodeKelas, $kodeJadwal);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Data Tidak Ditemukan!*',
                'message' => "Kode Kelas/Jadwal `{$cleanMessage}` tidak terdaftar di sistem!",
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

                Excel::download(new NilaiExport($this->jadwal, $this->jadwal->kode), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
                    ->getFile()
                    ->move($tempDir, $fileNameSafe);

                $base64Data = base64_encode(file_get_contents($path));
                @unlink($path);

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
                    Excel::download(new NilaiExport($jadwals->first()->id), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
                        ->getFile()
                        ->move($tempDir, $fileNameSafe);

                    $msgTeks = "Berikut berkas Excel nilai tunggal untuk Kelas *{$kodeKelas}*";
                } else {
                    Excel::download(new MultiNilaiExport($jadwals), $fileNameSafe, \Maatwebsite\Excel\Excel::XLSX)
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

    private function processGetCapaianNilai(string $noWA, string $nameWA, string $pesan, array $pdfGetCapaianKey)
    {
        Log::info('=== PROSES GET CAPAIAN NILAI ===');
        Log::info("Nomor dari Node.JS: {$noWA} | Pemicu: {$pesan}");

        // 1. Validasi Pengguna & Hak Akses
        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);
        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
            ], 442);
        }

        if ($user->mahasiswa) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Akses Gagal!*',
                'message' => 'Mahasiswa tidak memiliki akses untuk mengambil File Capaian!',
            ], 403);
        }

        // 2. Ekstraksi Parameter Kode dari Teks Pesan
        // Menghapus trigger kata pemicu untuk menyisakan kodenya saja
        $cleanMessage = strtoupper(trim($pesan));
        $isRpsRequest = false;
        $triggerRps = ['RPS', 'RENCANA PEMBELAJARAN SEMESTER'];

        foreach ($triggerRps as $trig) {
            if (str_contains($cleanMessage, $trig)) {
                $isRpsRequest = true;
                break;
            }
        }

        if ($isRpsRequest) {
            $parts = explode(' ', $cleanMessage);
            $kodeRps = null;
            $identifier = null;
            $angkatan = null;

            foreach ($parts as $p) {
                $pUpper = strtoupper($p);
                $clean = preg_replace('/[^A-Z0-9]/', '', $pUpper);

                if (preg_match('/^\d{6}[A-Z]{3,}\d+$/', $clean)) {
                    $kodeRps = $p;
                } elseif (is_numeric($p) && strlen($p) === 4) {
                    $angkatan = $p;
                } elseif (! preg_match('/^(S1|S2|S3|SARJANA|MAGISTER|DOKTOR)$/', $pUpper)) {
                    $identifier = $p;
                }
            }
            $rps = $this->getRPSByKode($kodeRps);
            if (! $rps) {
                return response()->json([
                    'status' => false,
                    'head' => '*❌ Data Tidak Ditemukan!*',
                    'message' => "Kode RPS `{$kodeRps}` tidak terdaftar di sistem!",
                ], 404);
            }
            $prodi = $this->getProdiByKode($identifier);
            $departemen = ! $prodi ? $this->getDepartemenByKode($identifier) : null;
            $fakultas = (! $prodi && ! $departemen) ? $this->getFakultasByKode($identifier) : null;

            return $this->resolveRpskGrafikPdf($rps, $prodi, $departemen, $fakultas, $angkatan);
        }

        foreach ($pdfGetCapaianKey as $trigger) {
            if (str_starts_with($cleanMessage, $trigger)) {
                $cleanMessage = trim(substr($cleanMessage, strlen($trigger)));
                break;
            }
        }

        if (empty($cleanMessage)) {
            return response()->json([
                'status' => false,
                'head' => '*⚠️ Parameter Kurang!*',
                'message' => "Format salah. Gunakan:\n`GET NILAI [KODE_KELAS]`",
            ], 400);
        }

        [$kodeKelas, $kodeJadwal] = $this->parseKodeJadwal($cleanMessage);

        try {
            $this->getKelasAndJadwalByKode($kodeKelas, $kodeJadwal);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Data Tidak Ditemukan!*',
                'message' => "Kode Jadwal `{$cleanMessage}` tidak terdaftar di sistem!",
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
            if ($this->jadwal) {
                $data = $this->resolveCpmkGrafikPdf($this->jadwal->id);

                $pesanFilter = "\n- ```{$this->jadwal->kelas_rel->pr_rel->prodi}```";
                $pesanFilter .= "\n- RPS: *{$this->jadwal->kelas_rel->rps_rel->kode}*";
                $pesanFilter .= "\n- MK: {$this->jadwal->kelas_rel->rps_rel->mk_rel->mk}";
                $pesanFilter .= "\n- {$this->jadwal->kelas_rel->rps_rel->mk_rel->sks_text} - `{$this->jadwal->kelas_rel->rps_rel->mk_rel->sks} SKS`";

                return response()->json([
                    'status' => true,
                    'message' => "Berkas grafik CPMK Jadwal *{$this->jadwal->kode}* berhasil dibuat: {$pesanFilter}",
                    'file_base64' => base64_encode($data['content']),
                    'file_name' => $data['name'],
                    'file_type' => 'pdf',
                ]);
            } elseif ($this->kelas) {
                $allData = $this->resolveAllCpmkGrafikPdf($this->kelas->id);

                $pesanFilter = "\n- ```{$this->kelas->pr_rel->prodi}```";
                $pesanFilter .= "\n- RPS: *{$this->kelas->rps_rel->kode}*";
                $pesanFilter .= "\n- MK: {$this->kelas->rps_rel->mk_rel->mk}";
                $pesanFilter .= "\n- {$this->kelas->rps_rel->mk_rel->sks_text} - `{$this->kelas->rps_rel->mk_rel->sks} SKS`";

                return response()->json([
                    'status' => true,
                    'head' => '*✅ File PDF Berhasil Dibuat!*',
                    'message' => "Berhasil memproses semua jadwal dalam Kelas *{$this->kelas->kode_kelas}*: {$pesanFilter}",
                    'files' => $allData,
                    'file_type' => 'excel',
                ]);
            }

            throw new \Exception('Data tidak ditemukan untuk diproses!');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal Render PDF*',
                'message' => 'Terjadi Error: '.$e->getMessage(),
            ], 500);
        }

        // Cari berdasarkan kodeRPS jika ada kata RPS di depan $pdfGetCapaianKey
        // $rps = $this->getRPSByKode($kodeRPS);
        // $prodi = $this->getProdiByKode($KodePr);
        // $departemen = $this->getProdiByKode($KodeDp); // mungkin saja variabelnya sama karena tidak bisa dibedakan
        // $fakultas = $this->getProdiByKode($KodeFk); // mungkin saja variabelnya sama karena tidak bisa dibedakan
        // $parseAngkatan = ...
        // public function resolveRpskGrafikPdf($rps, $prodi = null, $departemen = null, $fakultas = null, $parseAngkatan = null);
    }

    public function parseKodeJadwal($input)
    {
        $targetCode = strtoupper(str_replace([' ', '-', '_'], '', $input));

        $pos = strpos($targetCode, 'IDL');
        if ($pos === false) {
            $pos = strpos($targetCode, 'PLG');
        }
        if ($pos !== false) {
            $prefix = substr($targetCode, 0, $pos);
            $lokasi = substr($targetCode, $pos, 3);
            $tahun = substr($targetCode, $pos + 3);
            $wilayah = substr($prefix, -1);
            $kodeKelas = substr($prefix, 0, -1);
            $finalKodeKelas = substr($kodeKelas, 0, 3).'-'.substr($kodeKelas, 3);
            $finalKodeJadwal = "{$wilayah}-{$lokasi}-{$tahun}";

            return [$finalKodeKelas, $finalKodeJadwal];
        }

        return [$targetCode, null];
    }

    public function getKelasAndJadwalByKode(
        $kode = null,
        $kode_jadwal = null
    ) {
        $this->kode = $kode;
        $this->kode_jadwal = $kode_jadwal;

        $this->kelas = $this->getKelasByKode($kode);

        if (! $this->kelas) {
            throw new ModelNotFoundException("Kelas dengan kode {$kode} tidak ditemukan!");
        }

        if (! empty($kode_jadwal)) {

            $fullKodeJadwal = str_contains($kode_jadwal, $this->kode)
                ? $kode_jadwal
                : $this->kode.'-'.$kode_jadwal;

            $this->jadwal = $this->getJadwalByKode($fullKodeJadwal);

            if (! $this->jadwal) {
                throw new ModelNotFoundException("Jadwal spesifik '{$kode_jadwal}' tidak ditemukan!");
            }

            if ($this->jadwal->kelas_id !== $this->kelas->id) {
                throw new \Exception("Jadwal '{$kode_jadwal}' tidak cocok dengan Kelas '{$kode}'!");
            }

            $this->jadwal_id = $this->jadwal->id;
        } else {
            $this->jadwal = null;
            $this->jadwal_id = null;
        }
    }

    // public function getKelasAndJadwalByKode(
    //     $kode = null,
    //     $kode_jadwal = null,
    // ) {
    //     $this->kode = $kode;

    //     // 🌟 PERBAIKAN 1: Gunakan ->first() alih-alih firstOrFail() agar kita bisa handle manual jika kosong
    //     $this->kelas = Kelas::query()
    //         ->where('kode_kelas', $kode)
    //         ->orWhereRaw(
    //             "REPLACE(kode_kelas, '-', '') = REPLACE(?, '-', '')",
    //             [$kode]
    //         )
    //         ->first();

    //     // Jika kelas tidak ditemukan, langsung lempar exception agar ditangkap catch-block di luar
    //     if (! $this->kelas) {
    //         throw new ModelNotFoundException("Kelas dengan kode {$kode} tidak ditemukan!");
    //     }

    //     $this->kode_jadwal = $kode_jadwal;

    //     // 🌟 PERBAIKAN 2: Jalankan pencarian jadwal HANYA JIKA $kode_jadwal diisi oleh user
    //     if (! empty($kode_jadwal)) {
    //         $parts = explode('-', $kode_jadwal);

    //         if (count($parts) < 3) {
    //             throw new \Exception('Format susunan komponen Kode Jadwal tidak valid (Harus mengandung Label-Wilayah-Tahun)!');
    //         }

    //         $labelKelas = $parts[0];
    //         $kodeWilayah = $parts[1];
    //         $tahunBlok = $parts[2];

    //         $this->jadwal = KelasJadwal::query()
    //             ->where('kelas_id', $this->kelas->id)
    //             ->where('label_kelas', $labelKelas)
    //             ->where('kode_wilayah', $kodeWilayah)
    //             ->whereRaw(
    //                 '
    //         CASE
    //             WHEN YEAR(tanggal_mulai) >= 3000
    //                 THEN YEAR(tanggal_mulai)

    //             WHEN YEAR(tanggal_mulai) >= 2100
    //                 THEN RIGHT(YEAR(tanggal_mulai), 3)

    //             WHEN YEAR(tanggal_mulai) >= 2000
    //                 THEN RIGHT(YEAR(tanggal_mulai), 2)

    //             ELSE YEAR(tanggal_mulai)
    //         END = ?
    //         ',
    //                 [$tahunBlok]
    //             )
    //             ->first();

    //         if (! $this->jadwal) {
    //             throw new ModelNotFoundException("Jadwal spesifik '{$kode_jadwal}' tidak ditemukan untuk Kelas ini!");
    //         }

    //         $this->jadwal_id = $this->jadwal->id;
    //     } else {
    //         // Kosongkan properti jadwal jika request murni pencarian tingkat kelas
    //         $this->jadwal = null;
    //         $this->jadwal_id = null;
    //     }
    // }
}
