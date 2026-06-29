<?php

namespace App\Livewire\Staff\OBEManagement\ReferensiManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalReferensiManagement extends Component
{
    use HasToast;
    use WithRefModal;

    #[On('trigger-ref-modal')]
    public function handleTriggerRef()
    {
    }

    #[On('open-add-ref-modal')]
    public function handleAddRef()
    {
        $this->addRef();
    }

    #[On('open-edit-ref-modal')]
    public function handleEditRef($id)
    {
        $this->editRef($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.modal-ref-management');
    }
}
