<?php

namespace App\Livewire\Global;
use Illuminate\Support\Facades\Auth;

trait HasToast
{
    public function toast(
        ?string $text = null,
        ?string $message = null,
        string $type = 'save',
        string $variant = 'success',
        ?string $heading = null,
        int $duration = null,
        bool $clearable = true,
        bool $isAkun = false
    ) {
        if ($duration == null) {
            if ($variant == 'danger') {
                $duration = 12000;
            } elseif ($variant == 'warning') {
                $duration = 6000;
            } else {
                $duration = 4000;
            }
        }
        // Normalisasi variant ke standar Flux (danger)
        $finalVariant = ($variant === 'error' || $variant === 'danger') ? 'danger' : $variant;

        // Pastikan heading dan text terisi otomatis jika null
        $finalHeading = $heading ?? $this->getAutoHeading($type, $finalVariant);
        $finalText = $text ?? $this->getAutoText($type, $message ?? 'Data', $finalVariant, $isAkun);

        $options = [
            'variant' => $finalVariant,
            'heading' => $finalHeading,
            'text' => $finalText,
            'duration' => $duration,
            'clearable' => $clearable,
        ];

        // Gunakan json_encode agar semua karakter spesial aman saat dikirim ke JS
        $this->js('Flux.toast('.json_encode($options).')');
    }

    private function getAutoHeading(string $type, string $variant): string
    {
        if ($variant === 'danger') {
            return 'Gagal!';
        }
        if ($variant === 'warning') {
            return 'Peringatan!';
        }

        return match ($type) {
            'save' => 'Simpan Berhasil!',
            'update' => 'Update Berhasil!',
            'recycle' => 'Recycle Berhasil!',
            'delete' => 'Berhasil Dibuang!',
            'permanent' => 'Hapus Permanen!',
            default => 'Berhasil!',
        };
    }

    private function getAutoText(string $type, string $message, string $variant, bool $isAkun): string
    {
        $dataL = 'Data';
        $dataS = 'data';
        if ($isAkun) {
            $dataL = 'Akun';
            $dataS = 'akun';
        }
        if ($variant === 'danger') {
            return match ($type) {
                'save' => "Gagal menyimpan $dataS $message!",
                'update' => "Gagal memperbarui $dataS $message!",
                'recycle' => "Gagal mengembalikan $dataS $message!",
                'delete' => "Gagal membuang $dataS $message!",
                'permanent' => "Gagal menghapus permanen $dataS $message!",
                default => "Terjadi kesalahan pada $dataS $message!",
            };
        }

        if ($variant === 'warning') {
            return match ($type) {
                'auth' => "Hanya $message yang dapat akses!",
                'unfound' => "$dataL $message tidak ditemukan!",
                // 'permanent' => "Perhatian! $dataS $message tidak bisa dipulihkan setelah ini!",
                default => "Mohon periksa kembali $dataS $message.",
            };
        }

        return match ($type) {
            'save' => "$dataL $message telah disimpan!",
            'update' => "Perubahan $dataS $message telah disimpan!",
            'recycle' => "$dataL $message berhasil dikembalikan!",
            'delete' => "$dataL $message dipindahkan ke sampah!",
            'permanent' => "$dataL $message dihapus selamanya!",
            default => "Aksi $message berhasil!",
        };
    }

    public function AuthCheck($role = 'admin'): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();
        $isAuthorized = true;
        $message = '';

        match ($role) {
            'admin' => [
                $isAuthorized = (bool) $user->admin,
                $message = 'Admin',
            ],
            'staff' => [
                $isAuthorized = ($user->admin || $user->dosen),
                $message = 'Admin & Dosen',
            ],
            'dosen' => [
                $isAuthorized = (bool) $user->dosen,
                $message = 'Dosen',
            ],
            'akademik' => [
                $isAuthorized = ($user->dosen || $user->mahasiswa),
                $message = 'Dosen & Mahasiswa',
            ],
            'mahasiswa' => [ // Ganti 'akademik' kedua jadi 'mahasiswa'
                $isAuthorized = (bool) $user->mahasiswa,
                $message = 'Mahasiswa',
            ],
            default => $isAuthorized = false,
        };

        if (! $isAuthorized) {
            $this->toast(
                message: $message ?: ucfirst($role),
                type: 'auth',
                variant: 'warning'
            );

            return false;
        }

        return true;
    }

    public function normalizeNama($value)
    {
        return ucwords(strtolower(trim($value)));
    }

    public function normalizeText($value) {
        $input = trim($value ?? '');
        if (! str_ends_with($input, '.') && ! empty($input)) {
            $input .= '.';
        }
        return $input;
    }

}
