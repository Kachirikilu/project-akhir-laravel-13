<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Models\Akademik\RPS;

use App\Models\ProgramStudi\Prodi;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;

use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

trait WithCpmkGrafikShow
{
    use WithUserFilters;

    public $mapping_pertemuan;

    public function printPDFCpmkGrafik($jadwalRPSId, $isRPS = false)
    {
        if ($isRPS) {
            // $rpsId = $jadwalRPSId;
            if ($this->filterStatus == '') {
                $prodi = Prodi::find(Auth::user()->pr_id);
            } else {
                if ($this->selectedPrId !== null) {
                    $prodi = Prodi::find($this->selectedPrId);
                }
                if ($this->selectedDpId !== null) {
                    $departemen = Departemen::find($this->selectedDpId);
                }
                if ($this->selectedFkId !== null) {
                    $fakultas = Fakultas::find($this->selectedFkId);
                }

            }
            if ($this->filterAngkatan == '') {
                $angkatan = $this->searchAngkatan;
            } else {
                $angkatan = $this->filterAngkatan;
            }

            $prName = $prodi->prodi ?? null;
            $dpName = $departemen->departemen_dp ?? null;
            $fkName = $fakultas->fakultas_fk ?? null;

            $data = $this->resolveCpmkGrafikPdf($jadwalRPSId, $isRPS, $angkatan ?? null, $prName ?? null, $dpName ?? null, $fkName ?? null);

        } else {
            $data = $this->resolveCpmkGrafikPdf($jadwalRPSId);
        }

        return response()->streamDownload(fn () => print ($data['content']), $data['name'], ['Content-Type' => 'application/pdf']);
    }

    public function resolveAllCpmkGrafikPdf($kelasId)
    {
        $kelas = Kelas::where('id', $kelasId)->firstOrFail();

        $jadwals = $kelas->jadwals;
        $jumlahJadwal = $jadwals->count();

        $fileResults = [];

        foreach ($jadwals as $jadwal) {
            $data = $this->resolveCpmkGrafikPdf($jadwal->id);
            $fileResults[] = [
                'name' => $data['name'],
                'base64' => base64_encode($data['content']),
                'mime_type' => 'application/pdf',
            ];
        }

        return response()->json([
            'status' => true,
            'message' => "Berhasil generate {$jumlahJadwal} file PDF untuk Kelas {$kelas->kode}",
            'files' => $fileResults,
        ]);
    }

    public function resolveRpskGrafikPdf($rps, $prodi = null, $departemen = null, $fakultas = null, $angkatan = null)
    {
        $this->filterStatus = 'user-all';
        $this->switchTable = 'mahasiswa';

        if ($prodi) {
            $this->selectedPrId = $prodi->id;
        }
        if ($departemen) {
            $this->selectedDpId = $departemen->id;
        }
        if ($fakultas) {
            $this->selectedFkId = $fakultas->id;
        }

        $this->filterAngkatan = $angkatan;
        $prName = $prodi->prodi ?? null;
        $dpName = $departemen->departemen_dp ?? null;
        $fkName = $fakultas->fakultas_fk ?? null;

        $data = $this->resolveCpmkGrafikPdf($rps->id, true, $angkatan ?? null, $prName ?? null, $dpName ?? null, $fkName ?? null);

        $pesanFilter = "\n- RPS: *{$rps->kode}*";
        $pesanFilter .= "\n- MK: {$rps->mk_rel->mk}";
        $pesanFilter .= "\n- {$rps->mk_rel->sks_text} - `{$rps->mk_rel->sks} SKS`";
        if ($angkatan) {
            $pesanFilter .= "\n- Angkatan: {$angkatan}";
        }
        if ($prodi) {
            $pesanFilter .= "\n- ```{$prName}```";
        }
        if ($departemen) {
            $pesanFilter .= "\n- {$dpName}";
        }
        if ($fakultas) {
            $pesanFilter .= "\n- {$fkName}";
        }

        $cleanData = function ($data) {
            array_walk_recursive($data, function (&$item) {
                if (is_string($item)) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                    $item = iconv('UTF-8', 'UTF-8//IGNORE', $item);
                }
            });

            return $data;
        };

        $fileInfo = [
            'name' => $data['name'],
            'base64' => base64_encode($data['content']),
            'mime_type' => 'application/pdf',
        ];

        $response = [
            'status' => true,
            'head' => '*✅ File PDF Berhasil Dibuat!*',
            'message' => "Berhasil generate file untuk: {$pesanFilter}",
            'files' => [$fileInfo],
        ];

