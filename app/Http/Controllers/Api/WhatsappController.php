<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\WhatsappController\WithDataDiri;
use App\Http\Controllers\Api\WhatsappController\WithDeactivation;
use App\Http\Controllers\Api\WhatsappController\WithCapaianNilai;
use App\Http\Controllers\Api\WhatsappController\WithKelas;
use App\Http\Controllers\Api\WhatsappController\WithNilaiMahasiswa;
use App\Http\Controllers\Api\WhatsappController\WithRPS;
use App\Http\Controllers\Api\WhatsappController\WithVerification;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappController extends Controller
{
    use WithDataDiri;
    use WithDeactivation;
    use WithCapaianNilai;
    use WithKelas;
    use WithNilaiMahasiswa;
    use WithRPS;
    use WithVerification;

    public array $verifyKey;

    public array $dataDiriKey;

    public array $updateTokenKey;

    public array $deactivateKey;

    public array $daftarKelasKey;

    public array $kelasMingguKey;

    public array $kelasHariKey;

    public array $kelasKey;

    public array $excelNilaiKey;

    public array $excelGetNilaiKey;

    public array $pdfGetCapaianKey;

    public array $nilaiMahasiswaKey;

    public array $daftarRPSKey;

    public array $rpsKey;

    public array $pdfGetRPSKey;

    public function __construct()
    {
        // 1. Keyword Sederhana / Spesifik
        $this->verifyKey = config('wa.keywords.verify');
        $this->dataDiriKey = config('wa.keywords.data_diri');
        $this->updateTokenKey = config('wa.keywords.update_token');
        $this->deactivateKey = config('wa.keywords.deactivate');

        // 2. Keyword Kelas & Jadwal (Penggabungan Elemen Terkait)
        $this->daftarKelasKey = config('wa.keywords.daftar_kelas');
        $this->kelasMingguKey = config('wa.keywords.kelas_minggu');
        $this->kelasHariKey = config('wa.keywords.kelas_hari');

        // $kelasKey gabungan dari $daftarKelasKey + $kelasMingguKey + $kelasHariKey + $kelasExtra
        $this->kelasKey = array_values(array_unique(array_merge(
            $this->daftarKelasKey,
            $this->kelasHariKey,
            $this->kelasMingguKey,
            config('wa.keywords.kelas_extra')
        )));

        // 3. Keyword Excel & Capaian
        $this->excelNilaiKey = config('wa.keywords.excel_nilai');
        $this->excelGetNilaiKey = array_values(array_unique(config('wa.keywords.excel_get_nilai')));
        $this->pdfGetCapaianKey = array_values(array_unique(config('wa.keywords.pdf_get_capaian')));
        $this->nilaiMahasiswaKey = config('wa.keywords.nilai_mahasiswa');

        // 4. Keyword RPS (Penggabungan Elemen Terkait)
        $this->daftarRPSKey = config('wa.keywords.daftar_rps');

        // $rpsKey gabungan dari $daftarRPSKey + $rpsExtra
        $this->rpsKey = array_values(array_unique(array_merge(
            $this->daftarRPSKey,
            config('wa.keywords.rps_extra')
        )));

        $this->pdfGetRPSKey = array_values(array_unique(config('wa.keywords.pdf_get_rps')));
    }

    private function isTrigger(string $pesan, array $key): bool
    {
        $pesanUpper = strtoupper($pesan);

        return Str::startsWith($pesanUpper, $key);
    }

    private function isTriggerPas(string $pesan, array $key): bool
    {
        $pesanBersih = trim(strtoupper($pesan));
        $keyUpper = array_map('strtoupper', $key);

        return in_array($pesanBersih, $keyUpper);
    }

    // private function isTrigger(string $pesan, array $key): bool
    // {
    //     $pesanUpper = trim(strtoupper($pesan));

    //     foreach ($key as $trigger) {
    //         $triggerUpper = strtoupper($trigger);
    //         if ($pesanUpper === $triggerUpper || str_starts_with($pesanUpper, $triggerUpper . ' ')) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    public function handleIncomingChat(Request $request)
    {
        Log::info('=== ADA DATA MASUK DARI NODE.JS ===', $request->all());

        if ($request->bearerToken() !== config('services.nodejs.token')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized!'], 401);
        }

        $noWA = preg_replace('/[^0-9]/', '', $request->input('whatsapp_number'));
        $nameWA = $request->input('whatsapp_name');
        $pesan = trim($request->input('whatsapp_message'));

        if ($this->isTrigger($pesan, $this->kelasKey)) {
            return $this->processKelas($noWA, $nameWA, $pesan, $this->daftarKelasKey, $this->kelasMingguKey, $this->kelasHariKey);
        }
        if ($this->isTrigger($pesan, $this->verifyKey)) {
            return $this->processVerification($noWA, $nameWA, $pesan);
        }
        if ($this->isTrigger($pesan, $this->dataDiriKey)) {
            return $this->processDataDiri($noWA, $nameWA, $pesan);
        }
        if ($this->isTrigger($pesan, $this->updateTokenKey)) {
            return $this->processResetToken($noWA, $nameWA, $pesan);
        }
        if ($this->isTrigger($pesan, $this->deactivateKey)) {
            return $this->processDeactivateToken($noWA, $nameWA, $pesan);
        }

        if ($this->isTrigger($pesan, $this->nilaiMahasiswaKey)) {
            return $this->processNilaiMahasiswa($noWA, $nameWA, $pesan);
        }

        if ($this->isTrigger($pesan, $this->excelGetNilaiKey)) {
            return $this->processGetExcelNilai($noWA, $nameWA, $pesan, $this->excelGetNilaiKey);
        }
        if ($this->isTrigger($pesan, $this->pdfGetCapaianKey)) {
            return $this->processGetCapaianNilai($noWA, $nameWA, $pesan, $this->pdfGetCapaianKey);
        }
        if ($this->isTriggerPas($pesan, $this->excelNilaiKey)) {
            return $this->processGateAwayExcelNilai($noWA, $nameWA, $pesan);
        }
        if ($request->hasFile('excel_file')) {
            return $this->processExcelNilai($noWA, $nameWA, $pesan, $request);
        }

        if ($this->isTrigger($pesan, $this->rpsKey)) {
            return $this->processRPS($noWA, $nameWA, $pesan, $this->daftarRPSKey);
        }
        if ($this->isTrigger($pesan, $this->pdfGetRPSKey)) {
            return $this->processGetPDFRPS($noWA, $nameWA, $pesan, $this->pdfGetRPSKey);
        }

        return response()->json(['status' => false, 'message' => 'Perintah tidak dikenali!'], 400);
    }

    public function searchUserWhatsApp(string $sufiksNomor, bool $waActive = true)
    {
        return User::where(function ($mainQuery) use ($sufiksNomor, $waActive) {
            $mainQuery->whereHas('mahasiswa', function ($query) use ($sufiksNomor, $waActive) {
                $query->where('no_hp', 'LIKE', '%'.$sufiksNomor)->where('is_wa_active', $waActive);
            })
                ->orWhereHas('dosen', function ($query) use ($sufiksNomor, $waActive) {
                    $query->where('no_hp', 'LIKE', '%'.$sufiksNomor)->where('is_wa_active', $waActive);
                })
                ->orWhereHas('admin', function ($query) use ($sufiksNomor, $waActive) {
                    $query->where('no_hp', 'LIKE', '%'.$sufiksNomor)->where('is_wa_active', $waActive);
                });
        })->first();
    }

    public function hasProperty($property)
    {
        return property_exists($this, $property);
    }
}
