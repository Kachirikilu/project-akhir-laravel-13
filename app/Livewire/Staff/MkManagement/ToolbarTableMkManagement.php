<?php

namespace App\Livewire\Staff\MkManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarTableMkManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.mk-management.mk-toolbar.mk-toolbar-table.toolbar-table-mk-management');
    }
}
