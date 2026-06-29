<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteCpmkManagement extends Component
{
    use HasToast;
    use WithCPMKDelete;

    #[On('open-delete-cpmk-modal')]
    public function handleDeleteCPMK($id, $isTrash = false)
    {
        $this->deleteCPMK($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.cpmk-management.delete-cpmk-management');
    }
}
