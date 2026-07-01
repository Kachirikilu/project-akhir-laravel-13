<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarTimDosenManagement extends Component
{
    public $id;
    public $kode;
    public $kode_tim_dosen;
    public $tim;
    public $isTrashed;

    public function mount($id, $kode, $kode_tim_dosen, $tim, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->kode_tim_dosen = $kode_tim_dosen;
        $this->tim = $tim;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.toolbar-tim-dosen-management');
    }
}
