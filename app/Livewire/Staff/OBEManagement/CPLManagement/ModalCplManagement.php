<?php

namespace App\Livewire\Staff\OBEManagement\CPLManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCplManagement extends Component
{
    use HasToast;
    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithCPLModal;

    #[On('trigger-cpl-modal')]
    public function handleTriggerCPL()
    {
    }

    #[On('open-add-cpl-modal')]
    public function handleAddCPL()
    {
        $this->addCPL();
    }

    #[On('open-edit-cpl-modal')]
    public function handleEditCPL($id)
    {
        $this->editCPL($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.modal-cpl-management');
    }
}
