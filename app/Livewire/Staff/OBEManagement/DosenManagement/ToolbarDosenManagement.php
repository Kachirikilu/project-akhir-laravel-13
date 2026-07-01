<?php

namespace App\Livewire\Staff\OBEManagement\DosenManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarDosenManagement extends Component
{
    public $id;
    public $email;
    public $label_id1;
    public $identity1;
    public $role;
    public $count_rps;
    public $total_sks;
    public $isTrashed;

    public function mount($id, $email, $label_id1, $identity1, $role, $count_rps = 0, $total_sks = 0, $isTrashed) 
    {
        $this->id = $id;
        $this->email = $email;
        $this->label_id1 = $label_id1;
        $this->identity1 = $identity1;
        $this->role = $role;
        $this->count_rps = $count_rps;
        $this->total_sks = $total_sks;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }


    public function render()
    {
        return view('livewire.staff.obe-management.dosen-management.toolbar-dosen-management');
    }
}
