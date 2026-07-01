<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarSubCpmkManagement extends Component
{
    public $id;
    public $kode;
    public $kode_scpmk;
    public $deskripsi;
    public $materi;
    public $metodologi;
    public $indikator;
    public $metode;
    public $deskripsi_tugas;
    public $waktu_tugas;
    public $waktu_mandiri;
    public $bobot;
    public $isTrashed;

    public function mount($id, $kode, $kode_scpmk, $deskripsi, $materi, 
                          $metodologi, $indikator, $metode, $deskripsi_tugas, 
                          $waktu_tugas, $waktu_mandiri, $bobot, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->kode_scpmk = $kode_scpmk;
        $this->deskripsi = $deskripsi;
        $this->materi = $materi;
        $this->metodologi = $metodologi;
        $this->indikator = $indikator;
        $this->metode = $metode;
        $this->deskripsi_tugas = $deskripsi_tugas;
        $this->waktu_tugas = $waktu_tugas;
        $this->waktu_mandiri = $waktu_mandiri;
        $this->bobot = $bobot;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.toolbar-sub-cpmk-management');
    }
}
