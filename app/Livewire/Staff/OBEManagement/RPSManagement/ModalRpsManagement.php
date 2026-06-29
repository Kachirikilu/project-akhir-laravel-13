<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithTimDosenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalRpsManagement extends Component
{
    use HasToast;
    use WithMKSearchFilters;
    use WithCPMKSearchFilters;
    use WithReferensiSearchFilters;
    use WithTimDosenSearchFilters;
    use WithRPSModal;

    #[On('trigger-rps-modal')]
    public function handleTriggerRPS()
    {
    }

    #[On('open-add-rps-modal')]
    public function handleAddRPS()
    {
        $this->addRPS();
    }

    #[On('open-edit-rps-modal')]
    public function handleEditRPS($id)
    {
        $this->editRPS($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.modal-rps-management');
    }
}
