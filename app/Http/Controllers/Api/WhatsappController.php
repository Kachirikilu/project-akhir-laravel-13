<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\WhatsappController\WithDataDiri;
use App\Http\Controllers\Api\WhatsappController\WithDeactivation;
use App\Http\Controllers\Api\WhatsappController\WithExcelCapaianNilai;
use App\Http\Controllers\Api\WhatsappController\WithKelas;
use App\Http\Controllers\Api\WhatsappController\WithRPS;
use App\Http\Controllers\Api\WhatsappController\WithNilaiMahasiswa;
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
    use WithExcelCapaianNilai;
    use WithKelas;
    use WithRPS;
    use WithNilaiMahasiswa;
    use WithVerification;

    public $verifyKey = ['VERIFIKASI', 'VERIFY', 'VERYFICATION', 'IDENTITAS', 'REGISTRASI', 'LOGIN', 'LOG IN', 'MASUK'];

    public $dataDiriKey = ['DATA DIRI', 'MY SELF', 'DATA', 'AUTH', 'AKUN', 'USER', 'TOKEN', 'CHECK TOKEN', 'CEK TOKEN', 'INFORMASI TOKEN', 'LIMTI', 'CHECK LIMIT', 'LIMIT'];

    public $updateTokenKey = ['ISI TOKEN', 'PERBARUI TOKEN', 'TOKEN BARU', 'RESET TOKEN', 'TAMBAH TOKEN', 'PERBARUI LIMIT', 'UPDATE TOKEN', 'TOKEN UPDATE'];

    public $deactivateKey = ['NONAKTIFKAN', 'MATIKAN', 'DIAM', 'SILENT', 'LOGOUT', 'KELUAR', 'LOG OUT', 'MUTE', 'MIC MATI'];

    public $kelasKey = ['LIST KELAS', 'SEMUA KELAS', 'DAFTAR KELAS', 'KELAS HARI INI',  'KELAS MINGGU INI', 'KELAS HARI',  'KELAS MINGGU',  'CHECK KELAS',  'KELAS SAYA',  'KELAS', 'JADWAL KELAS', 'JADWAL', 'KELAS JADWAL'];

    public $daftarKelasKey = ['LIST KELAS', 'SEMUA KELAS', 'DAFTAR KELAS'];

    public $kelasMingguKey = ['MINGGU INI', 'KELAS MINGGU', 'JADWAL KELAS', 'KELAS JADWAL', 'JADWAL'];

    public $kelasHariKey = ['HARI INI', 'KELAS HARI'];

    public $excelNilaiKey = ['EXCEL NILAI',
        'INPUT NILAI EXCEL',
        'UPLOAD NILAI EXCEL',
        'INPUT FILE NILAI',
        'FILE NILAI'
    ];

    public $excelGetNilaiKey = ['DOWNLOAD NILAI', 'DOWNLOAD EXCEL NILAI', 'GET EXCEL NILAI', 'GET NILAI', 'DOWNLOAD EXCEL NILAI'];

    public $pdfGetCapaianKey = ['DOWNLOAD CAPAIAN', 'DOWNLOAD PDF CAPAIAN', 'GET PDF CAPAIAN', 'GET CAPAIAN', 'DOWNLOAD PDF CAPAIAN', 'PRINT CAPAIAN', 'PRINT PDF CAPAIAN', 'PRINT CAPAIAN PDF',
                                'DOWNLOAD CPMK', 'DOWNLOAD PDF CPMK', 'GET PDF CPMK', 'GET CPMK', 'DOWNLOAD PDF CPMK', 'PRINT CPMK', 'PRINT PDF CPMK', 'PRINT CPMK PDF',
                                'DOWNLOAD CPL', 'DOWNLOAD PDF CPL', 'GET PDF CPL', 'GET CPL', 'DOWNLOAD PDF CPL', 'PRINT CPL', 'PRINT PDF CPL', 'PRINT CPL PDF',
                            ];

    public $nilaiMahasiswaKey = ['NILAI', 'NILAI SAYA', 'LIHAT NILAI', 'NILAI SEMESTER'];

    public $rpsKey = ['LIST RPS', 'SEMUA RPS', 'DAFTAR RPS', 'RPS SAYA',  'RPS'];

    public $daftarRPSKey = ['LIST RPS', 'SEMUA RPS', 'DAFTAR RPS'];

    public $pdfGetRPSKey = ['DOWNLOAD RPS', 'DOWNLOAD PDF RPS', 'GET PDF RPS', 'GET RPS', 'DOWNLOAD PDF RPS', 'PRINT RPS', 'PRINT PDF RPS', 'PRINT RPS PDF'];

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
