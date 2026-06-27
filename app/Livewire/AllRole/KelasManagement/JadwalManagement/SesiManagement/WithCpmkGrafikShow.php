<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;


use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Global\HasNilaiAbsensi;

use App\Models\Penilaian\NilaiMahasiswa;

use Spatie\Browsershot\Browsershot;

trait WithCpmkGrafikShow
{
    // public $detailRPSData = [];

    public $mapping_pertemuan;

    public function printPDFCpmkGrafik($jadwalId, $rpsId)
    {
        $queryUser = $this->inputUserSearch('mahasiswa', $jadwalId, null, 1);

        $this->addNilaiJadwalSubquery($queryUser, $jadwalId, 'mhs_nilai_array', 'nilai_array');
        $this->addNilaiJadwalSubquery($queryUser, $jadwalId, 'mhs_bobot_array', 'bobot_array');

        $this->addMahasiswaNilaiAkhir($queryUser, $jadwalId, 'mhs_nilai_akhir');
        $this->addMahasiswaNilaiIndex($queryUser, $jadwalId, 'mhs_nilai_index');
        $this->addMahasiswaNilaiMutu($queryUser, $jadwalId, 'mhs_nilai_mutu');

                    $sampleNilai = NilaiMahasiswa::where('rps_id', $rpsId)->first();

                    if (! $sampleNilai) {
                        $sampleNilai = new NilaiMahasiswa;
                        $sampleNilai->rps_id = $rpsId;
                    }
                    $mappingData = $sampleNilai->mapping_pertemuan ?? [];
                    $users = $queryUser->get();
                    if (! empty($mappingData)) {
                        $collectionMapping = collect($mappingData);

                        $totalGlobalBobotMentah = $collectionMapping->sum('bobot');
                        $totalGlobalBobotMentah = $totalGlobalBobotMentah > 0 ? $totalGlobalBobotMentah : 1;

                        $normalizedMapping = $collectionMapping->map(function ($item) use ($totalGlobalBobotMentah) {
                            $bobotMentah = $item['bobot'] ?? 0;
                            $item['bobot'] = ($bobotMentah / $totalGlobalBobotMentah) * 100;

                            return $item;
                        })->toArray();

                        $this->mapping_pertemuan = $normalizedMapping;
                        $groupsCpmk = collect($normalizedMapping)->groupBy('kode_cpmk');
                    } else {
                        $groupsCpmk = collect();
                    }


        $fileNameSafe = str_replace('/', '-', 'CPMK_Grafik.pdf');

        return response()->streamDownload(function () use ($users, $groupsCpmk) {
            echo $this->generateCpmkGrafikRawPDFContent($users, $groupsCpmk);
        }, $fileNameSafe, ['Content-Type' => 'application/pdf']);
    }

    protected function generateCpmkGrafikRawPDFContent($users, $groupsCpmk): string
    {
        $logoPath = public_path('images/logo-unsri.png');
        $logoBase64 = '';

        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataLogo = file_get_contents($logoPath);
            $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($dataLogo);
        }
        $chunks = $users->chunk(10);
        $html = view('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-pdf-print', [
            'users' => $users,
            'chunks' => $chunks,
            'groupsCpmk' => $groupsCpmk ?? null,
            'totalBobotPerCpmk' => $totalBobotPerCpmk ?? null,
            'mapping_pertemuan' => $this->mapping_pertemuan,
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
