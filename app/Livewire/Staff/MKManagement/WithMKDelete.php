<?php

namespace App\Livewire\Staff\MKManagement;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithMKDelete
{
    use HasToast;
    public $showMKDelete = false;
    public $mkIdToDelete;
    public $mkNamaToDelete;
    public $mkKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteMK($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
        $mk = $isTrashed ? MataKuliah::withTrashed()->find($id) : MataKuliah::find($id);

        if (!$mk) {
            $this->toast(message: 'Mata Kuliah', type: 'unfound', variant: 'warning');
            return;
        }

        $this->mkIdToDelete = $id;
        $this->mkNamaToDelete = $mk->mk;
        $this->mkKodeToDelete = $mk->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showMKDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyMK()
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
       if (!$this->mkIdToDelete) return;

        $type = 'delete';

        try {
            $mk = MataKuliah::withTrashed()->findOrFail($this->mkIdToDelete);

            if ($this->isPermanentDelete) {
                if ($mk->rps()->exists()) {
                    throw new \Exception('Gagal hapus permanen: Mata Kuliah masih memiliki RPS!');
                }

                DB::transaction(function () use ($mk) {
                    $mk->prodis()->detach();
                    $mk->forceDelete();
                });
                $type = 'permanent';
            } else {
                $mk->delete();
            }

            $this->toast(message: 'Mata Kuliah ' . $this->mkNamaToDelete, type: $type);

            $this->cleanupDeleteStateMK();
            $this->dispatch('refresh-data-mk');
            $this->dispatch('refresh-stats-mk'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-mk');
            $this->showMKDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE MATA KULIAH
     */
    #[On('restore-mk')]
    public function restoreMK($id)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }
        try {
            $mk = MataKuliah::withTrashed()->findOrFail($id);
            $mk->restore();

            $this->dispatch('refresh-data-mk');
            $this->dispatch('refresh-stats-mk'); 
            $this->toast(message: 'Mata Kuliah '. $mk->mk, type: 'recycle', isAkun: true);

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-mk');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateMK()
    {
        $this->mkIdToDelete = null;
        $this->mkNamaToDelete = null;
        $this->mkKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showMKDelete = false;
    }
}