        return response()->json($cleanData($response));
    }

    protected function resolveCpmkGrafikPdf($jadwalRPSId, $isRPS = false, $angkatan = false, $prName = false, $dpName = false, $fkName = false)
    {
        if ($isRPS) {
            $queryUser = $this->inputUserSearch('mahasiswa', null, null, null, $jadwalRPSId);
            $rps = RPS::where('id', $jadwalRPSId)->firstOrFail();

            $this->addNilaiRPSSubquery($queryUser, $jadwalRPSId, 'mhs_nilai_array', 'nilai_array');
            $this->addNilaiRPSSubquery($queryUser, $jadwalRPSId, 'mhs_bobot_array', 'bobot_array');
        } else {
            $queryUser = $this->inputUserSearch('mahasiswa', $jadwalRPSId, null, 1);
            $jadwal = KelasJadwal::where('id', $jadwalRPSId)->firstOrFail();
            $rps = $jadwal->kelas_rel->rps_rel;

            $this->addNilaiJadwalSubquery($queryUser, $jadwalRPSId, 'mhs_nilai_array', 'nilai_array');
            $this->addNilaiJadwalSubquery($queryUser, $jadwalRPSId, 'mhs_bobot_array', 'bobot_array');
        }

        $sampleNilai = NilaiMahasiswa::where('rps_id', $rps->id)->first() ?? new NilaiMahasiswa(['rps_id' => $rps->id]);
        $mappingData = $sampleNilai->mapping_pertemuan ?? [];
        $users = $queryUser->get();

        $groupsCpmk = collect();
        if (! empty($mappingData)) {
            $collectionMapping = collect($mappingData);
            $totalGlobalBobotMentah = $collectionMapping->sum('bobot') ?: 1;
            $this->mapping_pertemuan = $collectionMapping->map(function ($item) use ($totalGlobalBobotMentah) {
                $item['bobot'] = (($item['bobot'] ?? 0) / $totalGlobalBobotMentah) * 100;

                return $item;
            })->toArray();
            $groupsCpmk = collect($this->mapping_pertemuan)->groupBy('kode_cpmk');
        }

        // Logika Tahun Akademik
        if (! $isRPS) {
            $sesiPertama = $jadwal->sesis->sortBy('tanggal_pelaksanaan')->first();
            $tahun = $sesiPertama ? Carbon::parse($sesiPertama->tanggal_pelaksanaan)->format('Y') : date('Y');
            $semester = ($sesiPertama && Carbon::parse($sesiPertama->tanggal_pelaksanaan)->format('n') >= 7) ? 'Ganjil' : 'Genap';
            $tahunAkademik = "{$semester} {$tahun}";
        }

        // Generate Content
        $pdfContent = $this->generateCpmkGrafikRawPDFContent($users, $jadwal ?? null, $rps, $groupsCpmk, $tahunAkademik ?? null, $angkatan ?? null, $prName ?? null, $dpName ?? null, $fkName ?? null);

        if ($isRPS) {
            $parts = [
                str_replace(['/', '\\'], '-', $rps->mk),
                str_replace(['/', '\\'], '-', $rps->kode)
            ];
            if (!empty($angkatan)) $parts[] = 'Angkatan ' . $angkatan;
            if (!empty($prName))   $parts[] = str_replace(['/', '\\'], '-', $prName);
            if (!empty($dpName))   $parts[] = str_replace(['/', '\\'], '-', $dpName);
            if (!empty($fkName))   $parts[] = str_replace(['/', '\\'], '-', $fkName);
            $fileName = implode('_', $parts) . '.pdf';

        } else {
            $parts = [
                str_replace('/', '-', $jadwal->kode),
                str_replace(['/', '\\'], '-', $rps->mk),
                str_replace(['/', '\\'], '-', $rps->kode),
                str_replace(' ', '-', $tahunAkademik)
            ];
            $fileName = implode('_', $parts) . '.pdf';
        }

        return ['content' => $pdfContent, 'name' => $fileName, 'jadwal' => $jadwal ?? null];
    }

    // protected function generateCpmkGrafikRawPDFContent($users, $jadwal, $rps, $groupsCpmk, $tahunAkademik, $angkatan = null, $prName = null, $dpName = null, $fkName = null): string
    // {
    //     $logoPath = public_path('images/logo-unsri.webp');
    //     $univ = strtoupper(env('UNIVERSITAS'));
    //     $logoBase64 = '';

    //     if (file_exists($logoPath)) {
    //         $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    //         $dataLogo = file_get_contents($logoPath);
    //         $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($dataLogo);
    //     }
    //     $countCPMK = $rps->count_cpmk;

    //     if ($countCPMK == 1) {
    //         $chunk_users = $users->chunk(18);
    //     } elseif ($countCPMK == 2) {
    //         $chunk_users = $users->chunk(13);
    //     } elseif ($countCPMK == 3) {
    //         $chunk_users = $users->chunk(10);
    //     } elseif ($countCPMK == 4) {
    //         $chunk_users = $users->chunk(8);
    //     } elseif ($countCPMK == 5) {
    //         $chunk_users = $users->chunk(6);
    //     } elseif ($countCPMK == 6 || $countCPMK == 7) {
    //         $chunk_users = $users->chunk(5);
    //     } elseif ($countCPMK == 8 || $countCPMK == 9) {
    //         $chunk_users = $users->chunk(4);
    //     } elseif ($countCPMK == 10 || $countCPMK == 11 || $countCPMK == 12 || $countCPMK == 13) {
    //         $chunk_users = $users->chunk(4);
    //     } elseif ($countCPMK == 14 || $countCPMK == 15 || $countCPMK == 16) {
    //         $chunk_users = $users->chunk(2);
    //     }
    //     $html = view('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-pdf-print', [
    //         'users' => $users,
    //         'chunk_users' => $chunk_users,
    //         'jadwal' => $jadwal ?? null,
    //         'rps' => $rps,
    //         'akademik' => $tahunAkademik,

    //         'angkatan' => $angkatan ?? null,
    //         'pr_name' => $prName ?? null,
    //         'dp_name' => $dpName ?? null,
    //         'fk_name' => $fkName ?? null,

    //         'groupsCpmk' => $groupsCpmk ?? null,
    //         'totalBobotPerCpmk' => $totalBobotPerCpmk ?? null,
    //         'mapping_pertemuan' => $this->mapping_pertemuan,
    //         'univ' => $univ,
    //         'logoBase64' => $logoBase64,
    //     ])->render();

    //     $browsershot = Browsershot::html($html)
    //         ->noSandbox()
    //         ->format('A4')
    //         ->landscape()
    //         ->margins(20, 20, 20, 20)
    //         ->showBackground();

    //     if ($chromePath = env('BROWSERSHOT_CHROME_PATH')) {
    //         $browsershot->setChromePath($chromePath);
    //     }

    //     return $browsershot->pdf();
    // }

    protected function generateCpmkGrafikRawPDFContent($users, $jadwal, $rps, $groupsCpmk, $tahunAkademik, $angkatan = null, $prName = null, $dpName = null, $fkName = null): string
    {
        $logoPath = public_path('images/logo-unsri.webp');
        $univ = strtoupper(env('UNIVERSITAS'));
        $logoBase64 = '';

        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataLogo = file_get_contents($logoPath);
            $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($dataLogo);
        }
        
        $countCPMK = $rps->count_cpmk;

        if ($countCPMK == 1 || $countCPMK == 2) {
            $chunk_users = $users->chunk(17); // SUDAH
        } elseif ($countCPMK == 3) {
            $chunk_users = $users->chunk(13); // SUDAH
        } elseif ($countCPMK == 4) {
            $chunk_users = $users->chunk(10); // SUDAH
        } elseif ($countCPMK == 5) {
            $chunk_users = $users->chunk(8); // SUDAH
        } elseif ($countCPMK == 6) {
            $chunk_users = $users->chunk(7); // SUDAH
        } elseif ($countCPMK == 7) {
            $chunk_users = $users->chunk(6); // SUDAH
        } elseif ($countCPMK == 8) {
            $chunk_users = $users->chunk(5); // SUDAH
        } elseif ($countCPMK == 9 || $countCPMK == 10) {
            $chunk_users = $users->chunk(4); // SUDAH
        } elseif ($countCPMK >= 11 && $countCPMK <= 14) {
            $chunk_users = $users->chunk(3); // SUDAH
        } elseif ($countCPMK == 15 || $countCPMK == 16) {
            $chunk_users = $users->chunk(2); // SUDAH
        } else {
            $chunk_users = $users->chunk(15);
        }

        $pdf = Pdf::loadView('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-pdf-print', [
            'users' => $users,
            'chunk_users' => $chunk_users,
            'jadwal' => $jadwal ?? null,
            'rps' => $rps,
            'akademik' => $tahunAkademik,
            'angkatan' => $angkatan ?? null,
            'pr_name' => $prName ?? null,
            'dp_name' => $dpName ?? null,
            'fk_name' => $fkName ?? null,
            'groupsCpmk' => $groupsCpmk ?? null,
            'totalBobotPerCpmk' => $totalBobotPerCpmk ?? null,
            'mapping_pertemuan' => $this->mapping_pertemuan,
            'univ' => $univ,
            'logoBase64' => $logoBase64,
        ]);

        $pdf->setPaper('a4', 'landscape');
        
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'dpi' => 96,
        ]);

        return $pdf->output();
    }
}
