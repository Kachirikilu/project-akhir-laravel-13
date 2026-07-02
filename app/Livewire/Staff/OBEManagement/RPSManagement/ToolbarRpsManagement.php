<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarRpsManagement extends Component
{
    use WithRPSShow;

    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.toolbar-rps-management');
    }
}
