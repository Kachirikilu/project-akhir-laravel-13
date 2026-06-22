<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Models\Kelas\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait WithKelas
{
private function processKelas(string $noWA, string $nameWA, string $pesan, array $daftarKelasKey, array $kelasMingguKey, array $kelasHariKey)
    {
        Log::info('=== SEDANG MENCARI KELAS BERDASARKAN SESI/PRODI ===');

        $sufiksNomor = substr($noWA, -9);
        $user = $this->searchUserWhatsApp($sufiksNomor);

        if (! $user) {
            return response()->json([
                'status' => false,
                'head' => '*❌ Autentikasi Gagal!*',
                'message' => "Silahkan Verifikasi dengan: \n`LOGIN [ID_IDENTITAS]`",
            ], 442);
        }

        $roleClean = strtolower($user->role);
        $pesanUpper = strtoupper(trim($pesan));

        // 1. CEK APAKAH USER MEMINTA DAFTAR INDUK KELAS MURNI
        $isDaftarKelasMurni = Str::contains($pesanUpper, array_map('strtoupper', $daftarKelasKey));

        $mulaiTanggal = null;
        $selesaiTanggal = null;
        $filterWaktu = 'Semua';
        $filterTeks = '';

        if (! $isDaftarKelasMurni) {
            if (Str::contains($pesanUpper, $kelasHariKey)) {
                $mulaiTanggal = now()->startOfDay()->toDateString();
                $selesaiTanggal = now()->endOfDay()->toDateString();
                $filterWaktu = 'Hari Ini';
                $filterTeks = "($filterWaktu)";
            } elseif (Str::contains($pesanUpper, $kelasMingguKey)) {
                $mulaiTanggal = now()->startOfWeek()->toDateString();
                $selesaiTanggal = now()->endOfWeek()->toDateString();
                $filterWaktu = 'Minggu Ini';
                $filterTeks = "($filterWaktu)";
            }
        } else {
            $filterTeks = '(Daftar Kelas)';
        }

        $daftarKelas = collect();

        // 2. AMBIL PR_ID (PROGRAM STUDI) BERDASARKAN USER ROLE
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
                'message' => 'Data Program Studi Anda tidak ditemukan.',
            ], 400);
        }

        // 3. PROSES QUERY UTAMA BERDASARKAN STRUKTUR MODEL KELAS DENGAN FILTER JADWAL SPESIFIK
        if ($isDaftarKelasMurni) {
            $daftarKelas = Kelas::where('pr_id', $prIdUser)->with('rps_rel')->get();
        } else {
            $queryKelas = Kelas::where('pr_id', $prIdUser);

            // 🌟 KUNCI PERBAIKAN: Filter inner relation 'jadwals' agar hanya memuat jadwal milik user tersebut
            $callbackFilterJadwal = function ($queryJadwal) use ($roleClean, $user, $mulaiTanggal, $selesaiTanggal) {
                if ($roleClean === 'dosen' && $user->dosen) {
                    $idDosen = $user->dosen->id;
                    $queryJadwal->whereHas('kelas_rel.rps_rel.dosens', function ($q) use ($idDosen) {
                        $q->where('dosens.id', $idDosen);
                    });
                } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
                    $idMahasiswa = $user->mahasiswa->id;
                    $queryJadwal->whereHas('mahasiswas', function ($q) use ($idMahasiswa) {
                        $q->where('mahasiswas.id', $idMahasiswa);
                    });
                }

                // Jika ada filter rentang tanggal, filter juga sesis di dalam jadwalnya
                if ($mulaiTanggal && $selesaiTanggal) {
                    $queryJadwal->whereHas('sesis', function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                    })->with(['sesis' => function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                    }]);
                } else {
                    $queryJadwal->with('sesis');
                }
            };

            // Terapkan pengetatan whereHas agar Kelas yang tidak punya Jadwal milik user langsung dibuang dari list
            $queryKelas->whereHas('jadwals', $callbackFilterJadwal);

            $daftarKelas = $queryKelas->with([
                'rps_rel',
                'jadwals' => $callbackFilterJadwal // 🌟 Inject filter ke dalam Eager Loading jadwals
            ])->get();
        }

        $head = "Halo _{$nameWA}_, berikut adalah...";
        $filterTeksX = !empty($filterTeks) ? ' ' . $filterTeks : '';
        $teksKelas = '📅 *Daftar Kelas' . $filterTeksX . "*\n- {$user->prodi_pr}\n\n";

        if ($daftarKelas->isEmpty()) {
            $teksKelas .= '_Tidak ada Kelas untuk ' . strtolower($isDaftarKelasMurni ? 'Prodi saat ini' : $filterWaktu) . '!_';
            return response()->json([
                'status' => true,
                'head' => trim($head),
                'message' => trim($teksKelas),
            ]);
        }

        $nomor = 1;

        // 4. LOOPING FORMATTING SEKARANG AKAN BERJALAN PER TIAP KELAS JADWAL SECARA AKURAT
        foreach ($daftarKelas as $kelas) {
            $mk = $kelas->rps_rel->mk ?? 'Mata Kuliah';
            $sks = $kelas->rps_rel->mk->sks ?? 2;
            $kodeKelas = $kelas->kode ?? 'MBG-121104';

            $hasJadwalDitampilkan = false;

            if (! $isDaftarKelasMurni && $kelas->jadwals && $kelas->jadwals->isNotEmpty()) {
                foreach ($kelas->jadwals as $jadwal) {
                    // Cek apakah ada sesi yang lolos filter tanggal (atau jika tanpa filter tanggal, pastikan sesi ada)
                    if ($jadwal->sesis && $jadwal->sesis->isNotEmpty()) {
                        $hasJadwalDitampilkan = true;
                        
                        foreach ($jadwal->sesis as $sesi) {
                            $teksKelas .= "{$nomor}. *{$mk}* - `{$sks} SKS`\n";
                            $teksKelas .= "- Kelas {$jadwal->label_extra}\n";
                            $teksKelas .= "- Kode Jadwal: {$jadwal->kode}\n"; // 🌟 Menampilkan Kode Jadwal Spesifik
                            $teksKelas .= $this->formatSesiTeks($sesi);
                            $nomor++;
                        }
                    }
                }
            }

            // Fallback jika user meminta daftar induk kelas (Murni) tanpa rincian sesi jadwal harian
            if (! $hasJadwalDitampilkan && $isDaftarKelasMurni) {
                $teksKelas .= "{$nomor}. *{$mk}*\n";
                $teksKelas .= "- Kode Kelas: {$kodeKelas}\n\n";
                $nomor++;
            }
        }

        return response()->json([
            'status' => true,
            'head' => trim($head),
            'message' => trim($teksKelas),
        ]);
    }
    private function formatSesiTeks($sesi)
    {
        $pertemuan = $sesi->pertemuan_ke ?? '-';
        $waktuISO = $sesi->waktu_pelaksanaan ?? null;

        if ($waktuISO) {
            $carbonWaktu = Carbon::parse($waktuISO);
            $hariSesi = $carbonWaktu->locale('id')->dayName;
            $tanggalSesi = $carbonWaktu->translatedFormat('d M Y');
        } else {
            $hariSesi = '-';
            $tanggalSesi = 'Waktu tidak diatur';
        }
        $teksKelas = "- Sesi ke-{$pertemuan}: _{$hariSesi}, {$tanggalSesi}_\n";
        $teksKelas .= "- ⏱️ {$sesi->jam_pelaksanaan}\n\n";

        return $teksKelas;
    }
}
