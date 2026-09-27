<?php

namespace App\Livewire\Staff\ObeManagement\SubCpmkManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarTableSubCpmkManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.scpmk-toolbar.scpmk-toolbar-table.toolbar-table-sub-cpmk-management');
    }
}
