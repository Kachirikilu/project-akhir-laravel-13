<?php

namespace App\Livewire\Staff\ObeManagement\ReferensiManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarTableReferensiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.ref-toolbar.ref-toolbar-table.toolbar-table-referensi-management');
    }
}
