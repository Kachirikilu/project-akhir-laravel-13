<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait WithRPS
{
    use WithRPSShow;

    private function processRPS(string $noWA, string $nameWA, string $pesan, array $daftarRPSKey)
    {
        Log::info('=== SEDANG MENCARI RPS BERDASARKAN ROLE & SEMESTER ===');

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
            ], 442);
        }

        $roleClean = strtolower($user->role);
        $pesanUpper = strtoupper(trim($pesan));

        // 1. CEK KEYWORD UTAMA
        $isDaftarRPSMurni = Str::contains($pesanUpper, array_map('strtoupper', $daftarRPSKey));

        // 2. EKSTRAKSI FILTER SEMESTER (Contoh: S1, Semester 3, Sem 5)
        $filterSemester = null;
        if (preg_match('/(?:SEMESTER|SEM|S)\s*(\d+)/i', $pesanUpper, $matches)) {
            $filterSemester = (int) $matches[1];
        }

        // 3. AMBIL PR_ID (PROGRAM STUDI) USER
        $prIdUser = null;
        if ($roleClean === 'admin' && $user->admin) {
            $prIdUser = $user->admin->pr_id;
        } elseif ($roleClean === 'dosen' && $user->dosen) {
            $prIdUser = $user->dosen->pr_id;
        } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
            $prIdUser = $user->mahasiswa->pr_id;
        }

        if (! $prIdUser) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal Memuat Data!*',
                'message' => 'Data Program Studi Anda tidak ditemukan!',
            ], 400);
        }

        // Base Query Model RPS
        $queryRPS = RPS::query();

        // 4. STRATEGI FILTER QUERY BERDASARKAN ROLE DAN KEYWORD
        if ($isDaftarRPSMurni || $roleClean === 'admin') {
            $queryRPS->whereHas('mk_rel.prodis', function ($q) use ($prIdUser) {
                $q->where('prodis.id', $prIdUser);
            });
        } else {
            if ($roleClean === 'dosen' && $user->dosen) {
                $idDosen = $user->dosen->id;
                $queryRPS->whereHas('dosens', function ($q) use ($idDosen) {
                    $q->where('dosens.id', $idDosen);
                });
            } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
                $idMahasiswa = $user->mahasiswa->id;
                $queryRPS->where(function ($mainQuery) use ($idMahasiswa) {
                    $mainQuery->whereHas('kelas.jadwals.mahasiswas', function ($q) use ($idMahasiswa) {
                        $q->where('mahasiswas.id', $idMahasiswa);
                    })->orWhereHas('nilai_mahasiswas', function ($q) use ($idMahasiswa) {
                        $q->where('mahasiswa_id', $idMahasiswa);
                    });
                });
            }
        }

        // 5. OPSI JIKA USER MELAKUKAN FILTER SEMESTER SPESIFIK
        if ($filterSemester) {
            $queryRPS->whereHas('mk_rel', function ($q) use ($filterSemester) {
                $q->where('semester', $filterSemester);
            });
        }

        // 6. EAGER LOADING & SORTING BERDASARKAN SEMESTER DAN DIGIT_MK (TERKECIL)
        $rawRPS = $queryRPS->with(['mk_rel'])
            ->get()
            ->sortBy(function ($rps) {
                return [
                    $rps->mk_rel->semester ?? 0,
                    $rps->mk_rel->digit_mk ?? 0,
                ];
            });

        // 7. PENGELOMPOKAN (GROUP BY) SEMESTER
        $groupedRPS = $rawRPS->groupBy(function ($rps) {
            return $rps->mk_rel->semester ?? 'Lainnya';
        });

        // 8. FORMAT OUTPUT PESAN WHATSAPP
        $head = "Halo _{$nameWA}_, berikut hasil pencarian RPS:";
        $subTitle = $isDaftarRPSMurni ? 'Seluruh Prodi' : 'RPS Terdaftar Anda';
        $filterTeks = $filterSemester ? " (Semester {$filterSemester})" : '';

        $teksRPS = "📚 *Daftar Dokumen RPS - {$subTitle}{$filterTeks}*\n";
        $teksRPS .= "- Prodi: {$user->prodi_pr}\n\n";

        if ($groupedRPS->isEmpty()) {
            $teksRPS .= '_Tidak ditemukan dokumen RPS yang sesuai dengan kriteria._';

            return response()->json([
                'status' => true,
                'head' => trim($head),
                'message' => trim($teksRPS),
            ]);
        }

        $nomorGlobal = 1;

        foreach ($groupedRPS as $semester => $daftarRpsSmt) {
            $teksRPS .= "🔷 *SEMESTER {$semester}*\n";

            foreach ($daftarRpsSmt as $rps) {
                $namaMK = $rps->mk_rel->mk ?? 'Mata Kuliah Tanpa Nama';
                $kodeMK = $rps->mk_rel->kode ?? '-';
                $sks = $rps->mk_rel->sks ?? 0;
                $kodeRps = $rps->kode ?? 'RPS-Code';

                $teksRPS .= "  {$nomorGlobal}. *{$namaMK}* - `{$sks} SKS`\n";
                $teksRPS .= "     - Kode MK: ```{$kodeMK}```\n";
                $teksRPS .= "     - Kode RPS: *{$kodeRps}*\n";
                if ($rps->is_draf == 1) {
                    $teksRPS .= "     - Draf:   ```Belum Aktif```\n";
                }
                $teksRPS .= "--------------------------------------------\n";
                $nomorGlobal++;
            }
            $teksRPS .= "\n";
        }

        return response()->json([
            'status' => true,
            'head' => trim($head),
            'message' => trim($teksRPS),
        ]);
    }

    // private function processGetPDFRPS(string $noWA, string $nameWA, string $pesan, $pdfGetRPSKey)
    // {

    //     Log::info('=== SEDANG MEMPROSES RPS MENJADI PDF ===');

    //     $sufiksNomor = substr($noWA, -9);
    //     $user = $this->searchUserWhatsApp($sufiksNomor);

    //     if (! $user) {
    //         return response()->json([
    //             'status' => false,
    //             'head' => '*❌ Autentikasi Gagal!*',
    //             'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`",
    //         ], 442);
    //     }

    //     $matchedKey = collect($pdfGetRPSKey)->first(fn($key) => Str::startsWith(strtoupper($pesan), strtoupper($key)));
    //     $kodeRPSInput = trim(str_ireplace($matchedKey, '', $pesan));

    //     if (empty($kodeRPSInput)) {
    //         return response()->json([
    //             'status' => true,
    //             'head' => '*❌ Gagal Mencari RPS!*',
    //             'message' => "Silakan masukkan Kode RPS setelah kata kunci! Contoh: *{$matchedKey} KODE-RPS*",
    //         ], 400);
    //     }

    //     $rps = $this->getRPSByKode($kodeRPSInput);
    //     if (! $rps) {
    //         return response()->json([
    //             'status' => true,
    //             'head' => '*❌ Gagal Mencari RPS!*',
    //             'message' => "Berkas RPS dengan kode `{$kodeRPSInput}` tidak ditemukan!",
    //         ], 404);
    //     }

    //     try {
    //         $prodis = $rps->mk_rel->prodis->sortBy([
    //             ['prodi', 'asc'],
    //             ['strata', 'asc'],
    //         ]);
    //         $prodi = null;

    //         if (//nanti ini ambil id pada prodi hasil pencarian getProdiByKode) {
    //             $prodi = $prodis->find(//nanti ini ambil id pada prodi hasil pencarian getProdiByKode);
    //         }
    //         if (!$prodi) {
    //             $prodi = $prodis->firstWhere('id', $user->pr_id);
    //         }
    //         if (!$prodi) {
    //             $prodi = $prodis->first();
    //         }
    //         if (!$prodi) {
    //             return response()->json([
    //                 'status' => true,
    //                 'head' => '*❌ Gagal Mencari RPS!*',
    //                 'message' => "Data Program Studi tidak ditemukan pada RPS ini!",
    //             ], 400);
    //         }
    //         $pdfRawContent = $this->generateRPSRawPDFContent($rps, $prodi);
    //         // $data = $this->formatRPSDetailForShow($rps);
    //         $fileNameSafe = str_replace('/', '-', 'RPS_'.$prodi->kode.'_'.$rps->kode.'_'.$rps->mk_rel->mk.'.pdf');

    //         return response()->json([
    //             'status' => true,
    //             'message' => "Berkas RPS *{$rps->mk_rel->mk}* dengan Kode `{$rps->kode}` berhasil dibuat!",
    //             'file_base64' => base64_encode($pdfRawContent),
    //             'file_name' => $fileNameSafe,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => '❌ Gagal me-render PDF RPS: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }

    private function processGetPDFRPS(string $noWA, string $nameWA, string $pesan, $pdfGetRPSKey)
    {
        Log::info('=== SEDANG MEMPROSES RPS MENJADI PDF ===');

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json(['status' => false, 'head' => '*❌ Autentikasi Gagal!*', 'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_AKADEMIK]`"], 442);
        }

        $matchedKey = collect($pdfGetRPSKey)->first(fn ($key) => Str::startsWith(strtoupper($pesan), strtoupper($key)));
        $inputParams = trim(str_ireplace($matchedKey, '', $pesan));

        if (empty($inputParams)) {
            return response()->json(['status' => true, 'head' => '*❌ Gagal Mencari RPS!*', 'message' => "Format: *{$matchedKey} KODE_RPS [KODE_PRODI]*"], 400);
        }

        $parts = explode(' ', $inputParams);
        $kodeRPSInput = $parts[0];
        $kodePrInput = $parts[1] ?? null;

        // Coba cari RPS
        $rps = $this->getRPSByKode($kodeRPSInput);

        if (! $rps && isset($parts[1])) {
            $rps = $this->getRPSByKode($parts[1]);
            $kodePrInput = $kodeRPSInput;
            $kodeRPSInput = $parts[1];
        }

        if (! $rps) {
            return response()->json(['status' => true, 'head' => '*❌ Gagal Mencari RPS!*', 'message' => "Berkas RPS dengan kode `{$kodeRPSInput}` tidak ditemukan!"], 404);
        }

        try {
            $data = $this->handleRpsPdfExport($rps->id, $kodePrInput, 'json', true);
            return response()->json([
                'status' => true,
                'message' => "Berkas RPS *{$data['rps']->mk_rel->mk}* {$data['prodi']->prodi} berhasil dibuat!",
                'file_base64' => base64_encode($data['content']),
                'file_name' => $data['name'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Gagal!*',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // public function getRPSByKode(
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
    //         throw new ModelNotFoundException("Kelas dengan kode {$kode} tidak ditemukan.");
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
    //             throw new ModelNotFoundException("Jadwal spesifik '{$kode_jadwal}' tidak ditemukan untuk Kelas ini.");
    //         }

    //         $this->jadwal_id = $this->jadwal->id;
    //     } else {
    //         // Kosongkan properti jadwal jika request murni pencarian tingkat kelas
    //         $this->jadwal = null;
    //         $this->jadwal_id = null;
    //     }
    // }
}
