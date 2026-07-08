<?php

namespace App\Livewire\Staff\ObeManagement\ReferensiManagement;

use App\Models\Akademik\Referensi;
// use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithRefDelete
{
    use HasToast;

    public $showRefDelete = false;
    public $refIdToDelete;
    public $refNamaToDelete;
    public $refKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteRef($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $ref = $isTrashed ? Referensi::withTrashed()->find($id) : Referensi::find($id);

        if (!$ref) {
            $this->toast(message: 'Referensi', type: 'unfound', variant: 'warning');
            return;
        }

        $this->refIdToDelete = $id;
        $this->refNamaToDelete = 'Referensi '.$ref->judul;
        $this->refKodeToDelete = $ref->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showRefDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyRef()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->refIdToDelete) return;

        $type = 'delete';

        try {
            $ref = Referensi::withTrashed()->findOrFail($this->refIdToDelete);

            if ($this->isPermanentDelete) {
                // Safety Check: RPS is_draf = 0
                // $isConnected = RPS::whereHas('refs', function($q) use ($ref) {
                //     $q->where('ref_id', $ref->id);
                // })->where('is_draf', 0)->exists();

                $isConnected =
                    $ref->rps()->where('is_draf', 0)->exists()
                    || $ref->cpmks()->whereHas('rps', fn ($q) => $q->where('is_draf', 0))->exists()
                    || $ref->scpmks()->whereHas('cpmks.rps', fn ($q) => $q->where('is_draf', 0))->exists();

                if ($isConnected) {
                    throw new \Exception('Gagal hapus permanen: Referensi masih terhubung ke RPS yang sudah Aktif!');
                }

                $type = 'permanent';
                $ref->forceDelete();
            } else {
                $ref->delete();
            }

            $this->toast(message: $this->refNamaToDelete, type: $type);
            $this->cleanupDeleteStateRef();
            $this->dispatch('refresh-data-ref'); 
            $this->dispatch('refresh-stats-ref');
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-ref');
            $this->showRefDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE REFERENSI
     */
    #[On('restore-ref')]
    public function restoreRef($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $ref = Referensi::withTrashed()->findOrFail($id);
            $ref->restore();

            $this->toast(message: 'Referensi '.$ref->judul, type: 'recycle');
            $this->dispatch('refresh-data-ref');
            $this->dispatch('refresh-stats-ref');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-ref');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateRef()
    {
        $this->refIdToDelete = null;
        $this->refNamaToDelete = null;
        $this->refKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showRefDelete = false;
    }
}