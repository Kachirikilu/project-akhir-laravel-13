<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RPSMahasiswaManagement;

use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithNilaiDelete
{    
    use HasToast;
    public $showNilaiDelete = false;
    public $nilaiIdToDelete;
    public $nilaiEmailToDelete;
    public $isPermanentDelete = false;

    public function deleteNilai($id, $isTrashed = false)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        $nilai = $isTrashed ? NilaiMahasiswa::withTrashed()->find($id) : NilaiMahasiswa::find($id);

        if (!$nilai) {
            $this->toast(type: 'unfound', variant: 'warning');
            return;
        }

        $this->nilaiIdToDelete = $id;
        $this->nilaiEmailToDelete = $nilai->email;
        $this->isPermanentDelete = $isTrashed;
        $this->showNilaiDelete = true;
    }

    public function destroyNilai()
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        if (!$this->nilaiIdToDelete) return;

        $type = 'delete';

        try {
            $nilai = NilaiMahasiswa::withTrashed()->findOrFail($this->nilaiIdToDelete);

            if ($this->isPermanentDelete) {
                $type = 'permanent';
                $nilai->forceDelete();
            } else {
                $nilai->delete();
            }

            $this->dispatch('refresh-data-nilai'); 
            $this->showNilaiDelete = false;
            $this->toast(message: $this->nilaiEmailToDelete, type: $type);
            $this->cleanupDeleteStateNilai();
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-nilai');
            $this->showNilaiDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    #[On('restore-nilai')]
    public function restoreNilai($id)
    {
        if (! $this->AuthCheck()) {
            return; 
        }

        try {
            $nilai = NilaiMahasiswa::withTrashed()->findOrFail($id);
            $nilai->restore();

            $this->dispatch('refresh-data-nilai');
            $this->toast(message: $nilai->email, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-nilai');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateNilai()
    {
        $this->nilaiIdToDelete = null;
        $this->nilaiEmailToDelete = null;
        $this->isPermanentDelete = false;
        $this->showNilaiDelete = false;
    }
}