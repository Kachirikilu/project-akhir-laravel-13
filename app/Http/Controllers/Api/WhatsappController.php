<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\WhatsappController\WithDeactivation;
use App\Http\Controllers\Api\WhatsappController\WithKelas;
use App\Http\Controllers\Api\WhatsappController\WithDataDiri;
use App\Http\Controllers\Api\WhatsappController\WithVerification;
use App\Http\Controllers\Api\WhatsappController\WithNilaiMahasiswa;
use App\Http\Controllers\Api\WhatsappController\WithExcelNilai;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappController extends Controller
{
    use WithDeactivation;
    use WithKelas;
    use WithDataDiri;
    use WithVerification;
    use WithNilaiMahasiswa;
    use WithExcelNilai;

    public $verifyKey = ['VERIFIKASI', 'VERIFY', 'VERYFICATION', 'IDENTITAS', 'DAFTAR', 'REGISTRASI', 'LOGIN', 'LOG IN', 'MASUK'];

    public $dataDiriKey = ["DATA DIRI", "MY SELF", "DATA", "AUTH", "AKUN", "USER", "TOKEN", "CHECK TOKEN", "CEK TOKEN", "INFORMASI TOKEN", "LIMTI", "CHECK LIMIT", "LIMIT",];

    public $updateTokenKey = ["ISI TOKEN", "PERBARUI TOKEN", "TOKEN BARU", "RESET TOKEN", "TAMBAH TOKEN", "PERBARUI LIMIT", "UPDATE TOKEN", "TOKEN UPDATE",];

    public $deactivateKey = ['NONAKTIFKAN', 'MATIKAN', 'DIAM', 'SILENT', 'LOGOUT', 'KELUAR', 'LOG OUT', 'MUTE', 'MIC MATI'];

    public $kelasKey = ['KELAS HARI INI',  'KELAS MINGGU INI', 'KELAS HARI',  'KELAS MINGGU',  'CHECK KELAS',  'KELAS SAYA',  'KELAS', 'JADWAL KELAS', 'JADWAL', 'KELAS JADWAL'];

    public $kelasMingguKey = ['MINGGU INI', 'KELAS MINGGU', 'JADWAL KELAS', 'KELAS JADWAL', 'JADWAL'];

    public $kelasHariKey = ['HARI INI', 'KELAS HARI'];

    public $excelNilaiKey = ['EXCEL NILAI', 'INPUT NILAI', 'UPLOAD NILAI', 'INPUT FILE NILAI', 'FILE NILAI'];

    public $nilaiMahasiswaKey = ['NILAI', 'NILAI SAYA', 'LIHAT NILAI', 'NILAI SEMESTER'];

    private function isTrigger(string $pesan, array $key): bool
    {
        $pesanUpper = strtoupper($pesan);
        // return in_array($pesanUpper, $key);
        return Str::startsWith($pesanUpper, $key);
    }

    public function handleIncomingChat(Request $request)
    {
        Log::info('=== ADA DATA MASUK DARI NODE.JS ===', $request->all());

        if ($request->bearerToken() !== config('services.nodejs.token')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized!'], 401);
        }

        $noWA = preg_replace('/[^0-9]/', '', $request->input('whatsapp_number'));
        $nameWA = $request->input('whatsapp_name');
        $pesan = trim($request->input('whatsapp_message'));

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

        if ($this->isTrigger($pesan, $this->kelasKey)) {
            return $this->processKelas($noWA, $nameWA, $pesan, $this->kelasMingguKey, $this->kelasHariKey);
        }

        if ($this->isTrigger($pesan, $this->nilaiMahasiswaKey)) {
            return $this->processNilaiMahasiswa($noWA, $nameWA, $pesan);
        }

        if ($this->isTrigger($pesan, $this->excelNilaiKey)) {
            return $this->processGateAwayExcelNilai($noWA, $nameWA, $pesan);
        }
        if ($request->hasFile('excel_file')) {
            return $this->processExcelNilai($noWA, $nameWA, $pesan, $request);
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
