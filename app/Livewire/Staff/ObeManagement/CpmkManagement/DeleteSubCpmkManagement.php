<?php

namespace App\Livewire\Staff\ObeManagement\CpmkManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteSubCpmkManagement extends Component
{
    use HasToast;
    use WithSubCPMKDelete;

    public $isReady;

    #[On('open-delete-scpmk-modal')]
    public function handleDeleteSubCPMK($id, $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteSCPMK($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.delete-sub-cpmk-management');
    }
}
