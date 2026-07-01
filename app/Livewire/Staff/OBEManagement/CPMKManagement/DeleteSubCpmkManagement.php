<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteSubCpmkManagement extends Component
{
    use HasToast;
    use WithSubCPMKDelete;

    #[On('open-delete-scpmk-modal')]
    public function handleDeleteSubCPMK($id, $isTrash = false)
    {
        $this->deleteSCPMK($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.delete-sub-cpmk-management');
    }
}
