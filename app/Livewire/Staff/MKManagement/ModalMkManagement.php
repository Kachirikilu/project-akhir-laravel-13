<?php

namespace App\Livewire\Staff\MKManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalMkManagement extends Component
{
    use HasToast;
    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithMKModal;

    #[On('trigger-mk-modal')]
    public function handleTriggerMK()
    {
    }

    #[On('open-add-mk-modal')]
    public function handleAddMK($type = 1)
    {
        $this->addMK($type);
    }

    #[On('open-edit-mk-modal')]
    public function handleEditMK($id, $type = false)
    {
        $this->editMK($id, $type);
    }

    public function render()
    {
        return view('livewire.staff.mk-management.modal-mk-management');
    }
}
