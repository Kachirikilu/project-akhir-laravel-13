<?php

namespace App\Http\Controllers\Api\WhatsappController;

use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait WithKelas
{
    private function processKelas(string $noWA, string $nameWA, string $pesan, array $daftarKelasKey, array $kelasMingguKey, array $kelasHariKey)
    {
        Log::info('=== SEDANG MENCARI KELAS BERDASARKAN SESI ===');

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

        $mulaiTanggal = null;
        $selesaiTanggal = null;
        $filterWaktu = 'Semua';
        $filterTeks = '';

        $isDaftarKelasMurni = Str::contains($pesanUpper, array_map('strtoupper', $daftarKelasKey));

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
        }

        $daftarKelas = collect();

        if ($isDaftarKelasMurni) {
            $queryKelas = Kelas::query();

            if ($roleClean === 'admin' && $user->admin) {
                $queryKelas->where('pr_id', $user->pr_id);
            } elseif ($roleClean === 'dosen' && $user->dosen) {
                $idDosen = $user->dosen->id;
                $queryKelas->whereHas('rps_rel.dosens', function ($q) use ($idDosen) {
                    $q->where('dosens.id', $idDosen);
                });
            } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
                $idMahasiswa = $user->mahasiswa->id;
                $queryKelas->whereHas('jadwals.mahasiswas', function ($q) use ($idMahasiswa) {
                    $q->where('mahasiswas.id', $idMahasiswa);
                });
            }
            $daftarKelas = $queryKelas->with(['rps_rel', 'jadwals'])->get();

        } else {
            if ($roleClean === 'admin' && $user->admin) {
                $prIdAdmin = $user->admin->pr_id;
                $queryKelasAdmin = Kelas::where('pr_id', $prIdAdmin);

                if ($mulaiTanggal && $selesaiTanggal) {
                    $queryKelasAdmin->whereHas('jadwals.sesis', function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                    });

                    $daftarKelas = $queryKelasAdmin->with([
                        'rps_rel',
                        'jadwals.sesis' => function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                            $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                        },
                    ])->get();
                } else {
                    $daftarKelas = $queryKelasAdmin->with('rps_rel')->get();
                }

            } elseif ($roleClean === 'dosen' && $user->dosen) {
                $idDosen = $user->dosen->id;

                $queryJadwal = KelasJadwal::whereHas('kelas_rel.rps_rel.dosens', function ($query) use ($idDosen) {
                    $query->where('dosens.id', $idDosen);
                });

                if ($mulaiTanggal && $selesaiTanggal) {
                    $queryJadwal->whereHas('sesis', function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                    });
                }

                $daftarKelas = $queryJadwal->with([
                    'kelas_rel.rps_rel',
                    'sesis' => function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        if ($mulaiTanggal && $selesaiTanggal) {
                            $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                        }
                    },
                ])->get();

            } elseif ($roleClean === 'mahasiswa' && $user->mahasiswa) {
                $idMahasiswa = $user->mahasiswa->id;

                $queryJadwal = KelasJadwal::whereHas('mahasiswas', function ($query) use ($idMahasiswa) {
                    $query->where('mahasiswas.id', $idMahasiswa);
                });

                if ($mulaiTanggal && $selesaiTanggal) {
                    $queryJadwal->whereHas('sesis', function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                    });
                }

                $daftarKelas = $queryJadwal->with([
                    'kelas_rel.rps_rel',
                    'sesis' => function ($querySesi) use ($mulaiTanggal, $selesaiTanggal) {
                        if ($mulaiTanggal && $selesaiTanggal) {
                            $querySesi->whereBetween('tanggal', [$mulaiTanggal, $selesaiTanggal]);
                        }
                    },
                ])->get();
            }
        }

        $head = "Halo _{$nameWA}_, berikut hasil pencarian Kelas:";
        $filterTeksX = ! empty($filterTeks) ? ' '.$filterTeks : '';
        $teksKelas = '📅 *Daftar Kelas'.$filterTeksX."*\n- {$user->prodi_pr}\n\n";

        if ($daftarKelas->isEmpty()) {
            $teksKelas .= '_Tidak ada Kelas untuk '.strtolower(empty($filterTeks) ? 'saat ini' : $filterWaktu).'!_';

            return response()->json([
                'status' => true,
                'head' => trim($head),
                'message' => trim($teksKelas),
            ]);
        }

        $nomor = 1;

        // ==========================================
        // REFAKTOR LOOP FORMATTING BERDASARKAN ROLE
        // ==========================================
        foreach ($daftarKelas as $item) {

            if ($roleClean === 'admin' || $isDaftarKelasMurni) {
                $mk = $item->rps_rel->mk ?? 'Mata Kuliah';
                $sks = $item->rps_rel->mk->sks ?? 2;
                $kode = $item->kode ?? 'MBG-121104';
                $kodeRPS = $item->kode_rps;

                $sesis = collect();
                if ($item->jadwals && ($mulaiTanggal && $selesaiTanggal)) {
                    foreach ($item->jadwals as $jadwal) {
                        if ($jadwal->sesis) {
                            $sesis = $sesis->merge($jadwal->sesis);
                        }
                    }
                }

                if ($sesis->isEmpty()) {
                    $teksKelas .= "{$nomor}. *{$mk}* - `{$sks} SKS`\n";
                    $teksKelas .= "- Kode: {$kode}\n";
                    $teksKelas .= "- RPS: ```{$kodeRPS}```\n\n";
                    $nomor++;
                } else {
                    foreach ($sesis as $sesi) {
                        $kodeJadwal = $sesi->jadwal_rel->kode;
                        $label = $sesi->jadwal_rel->label_extra;

                        $teksKelas .= "{$nomor}. *{$mk}* - `{$sks} SKS`\n";
                        $teksKelas .= "- Kelas {$label}\n";
                        $teksKelas .= "- Kode: *{$kodeJadwal}*\n";
                        $teksKelas .= "- RPS: ```{$kodeRPS}```\n";
                        $teksKelas .= $this->formatSesiTeks($sesi);
                        $nomor++;
                    }
                }

            } else {
                $mk = $item->kelas_rel->rps_rel->mk ?? 'Mata Kuliah';
                $sks = $item->kelas_rel->rps_rel->mk->sks ?? 2;
                $kodeJadwal = $item->kode ?? 'MBG-121104-X-IDL';
                $kodeRPS = $item->kode_rps;
                $label = $item->label_extra;

                $sesis = $item->sesis ?? collect();

                foreach ($sesis as $sesi) {
                    $teksKelas .= "{$nomor}. *{$mk}* - `{$sks} SKS`\n";
                    $teksKelas .= "- Kelas {$label}\n";
                    $teksKelas .= "- Kode: *{$kodeJadwal}*\n";
                    $teksKelas .= "- RPS: ```{$kodeRPS}```\n";
                    $teksKelas .= $this->formatSesiTeks($sesi);
                    $nomor++;
                }
            }
        }

        return response()->json([
            'status' => true,
            'head' => trim($head),
            'message' => trim($teksKelas),
        ]);

        // return response()->json([
        //     'status' => true,
        //     'head' => $head,
        //     'message' => trim($teksKelas),
        // ]);
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
