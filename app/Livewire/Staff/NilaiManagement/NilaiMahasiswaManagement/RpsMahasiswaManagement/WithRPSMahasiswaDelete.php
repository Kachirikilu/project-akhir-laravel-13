<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;

trait WithRPSMahasiswaDelete
{    
    use HasToast;
    public $showRPSMahasiswaDelete = false;
    public $nilaiIdToDelete;
    public $nilaiNameToDelete;
    public $isPermanentDelete = false;

    public function deleteNilai($id, $isTrashed = false)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }

        $nilai = $isTrashed ? NilaiMahasiswa::withTrashed()->find($id) : NilaiMahasiswa::find($id);

        if (!$nilai) {
            $this->toast(type: 'unfound', variant: 'warning');
            return;
        }

        $this->nilaiIdToDelete = $id;
        $this->nilaiNameToDelete = $nilai->mahasiswa_rel?->name;
        $this->isPermanentDelete = $isTrashed;
        $this->showRPSMahasiswaDelete = true;
    }

    public function destroyNilai()
    {
        if (! $this->AuthCheck('staff')) {
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

            $this->dispatch('refresh-data-rps-mahasiswa'); 
            $this->showRPSMahasiswaDelete = false;
            $this->toast(message: 'Nilai ' .$this->nilaiNameToDelete, type: $type);
            $this->cleanupDeleteStateNilai();
            
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-rps-mahasiswa');
            $this->showRPSMahasiswaDelete = false;
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    #[On('restore-rps-mahasiswa')]
    public function restoreNilai($id)
    {
        if (! $this->AuthCheck('staff')) {
            return; 
        }

        try {
            $nilai = NilaiMahasiswa::withTrashed()->findOrFail($id);
            $nilai->restore();

            $this->dispatch('refresh-data-rps-mahasiswa');
            $this->toast(message: 'Nilai ' .$nilai->mahasiswa_rel?->name, type: 'recycle');

        } catch (\Exception $e) {
            $this->dispatch('refresh-data-rps-mahasiswa');
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    private function cleanupDeleteStateNilai()
    {
        $this->nilaiIdToDelete = null;
        $this->nilaiNameToDelete = null;
        $this->isPermanentDelete = false;
        $this->showRPSMahasiswaDelete = false;
    }
}