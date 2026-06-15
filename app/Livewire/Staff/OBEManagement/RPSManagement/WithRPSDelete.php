<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;


use App\Models\Akademik\RPS;
use App\Models\Kelas\Kelas;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;

trait WithRPSDelete
{
    use HasToast;

    public $showRPSDelete = false;
    public $rpsIdToDelete;
    public $rpsNamaToDelete;
    public $rpsKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteRPS($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $rps = $isTrashed ? RPS::withTrashed()->find($id) : RPS::find($id);

        if (!$rps) {
            $this->toast(message: 'RPS', type: 'unfound', variant: 'warning');
            return;
        }

        $this->rpsIdToDelete = $id;
        $this->rpsNamaToDelete = $rps->rps;
        $this->rpsKodeToDelete = $rps->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showRPSDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyRPS()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->rpsIdToDelete) return;

        $type = 'delete';

        try {
            $rps = RPS::withTrashed()->findOrFail($this->rpsIdToDelete);

            if ($this->isPermanentDelete) {
                // Safety Check: Kelas
                if (Kelas::where('rps_id', $rps->id)->exists()) {
                    throw new \Exception('Gagal hapus permanen: RPS masih terhubung ke data Kelas!');
                }

                $type = 'permanent';
                $rps->forceDelete();
            } else {
                $rps->delete();
            }

            $this->toast(message: $this->rpsNamaToDelete, type: $type);
            $this->cleanupDeleteStateRPS();
            $this->dispatch('refresh-data-rps'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-rps');
            $this->showRPSDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE RPS
     */
    public function restoreRPS($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $rps = RPS::withTrashed()->findOrFail($id);
            $rps->restore();

            $this->toast(message: $rps->rps, type: 'recycle');
            $this->dispatch('refresh-data-rps');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-rps');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateRPS()
    {
        $this->rpsIdToDelete = null;
        $this->rpsNamaToDelete = null;
        $this->rpsKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showRPSDelete = false;
    }
}