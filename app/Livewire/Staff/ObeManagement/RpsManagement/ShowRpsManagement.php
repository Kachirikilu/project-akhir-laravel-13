<?php

namespace App\Livewire\Staff\ObeManagement\RpsManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowRpsManagement extends Component
{
    use HasToast;
    use WithRPSModal;
    use WithRPSShow;

    public $isReady;

    #[On('open-show-rps-modal')]
    public function handleShowRPS($id, $prId = null)
    {
        $this->isReady = true;
        $this->showRPS($id, $prId);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.show-rps-management');
    }
}
