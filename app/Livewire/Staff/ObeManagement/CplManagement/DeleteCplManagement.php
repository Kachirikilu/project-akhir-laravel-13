<?php

namespace App\Livewire\Staff\ObeManagement\CplManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteCplManagement extends Component
{
    use HasToast;
    use WithCPLDelete;

    public $isReady;

    #[On('open-delete-cpl-modal')]
    public function handleDeleteCPL($id, $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteCPL($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.delete-cpl-management');
    }
}
