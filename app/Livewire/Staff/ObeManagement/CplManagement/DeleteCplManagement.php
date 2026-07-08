<?php

namespace App\Livewire\Staff\ObeManagement\CplManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteCplManagement extends Component
{
    use HasToast;
    use WithCPLDelete;

    #[On('open-delete-cpl-modal')]
    public function handleDeleteCPL($id, $isTrash = false)
    {
        $this->deleteCPL($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.delete-cpl-management');
    }
}
