<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithReferensiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSubCpmkManagement extends Component
{
    use HasToast;
    use WithReferensiSearchFilters;
    use WithSubCPMKModal;

    #[On('trigger-scpmk-modal')]
    public function handleTriggerSCPMK()
    {
    }

    #[On('open-add-scpmk-modal')]
    public function handleAddSCPMK()
    {
        $this->addSCPMK();
    }

    #[On('open-edit-scpmk-modal')]
    public function handleEditSCPMK($id)
    {
        $this->editSCPMK($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.modal-scpmk-management');
    }
}
