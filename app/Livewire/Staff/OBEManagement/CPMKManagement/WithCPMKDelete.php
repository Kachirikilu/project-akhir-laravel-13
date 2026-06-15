<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;

trait WithCPMKDelete
{
    use HasToast;

    public $showCPMKDelete = false;
    public $cpmkIdToDelete;
    public $cpmkNamaToDelete;
    public $cpmkKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteCPMK($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $cpmk = $isTrashed ? CPMK::withTrashed()->find($id) : CPMK::find($id);

        if (!$cpmk) {
            $this->toast(message: 'CPMK', type: 'unfound', variant: 'warning');
            return;
        }

        $this->cpmkIdToDelete = $id;
        $this->cpmkNamaToDelete = 'CPMK '.$cpmk->kode;
        $this->cpmkKodeToDelete = $cpmk->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showCPMKDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyCPMK()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->cpmkIdToDelete) return;

        $type = 'delete';

        try {
            $cpmk = CPMK::withTrashed()->findOrFail($this->cpmkIdToDelete);

            if ($this->isPermanentDelete) {
                // Safety: RPS is_draf = 0
                $isConnected = $cpmk->rps()
                    ->where('is_draf', 0)
                    ->exists();

                if ($isConnected) {
                    throw new \Exception('Gagal hapus permanen: CPMK masih terhubung ke RPS yang sudah Aktif!');
                }

                $type = 'permanent';
                $cpmk->forceDelete();
            } else {
                $cpmk->delete();
            }

            $this->toast(message: $this->cpmkNamaToDelete, type: $type);
            $this->cleanupDeleteStateCPMK();
            $this->dispatch('refresh-data-cpmk'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-cpmk');
            $this->showCPMKDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE CPMK
     */
    public function restoreCPMK($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $cpmk = CPMK::withTrashed()->findOrFail($id);
            $cpmk->restore();

            $this->toast(message: 'CPMK '.$cpmk->kode, type: 'recycle');
            $this->dispatch('refresh-data-cpmk');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-cpmk');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateCPMK()
    {
        $this->cpmkIdToDelete = null;
        $this->cpmkNamaToDelete = null;
        $this->cpmkKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showCPMKDelete = false;
    }
}