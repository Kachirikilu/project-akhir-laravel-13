<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarKelasManagement extends Component
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
        return view('livewire.all-role.kelas-management.toolbar-kelas-management');
    }
}
