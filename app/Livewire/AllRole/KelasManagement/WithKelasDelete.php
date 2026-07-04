<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Global\HasToast;
use App\Models\Kelas\Kelas;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

trait WithKelasDelete
{
    use HasToast;

    public $showKelasDelete = false;

    public $kelasIdToDelete;

    public $kelasNameToDelete;

    public $isPermanentDelete = false;

    public function deleteKelas($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $kelas = $isTrashed ? Kelas::withTrashed()->find($id) : Kelas::find($id);

        if (! $kelas) {
            $this->toast(type: 'unfound', variant: 'warning', isAkun: 1);

            return;
        }

        if (Auth::id() === $kelas->id) {
            $this->toast(text: 'Anda tidak dapat menghapus Akun sendiri!', variant: 'warning');

            return;
        }

        $this->kelasIdToDelete = $id;
        $this->kelasNameToDelete = $kelas->kode;
        $this->isPermanentDelete = $isTrashed;
        $this->showKelasDelete = true;
    }

    public function destroyKelas()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (! $this->kelasIdToDelete) {
            return;
        }

        $type = 'delete';

        try {
            $kelas = Kelas::withTrashed()->findOrFail($this->kelasIdToDelete);

            // $compositeKey = $kelas->kode;
            // $kelasId = $this->kelasIdToDelete;

            if ($this->isPermanentDelete) {
                $this->checkKelasSafety($kelas);

                $type = 'permanent';
                $kelas->forceDelete();
            } else {
                $kelas->delete();
            }

            // foreach (['kelas.history', 'kelas_mahasiswa.history'] as $key) {
            //     $history = session($key, []);
            //     $historyBefore = count($history);
            //     $history = array_filter($history, function ($item) use ($kelasId) {
            //         return data_get($item, 'kelas_id') != $kelasId;
            //     });

            //     if (count($history) !== $historyBefore) {
            //         session([$key => $history]);
            //     }
            // }

            $this->dispatch('refresh-data-kelas');
            $this->dispatch('refresh-stats-kelas');
            // $this->dispatch('refresh-layout-sidebar');

            $this->showKelasDelete = false;
            $this->toast(message: $this->kelasNameToDelete, type: $type);
            $this->cleanupDeleteStateKelas();

            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-kelas');
            $this->showKelasDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function checkKelasSafety($kelas)
    {
        if ($kelas->jadwals()->exists()) {
            throw new \Exception('Gagal hapus permanen: Kelas masih memiliki Jadwal Kelas!');
        }
    }

    #[On('restore-kelas')]
    public function restoreKelas($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $kelas = Kelas::withTrashed()->findOrFail($id);
            $kelas->restore();

            $this->dispatch('refresh-data-kelas');
            $this->dispatch('refresh-stats-kelas');
            $this->toast(message: $kelas->kode, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-kelas');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateKelas()
    {
        $this->kelasIdToDelete = null;
        $this->kelasNameToDelete = null;
        $this->isPermanentDelete = false;
        $this->showKelasDelete = false;
    }
}
