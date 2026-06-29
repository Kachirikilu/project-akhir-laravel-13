<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteRpsManagement extends Component
{
    use HasToast;
    use WithRPSDelete;

    #[On('open-delete-rps-modal')]
    public function handleDeleteRPS($id, $isTrash = false)
    {
        $this->deleteRPS($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.delete-rps-management');
    }
}
