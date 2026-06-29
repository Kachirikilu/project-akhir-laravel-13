<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCpmkManagement extends Component
{
    use HasToast;
    use WithSubCPMKSearchFilters;
    use WithReferensiSearchFilters;
    use WithCPMKModal;

    #[On('trigger-cpmk-modal')]
    public function handleTriggerCPMK()
    {
    }

    #[On('open-add-cpmk-modal')]
    public function handleAddCPMK()
    {
        $this->addCPMK();
    }

    #[On('open-edit-cpmk-modal')]
    public function handleEditCPMK($id)
    {
        $this->editCPMK($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpmk-management.modal-cpmk-management');
    }
}
