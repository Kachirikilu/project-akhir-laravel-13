<?php

namespace App\Livewire\Staff\MKManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarMkManagement extends Component
{
    public $id;
    public $kode;
    public $level_mk;
    public $kode_blok;
    public $digit_semester;
    public $digit_mk;
    public $mk;
    public $semester;
    public $sks;
    public $tipe_sks;
    public $wajib;
    public $deskripsi;
    public $bahan_kajian;
    public $isTrashed;

    public function mount($id, $kode, $level_mk, $kode_blok, $digit_semester, $digit_mk, $mk, 
                          $semester, $sks, $tipe_sks, $wajib, $deskripsi, $bahan_kajian, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->level_mk = $level_mk;
        $this->kode_blok = $kode_blok;
        $this->digit_semester = $digit_semester;
        $this->digit_mk = $digit_mk;
        $this->mk = $mk;
        $this->semester = $semester;
        $this->sks = $sks;
        $this->tipe_sks = $tipe_sks;
        $this->wajib = $wajib;
        $this->deskripsi = $deskripsi;
        $this->bahan_kajian = $bahan_kajian;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }


    public function render()
    {
        return view('livewire.staff.mk-management.toolbar-mk-management');
    }
}
