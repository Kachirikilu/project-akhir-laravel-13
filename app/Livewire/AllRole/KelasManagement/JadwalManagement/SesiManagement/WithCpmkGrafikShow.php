<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Models\Akademik\RPS;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

trait WithCpmkGrafikShow
{
    public $mapping_pertemuan;

    public function printPDFCpmkGrafik($jadwalRPSId, $isRPS = false)
    {
        $data = $this->resolveCpmkGrafikPdf($jadwalRPSId, $isRPS);

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
            'message' => "Berhasil generate {$jumlahJadwal} file PDF untuk kelas {$kelas->kode}",
            'files' => $fileResults,
        ]);
    }

    protected function resolveCpmkGrafikPdf($jadwalRPSId, $isRPS = false)
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
        if (!$isRPS) {
            $sesiPertama = $jadwal->sesis->sortBy('tanggal_pelaksanaan')->first();
            $tahun = $sesiPertama ? Carbon::parse($sesiPertama->tanggal_pelaksanaan)->format('Y') : date('Y');
            $semester = ($sesiPertama && Carbon::parse($sesiPertama->tanggal_pelaksanaan)->format('n') >= 7) ? 'Ganjil' : 'Genap';
            $tahunAkademik = "{$semester} {$tahun}";
        }

        // Generate Content
        $pdfContent = $this->generateCpmkGrafikRawPDFContent($users, $jadwal ?? null, $rps, $groupsCpmk, $tahunAkademik ?? null);

        if ($isRPS) {
            $fileName = sprintf('%s_%s.pdf',
                str_replace(['/', '\\'], '-', $rps->mk),
                $rps->kode,
            );
        } else {
            $fileName = sprintf('%s_%s_%s_%s.pdf',
                str_replace('/', '-', $jadwal->kode),
                str_replace(['/', '\\'], '-', $rps->mk),
                $rps->kode,
                str_replace(' ', '-', $tahunAkademik)
            );
        }

        return ['content' => $pdfContent, 'name' => $fileName, 'jadwal' => $jadwal ?? null];
    }

    protected function generateCpmkGrafikRawPDFContent($users, $jadwal = null, $rps, $groupsCpmk, $tahunAkademik): string
    {
        $logoPath = public_path('images/logo-unsri.png');
        $univ = strtoupper(env('UNIVERSITAS'));
        $logoBase64 = '';

        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataLogo = file_get_contents($logoPath);
            $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($dataLogo);
        }
        $countCPMK = $rps->count_cpmk;

        if ($countCPMK == 1) {
            $chunk_users = $users->chunk(18);
        } elseif ($countCPMK == 2) {
            $chunk_users = $users->chunk(13);
        } elseif ($countCPMK == 3) {
            $chunk_users = $users->chunk(10);
        } elseif ($countCPMK == 4) {
            $chunk_users = $users->chunk(8);
        } elseif ($countCPMK == 5) {
            $chunk_users = $users->chunk(6);
        } elseif ($countCPMK == 6 || $countCPMK == 7) {
            $chunk_users = $users->chunk(5);
        } elseif ($countCPMK == 8 || $countCPMK == 9) {
            $chunk_users = $users->chunk(4);
        } elseif ($countCPMK == 10 || $countCPMK == 11 || $countCPMK == 12 || $countCPMK == 13) {
            $chunk_users = $users->chunk(4);
        } elseif ($countCPMK == 14 || $countCPMK == 15 || $countCPMK == 16) {
            $chunk_users = $users->chunk(2);
        }
        $html = view('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-pdf-print', [
            'users' => $users,
            'chunk_users' => $chunk_users,
            'jadwal' => $jadwal ?? null,
            'rps' => $rps,
            'tahun_akademik' => $tahunAkademik,
            'groupsCpmk' => $groupsCpmk ?? null,
            'totalBobotPerCpmk' => $totalBobotPerCpmk ?? null,
            'mapping_pertemuan' => $this->mapping_pertemuan,
            'univ' => $univ,
            'logoBase64' => $logoBase64,
        ])->render();

        $browsershot = Browsershot::html($html)
            ->noSandbox()
            ->format('A4')
            ->landscape()
            ->margins(20, 20, 20, 20)
            ->showBackground();

        if ($chromePath = env('BROWSERSHOT_CHROME_PATH')) {
            $browsershot->setChromePath($chromePath);
        }

        return $browsershot->pdf();
    }
}
