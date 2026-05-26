<?php

namespace App\Livewire\Staff\CPMKManagement;

use App\Models\Akademik\SubCPMK;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;

trait WithSubCPMKDelete
{
    use HasToast;

    public $showSCPMKDelete = false;
    public $scpmkIdToDelete;
    public $scpmkNamaToDelete;
    public $scpmkKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteSCPMK($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $sub = $isTrashed ? SubCPMK::withTrashed()->find($id) : SubCPMK::find($id);

        if (!$sub) {
            $this->toast(message: 'Sub-CPMK', type: 'unfound', variant: 'warning');
            return;
        }

        $this->scpmkIdToDelete = $id;
        $this->scpmkNamaToDelete = $sub->kode;
        $this->scpmkKodeToDelete = $sub->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showSCPMKDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroySCPMK()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->scpmkIdToDelete) return;

        $type = 'delete';

        try {
            $sub = SubCPMK::withTrashed()->findOrFail($this->scpmkIdToDelete);

            if ($this->isPermanentDelete) {
                // Safety: RPS is_draf = 0 (SubCPMK -> CPMK -> RPS)
                $isConnected = RPS::whereHas('cpmks.scpmks', function($q) use ($sub) {
                    $q->where('scpmk_id', $sub->id);
                })->where('is_draf', 0)->exists();

                if ($isConnected) {
                    throw new \Exception('Gagal hapus permanen: Sub-CPMK masih terhubung ke RPS yang sudah Aktif!');
                }

                $type = 'permanent';
                $sub->forceDelete();
            } else {
                $sub->delete();
            }

            $this->toast(message: $this->scpmkNamaToDelete, type: $type);
            $this->cleanupDeleteStateSCPMK();
            $this->dispatch('refresh-data-scpmk'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-scpmk');
            $this->showSCPMKDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE SUB-CPMK
     */
    public function restoreSCPMK($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $sub = SubCPMK::withTrashed()->findOrFail($id);
            $sub->restore();

            $this->toast(message: $sub->kode, type: 'recycle');
            $this->dispatch('refresh-data-scpmk');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-scpmk');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateSCPMK()
    {
        $this->scpmkIdToDelete = null;
        $this->scpmkNamaToDelete = null;
        $this->scpmkKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showSCPMKDelete = false;
    }
}