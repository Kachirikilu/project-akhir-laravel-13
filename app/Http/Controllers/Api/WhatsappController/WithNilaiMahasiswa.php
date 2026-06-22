<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Models\Auth\Mahasiswa;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Log;

trait WithNilaiMahasiswa
{
    private function processNilaiMahasiswa(string $noWA, string $nameWA, string $pesan)
    {
        Log::info('=== SEDANG MENCARI NILAI MAHASISWA ===');

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_IDENTITAS]`",
            ], 404);
        }

        $roleClean = strtolower($user->role);
        $pesanUpper = strtoupper(trim($pesan));

        // =======================================================================
        // PARSING REGEX: Nilai [NIM] [GANJIL/GENAP] [TAHUN AKADEMIK]
        // Contoh match: "NILAI 03041282227063 GANJIL 2025/2026" atau "NILAI GANJIL 2025/2026"
        // =======================================================================
        $targetMahasiswaId = null;
        $targetMhsNama = '';
        $targetMhsNim = '';
        $filterSemester = null;
        $filterTA = null;

        // Pecah komponen pesan menggunakan spasi/whitespace
        $parts = preg_split('/\s+/', $pesanUpper);

        // Buat temporary array untuk menampung argumen setelah kata kunci "NILAI"
        $args = array_slice($parts, 1);

        // Deteksi NIM jika pengirim adalah Admin atau Dosen
        if (in_array($roleClean, ['admin', 'dosen'])) {
            if (isset($args[0]) && is_numeric($args[0])) {
                $nimInput = array_shift($args); // Ambil argumen pertama sebagai NIM

                $mhsTarget = Mahasiswa::where('nim', $nimInput)->first();
                if (! $mhsTarget) {
                    return response()->json([
                        'status' => true,
                        'head' => "*❌ Data Tidak Ditemukan!*",
                        'message' => "Data Mahasiswa dengan NIM *{$nimInput}* tidak ditemukan!",
                    ]);
                }
                $targetMahasiswaId = $mhsTarget->id;
                $targetMhsNama = $mhsTarget->name;
                $targetMhsNim = $mhsTarget->nim;
            } else {
                return response()->json([
                    'status' => true,
                    'head' => "*❌ Format Salah!*",
                    'message' => "Untuk melihat data Nilai Mahasiswa, gunakan:\n_`Nilai [NIM]`_\natau\n_`Nilai [NIM] [GANJIL/GENAP] [TAHUN_AKADEMIK]`_",
                ]);
            }
        } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
            // Jika mahasiswa, paksa ambil ID-nya sendiri
            $targetMahasiswaId = $user->mahasiswa->id;
            $targetMhsNama = $user->mahasiswa->name;
            $targetMhsNim = $user->mahasiswa->nim;
        } else {
            return response()->json([
                'status' => false,
                'head' => "*❌ Gagal Mengambil Data!*",
                'message' => '❌ Fitur ini hanya untuk menampilkan Nilai Mahasiswa!',
            ], 442);
        }


        // Sisa elemen di array $args sekarang murni untuk [GANJIL/GENAP] dan [TAHUN AKADEMIK]
        if (isset($args[0]) && in_array($args[0], ['GANJIL', 'GENAP'])) {
            $filterSemester = ucfirst(strtolower(array_shift($args)));
        }

        if (isset($args[0])) {
            if (preg_match('/^\d{4}\/\d{4}$/', $args[0])) {
                $filterTA = $args[0];
            }
        }

        // ==========================================
        // QUERY DATA NILAI MAHASISWA
        // ==========================================
        $queryNilai = NilaiMahasiswa::where('mahasiswa_id', $targetMahasiswaId);

        // Tambahkan filter dinamis jika diinput oleh user
        if ($filterSemester) {
            $queryNilai->where('ganjil_genap', $filterSemester);
        }
        if ($filterTA) {
            $queryNilai->where('tahun_akademik', $filterTA);
        }
        $isFilter = false;
        if ($filterSemester || $filterTA) {
            $isFilter = true;
        }

        $daftarNilai = $queryNilai->with(['rps_rel.mk_rel'])
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('ganjil_genap', 'desc')
            ->get();

        if ($daftarNilai->isEmpty()) {
            $periodeAktif = array_filter([$filterSemester, $filterTA]);
            $infoFilter = !empty($periodeAktif) ? " untuk Periode " . implode(' ', $periodeAktif) : '';

            return response()->json([
                'status' => true,
                'head' => "📊 *Informasi Nilai*",
                'message' => "Belum ada data nilai yang terekam untuk Mahasiswa *{$targetMhsNama}* ({$targetMhsNim}){$infoFilter}.",
            ]);
        }

        // ==========================================
        // GENERATE OUTPUT TEKS WHATSAPP
        // ==========================================
        // --- 1. HITUNG IPK (KUMULATIF GLOBAL) TERLEBIH DAHULU ---
        $totalSksIpk = 0;
        $totalBobotIpk = 0;

        foreach ($daftarNilai as $nilaiMhs) {
            // Ambil SKS, default 2 jika null
            $sks = $nilaiMhs->rps_rel->mk_rel->sks ?? 2;

            // Pastikan nilai_index ada dan berupa angka sebelum dihitung
            if (isset($nilaiMhs->nilai_index)) {
                $index = (float) $nilaiMhs->nilai_index;
                $totalSksIpk += $sks;
                $totalBobotIpk += ($index * $sks);
            }
        }

        // Hitung skor IPK & tentukan Mutu IPK
        $ipkSkor = $totalSksIpk > 0 ? $totalBobotIpk / $totalSksIpk : 0;
        $ipkFormat = number_format($ipkSkor, 2, '.', '');

        // Logika konversi mutu IPK (Sesuaikan dengan standar kampusmu)
        $ipkPredikat = 'E';
        $ipkMutu = 'Sangat Kurang';

        if ($ipkSkor >= 4.00) {
            $ipkPredikat = 'A';
            $ipkMutu = 'Sangat Memuaskan';
        } elseif ($ipkSkor >= 3.70) {
            $ipkPredikat = 'A-';
            $ipkMutu = 'Sangat Memuaskan';
        } elseif ($ipkSkor >= 3.30) {
            $ipkPredikat = 'B+';
            $ipkMutu = 'Memuaskan';
        } elseif ($ipkSkor >= 3.00) {
            $ipkPredikat = 'B';
            $ipkMutu = 'Memuaskan';
        } elseif ($ipkSkor >= 2.70) {
            $ipkPredikat = 'B-';
            $ipkMutu = 'Memuaskan';
        } elseif ($ipkSkor >= 2.30) {
            $ipkPredikat = 'C+';
            $ipkMutu = 'Cukup';
        } elseif ($ipkSkor >= 2.00) {
            $ipkPredikat = 'C';
            $ipkMutu = 'Cukup';
        } elseif ($ipkSkor >= 1.00) {
            $ipkPredikat = 'D';
            $ipkMutu = 'Kurang';
        }

        // --- 2. STRUKTUR HEAD (INFO MAHASISWA & IPK DI ATAS) ---
        $head = "📊 *KARTU HASIL EVALUASI MAHASISWA*\n";
        $head .= "Nama: *{$targetMhsNama}*\n";
        $head .= "NIM: ```{$targetMhsNim}```\n";
        if (!$isFilter) {
            $head .= "🎓 Total SKS: *{$totalSksIpk} SKS*\n";
            $head .= "🏆 *IPK: {$ipkFormat}*\n";
            $head .= "🏅 Predikat: *{$ipkPredikat}*\n";
            $head .= "⭐ _{$ipkMutu}_\n";
        }
        $head .= '--------------------------------------------';

        $teksNilai = '';
        $groupedNilai = $daftarNilai->groupBy(function ($item) {
            return $item->tahun_akademik.' -  '.$item->ganjil_genap;
        });

        foreach ($groupedNilai as $periode => $listNilai) {
            $teksNilai .= "🔹 *Periode {$periode}*\n";
            $nomor = 1;

            $totalSksIps = 0;
            $totalBobotIps = 0;

            foreach ($listNilai as $nilaiMhs) {
                $mkNama = $nilaiMhs->rps_rel->mk_rel->nama_mk ?? $nilaiMhs->rps_rel->nama_skm ?? 'Mata Kuliah';
                $rps = $nilaiMhs->rps_rel->kode ?? '2';
                $sks = $nilaiMhs->rps_rel->mk_rel->sks ?? 2;

                $skor = isset($nilaiMhs->nilai) ? number_format((float) $nilaiMhs->nilai, 2, '.', '') : '-';
                $indexValue = isset($nilaiMhs->nilai_index) ? (float) $nilaiMhs->nilai_index : null;
                $index = is_null($indexValue) ? '-' : number_format($indexValue, 2, '.', '');
                $mutu = $nilaiMhs->nilai_mutu ?? '-';

                if (! is_null($indexValue)) {
                    $totalSksIps += $sks;
                    $totalBobotIps += ($indexValue * $sks);
                }

                $teksNilai .= "  {$nomor}. *{$mkNama}* - `{$sks} SKS`\n";
                $teksNilai .= "     » RPS: ```{$rps}```\n";
                $teksNilai .= "     » Nilai: *{$skor}*\n";
                $teksNilai .= "     » Index: *{$index}*\n";
                $teksNilai .= "     » Mutu: *{$mutu}*\n";
                $nomor++;
            }

            // Hitung skor IPS Semester
            $ipsSkor = $totalSksIps > 0 ? $totalBobotIps / $totalSksIps : 0;
            $ipsFormat = number_format($ipsSkor, 2, '.', '');

            // Logika konversi mutu IPS Semester
            $ipsMutu = 'E';
            if ($ipsSkor >= 3.51) {
                $ipsMutu = 'A';
            } elseif ($ipsSkor >= 3.00) {
                $ipsMutu = 'B';
            } elseif ($ipsSkor >= 2.50) {
                $ipsMutu = 'C';
            } elseif ($ipsSkor >= 2.00) {
                $ipsMutu = 'D';
            }

            // Tampilkan totalan IPS di bawah tiap akhir semester
            $teksNilai .= "🔸 *TOTAL SEMESTER INI:*\n";
            $teksNilai .= "     » SKS Diambil: *{$totalSksIps} SKS*\n";
            $teksNilai .= "     » *IPS: {$ipsFormat}* (Mutu: *{$ipsMutu}*)\n";
            $teksNilai .= "--------------------------------------------\n\n";
        }

        // $teksNilai .= '_Generated automatically via Gateway Academic System_';

        return response()->json([
            'status' => true,
            'head' => trim($head),
            'message' => trim($teksNilai),
        ]);
    }
}
