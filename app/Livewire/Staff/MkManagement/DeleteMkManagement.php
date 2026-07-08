<?php

namespace App\Livewire\Staff\MkManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteMkManagement extends Component
{
    use HasToast;
    use WithMKDelete;

    #[On('open-delete-mk-modal')]
    public function handleDeleteMK($id, $isTrash = false)
    {
        $this->deleteMK($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.mk-management.delete-mk-management');
    }
}
