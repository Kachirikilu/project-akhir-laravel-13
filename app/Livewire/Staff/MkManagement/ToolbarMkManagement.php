<?php

namespace App\Livewire\Staff\MkManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarMkManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.mk-management.toolbar-mk-management');
    }
}
