<?php

namespace App\Livewire\Staff\KelasManagement;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\DB;

trait WithMatkulDelete
{
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
        $mk = $isTrashed ? MataKuliah::withTrashed()->find($id) : MataKuliah::find($id);

        if (!$mk) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Mata Kuliah tidak ditemukan!' })");
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
       if (!$this->mkIdToDelete) return;

        try {
            DB::transaction(function () {
                $mk = MataKuliah::withTrashed()->findOrFail($this->mkIdToDelete);

                if ($this->isPermanentDelete) {
                    $mk->prodis()->detach();
                    $mk->forceDelete();
                    $message = "Mata Kuliah {$this->mkNamaToDelete} DIHAPUS PERMANEN!";
                } else {
                    $mk->delete();
                    $message = "Mata Kuliah {$this->mkNamaToDelete} dipindahkan ke sampah.";
                }

                $this->js("Flux.toast('{$message}')");
            });

            $this->cleanupDeleteState();
            $this->dispatch('refresh-data-mk'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memproses: ' . $e->getMessage() })");
            $this->showMKDelete = false;
        }
    }

    /**
     * RESTORE MATA KULIAH
     */
    public function restoreMK($id)
    {
        try {
            $mk = MataKuliah::withTrashed()->findOrFail($id);
            $mk->restore();

            $this->js("Flux.toast('Mata Kuliah {$mk->mk} berhasil dipulihkan!')");
            $this->dispatch('refresh-data-mk');

        } catch (\Exception $e) {
            $this->js("Flux.toast({ variant: 'danger', text: 'Gagal memulihkan Mata Kuliah!' })");
        }
    }

    private function cleanupDeleteState()
    {
        $this->mkIdToDelete = null;
        $this->mkNamaToDelete = null;
        $this->mkKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showMKDelete = false;
    }
}