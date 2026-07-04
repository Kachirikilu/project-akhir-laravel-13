<?php

namespace App\Livewire\Staff\NilaiManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarNilaiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.nilai-management.toolbar-nilai-management');
    }
}
