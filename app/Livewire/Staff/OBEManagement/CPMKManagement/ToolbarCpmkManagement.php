<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarCpmkManagement extends Component
{
    public $id;
    public $kode;
    public $kode_cpmk;
    public $deskripsi_cpl;
    public $isTrashed;

    public function mount($id, $kode, $kode_cpmk, $deskripsi_cpl, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->kode_cpmk = $kode_cpmk;
        $this->deskripsi_cpl = $deskripsi_cpl;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpmk-management.toolbar-cpmk-management');
    }
}
