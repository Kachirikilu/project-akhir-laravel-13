<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

trait WithUserDelete
{
    use HasToast;

    public $showUserDelete = false;

    public $userIdToDelete;

    public $userEmailToDelete;

    public $isPermanentDelete = false;

    public function deleteUser($id, $isTrashed = false)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $user = $isTrashed ? User::withTrashed()->find($id) : User::find($id);

        if (! $user) {
            $this->toast(type: 'unfound', variant: 'warning', isAkun: 1);

            return;
        }

        if (Auth::id() === $user->id) {
            $this->toast(text: 'Anda tidak dapat menghapus Akun sendiri!', variant: 'warning');

            return;
        }

        $this->userIdToDelete = $id;
        $this->userEmailToDelete = $user->email;
        $this->isPermanentDelete = $isTrashed;
        $this->showUserDelete = true;
    }

    public function destroyUser()
    {
        if (! $this->AuthCheck()) {
            return;
        }

        if (! $this->userIdToDelete) {
            return;
        }

        $type = 'delete';

        try {
            $user = User::withTrashed()->findOrFail($this->userIdToDelete);

            if ($this->isPermanentDelete) {
                $this->checkUserSafety($user);

                $type = 'permanent';
                $user->forceDelete();
            } else {
                $user->delete();
            }

            $this->dispatch('refresh-data-user');
            $this->dispatch('refresh-stats-user');
            $this->showUserDelete = false;
            $this->toast(message: $this->userEmailToDelete, type: $type, isAkun: true);
            $this->cleanupDeleteStateUser();

            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-user');
            $this->showUserDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function checkUserSafety($user)
    {
        // 1. Cek Dosen
        if ($user->dosen) {
            $dosen = $user->dosen;

            if ($dosen->tim_dosens()->whereHas('rps')->exists()) {
                throw new \Exception('Gagal hapus permanen: User (Dosen) masih terhubung ke data RPS!');
            }

            // if ($dosen->scpmks()->exists()) {
            //     throw new \Exception('Gagal hapus permanen: User (Dosen) masih terhubung ke data Sub-CPMK!');
            // }

            // if ($dosen->sesiMengajars()->exists()) {
            //     throw new \Exception('Gagal hapus permanen: User (Dosen) masih terhubung ke jadwal Kelas!');
            // }
        }

        // 2. Cek Mahasiswa
        if ($user->mahasiswa) {
            if ($user->mahasiswa?->jadwals()->exists()) {
                throw new \Exception(
                    'Gagal hapus permanen: User (Mahasiswa) masih terhubung ke data Kelas!'
                );
            }
        }
    }

    #[On('restore-user')]
    public function restoreUser($id)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            $this->dispatch('refresh-data-user');
            $this->dispatch('refresh-stats-user');
            $this->toast(message: $user->email, type: 'recycle', isAkun: true);

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-user');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateUser()
    {
        $this->userIdToDelete = null;
        $this->userEmailToDelete = null;
        $this->isPermanentDelete = false;
        $this->showUserDelete = false;
    }
}
