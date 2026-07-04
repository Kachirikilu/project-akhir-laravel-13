<?php

namespace App\Livewire\Staff\OBEManagement\CPLManagement;

use App\Models\Akademik\CPL;
// use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithCPLDelete
{
    use HasToast;

    public $showCPLDelete = false;
    public $cplIdToDelete;
    public $cplNamaToDelete;
    public $cplKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteCPL($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $cpl = $isTrashed ? CPL::withTrashed()->find($id) : CPL::find($id);

        if (!$cpl) {
            $this->toast(message: 'CPL', type: 'unfound', variant: 'warning');
            return;
        }

        $this->cplIdToDelete = $id;
        $this->cplNamaToDelete = 'CPL '.$cpl->kode;
        $this->cplKodeToDelete = $cpl->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showCPLDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyCPL()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->cplIdToDelete) return;

        $type = 'delete';

        try {
            $cpl = CPL::withTrashed()->findOrFail($this->cplIdToDelete);

            if ($this->isPermanentDelete) {
                // Safety Check: RPS is_draf = 0
                $isConnected = $cpl->cpmks()
                    ->whereHas('rps', function ($q) {
                        $q->where('is_draf', 0);
                    })
                    ->exists();

                if ($isConnected) {
                    throw new \Exception('Gagal hapus permanen: CPL masih terhubung ke RPS yang sudah Aktif!');
                }

                $type = 'permanent';
                $cpl->prodis()->detach();
                $cpl->forceDelete();
            } else {
                $cpl->delete();
            }

            $this->toast(message: $this->cplNamaToDelete, type: $type);
            $this->cleanupDeleteStateCPL();
            $this->dispatch('refresh-data-cpl'); 
            $this->dispatch('refresh-stats-cpl'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-cpl');
            $this->showCPLDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE CPL
     */
    #[On('restore-cpl')]
    public function restoreCPL($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $cpl = CPL::withTrashed()->findOrFail($id);
            $cpl->restore();

            $this->toast(message: 'CPL '.$cpl->kode, type: 'recycle');
            $this->dispatch('refresh-data-cpl');
            $this->dispatch('refresh-stats-cpl'); 
        } catch (\Exception $e) {
            $this->dispatch('refresh-data-cpl');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateCPL()
    {
        $this->cplIdToDelete = null;
        $this->cplNamaToDelete = null;
        $this->cplKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showCPLDelete = false;
    }
}