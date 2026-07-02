<?php

namespace App\Livewire\Staff\OBEManagement\ReferensiManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarReferensiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.toolbar-referensi-management');
    }
}
