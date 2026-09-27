<?php

namespace App\Livewire\Staff\ObeManagement\DosenManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarTableDosenManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }


    public function render()
    {
        return view('livewire.staff.obe-management.dosen-management.dosen-toolbar.dosen-toolbar-table.toolbar-table-dosen-management');
    }
}
