<?php

namespace App\Livewire\Staff\ObeManagement\ReferensiManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteReferensiManagement extends Component
{
    use HasToast;
    use WithRefDelete;

    #[On('open-delete-ref-modal')]
    public function handleDeleteRef($id, $isTrash = false)
    {
        $this->deleteRef($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.delete-ref-management');
    }
}
