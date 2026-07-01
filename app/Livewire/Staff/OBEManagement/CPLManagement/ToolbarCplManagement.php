<?php

namespace App\Livewire\Staff\OBEManagement\CPLManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarCplManagement extends Component
{
    public $id;
    public $kode;
    public $kode_cpl;
    public $level_cpl;
    public $deskripsi;
    public $rekap_cpl_pr;
    public $index_cpl_pr;
    public $mutu_cpl_pr;
    public $isTrashed;

    public function mount($id, $kode, $kode_cpl, $level_cpl, $deskripsi, 
                          $rekap_cpl_pr, $index_cpl_pr, $mutu_cpl_pr, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->kode_cpl = $kode_cpl;
        $this->level_cpl = $level_cpl;
        $this->deskripsi = $deskripsi;
        $this->rekap_cpl_pr = $rekap_cpl_pr;
        $this->index_cpl_pr = $index_cpl_pr;
        $this->mutu_cpl_pr = $mutu_cpl_pr;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.toolbar-cpl-management');
    }
}
