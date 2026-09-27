<?php

namespace App\Livewire\Staff\ObeManagement\RpsManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarTableRpsManagement extends Component
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
        return view('livewire.staff.obe-management.rps-management.rps-toolbar.rps-toolbar-table.toolbar-table-rps-management');
    }
}
