<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowRpsManagement extends Component
{
    use HasToast;
    use WithRPSModal;
    use WithRPSShow;

    // public $showCPLModal;

    // public $showCPMKModal;

    // public $showSCPMKModal;

    // public $showRefModal;

    // public $showTimDosenModal;

    // public $isEditingCPL;

    // public $isEditingCPMK;

    // public $isEditingSCPMK;

    // public $isEditingRef;

    // public $isEditingTimDosen;

    // public $parent;

    #[On('open-show-rps-modal')]
    public function handleShowRPS($id, $prId = null)
    {
        // $this->parent = $parent;
        $this->showRPS($id, $prId);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.show-rps-management');
    }
}
