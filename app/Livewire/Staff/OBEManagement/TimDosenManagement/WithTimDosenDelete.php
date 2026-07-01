<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use App\Models\Akademik\TimDosen;
// use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithTimDosenDelete
{
    use HasToast;

    public $showTimDosenDelete = false;
    public $timDosenIdToDelete;
    public $timDosenNamaToDelete;
    public $timDosenKodeToDelete;
    public $isPermanentDelete = false;

    /**
     * DELETE (SOFT & FORCE DELETE GABUNGAN)
     */
    public function deleteTimDosen($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $timDosen = $isTrashed ? TimDosen::withTrashed()->find($id) : TimDosen::find($id);

        if (!$timDosen) {
            $this->toast(message: 'Tim Dosen', type: 'unfound', variant: 'warning');
            return;
        }

        $this->timDosenIdToDelete = $id;
        $this->timDosenNamaToDelete = 'Tim Dosen '.$timDosen->tim;
        $this->timKodeToDelete = $timDosen->kode;
        $this->isPermanentDelete = $isTrashed;
        
        $this->showTimDosenDelete = true;
    }

    /**
     * PROSES EKSEKUSI PENGHAPUSAN
     */
    public function destroyTimDosen()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (!$this->timDosenIdToDelete) return;

        $type = 'delete';

        try {
            $timDosen = TimDosen::withTrashed()->findOrFail($this->timDosenIdToDelete);

            if ($this->isPermanentDelete) {
                $isConnected = $timDosen->rps()->where('is_draf', 0)->exists();

                if ($isConnected) {
                    throw new \Exception('Gagal hapus permanen: Tim Dosen masih terhubung ke RPS yang sudah Aktif!');
                }

                $type = 'permanent';
                $timDosen->forceDelete();
            } else {
                $timDosen->delete();
            }

            $this->toast(message: $this->timDosenNamaToDelete, type: $type);
            $this->cleanupDeleteStateTimDosen();
            $this->dispatch('refresh-data-tim-dosen'); 
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-tim-dosen');
            $this->showTimDosenDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    /**
     * RESTORE REFERENSI
     */
    #[On('restore-tim-dsoen')]
    public function restoreTimDosen($id)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        try {
            $timDosen = TimDosen::withTrashed()->findOrFail($id);
            $timDosen->restore();

            $this->toast(message: 'Tim Dosen '.$timDosen->tim, type: 'recycle');
            $this->dispatch('refresh-data-tim-dosen');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-tim-dosen');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateTimDosen()
    {
        $this->timDosenIdToDelete = null;
        $this->timDosenNamaToDelete = null;
        $this->timKodeToDelete = null;
        $this->isPermanentDelete = false;
        $this->showTimDosenDelete = false;
    }
}