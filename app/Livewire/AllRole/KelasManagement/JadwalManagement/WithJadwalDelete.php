<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasToast;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

trait WithJadwalDelete
{
    use HasToast;

    public $showJadwalDelete = false;

    public $jadwalIdToDelete;

    public $jadwalNameToDelete;

    public $isPermanentDelete = false;

    public function deleteJadwal($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $jadwal = $isTrashed ? KelasJadwal::withTrashed()->find($id) : KelasJadwal::find($id);

        if (! $jadwal) {
            $this->toast(type: 'unfound', variant: 'warning', isAkun: 1);
            return;
        }

        $this->jadwalIdToDelete = $id;
        $this->jadwalNameToDelete = $jadwal->kode;
        $this->isPermanentDelete = $isTrashed;
        $this->showJadwalDelete = true;
    }

    public function destroyJadwal()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (! $this->jadwalIdToDelete) {
            return;
        }

        $type = 'delete';

        try {
            $jadwal = KelasJadwal::withTrashed()->findOrFail($this->jadwalIdToDelete);

            // $compositeKey = $jadwal->kode;
            // $jadwalId = $this->jadwalIdToDelete;

            if ($this->isPermanentDelete) {
                $this->checkJadwalSafety($jadwal);

                $type = 'permanent';
                $jadwal->forceDelete();
            } else {
                $jadwal->delete();
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

            $this->dispatch('refresh-data-jadwal');
            // $this->dispatch('refresh-layout-sidebar');

            $this->showJadwalDelete = false;
            $this->toast(message: $this->jadwalNameToDelete, type: $type);
            $this->cleanupDeleteStateJadwal();

            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-jadwal');
            $this->showJadwalDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function checkJadwalSafety($jadwal)
    {
        if ($jadwal->mahasiswas()->exists()) {
            throw new \Exception('Gagal hapus permanen: Jadwal Kelas masih memiliki Mahasiswa!');
        }
    }

    #[On('restore-jadwal')]
    public function restoreJadwal($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $jadwal = KelasJadwal::withTrashed()->findOrFail($id);
            $jadwal->restore();

            $this->dispatch('refresh-data-jadwal');
            $this->toast(message: $jadwal->email, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-jadwal');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateJadwal()
    {
        $this->jadwalIdToDelete = null;
        $this->jadwalNameToDelete = null;
        $this->isPermanentDelete = false;
        $this->showJadwalDelete = false;
    }
}
