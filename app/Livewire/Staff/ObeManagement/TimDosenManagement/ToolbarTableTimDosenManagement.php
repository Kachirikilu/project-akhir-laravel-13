<?php

namespace App\Livewire\Staff\ObeManagement\TimDosenManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarTableTimDosenManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.tim-dosen-toolbar.tim-dosen-toolbar-table.toolbar-table-tim-dosen-management');
    }
}
