<?php

namespace App\Livewire\Staff\ObeManagement\CplManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarCplManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.toolbar-cpl-management');
    }
}
