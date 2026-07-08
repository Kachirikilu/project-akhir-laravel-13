<?php

namespace App\Livewire\Staff\ObeManagement\CpmkManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarCpmkManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpmk-management.toolbar-cpmk-management');
    }
}
