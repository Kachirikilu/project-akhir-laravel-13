<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarTableKelasManagement extends Component
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
        return view('livewire.all-role.kelas-management.kelas-toolbar.kelas-toolbar-table.toolbar-table-kelas-management');
    }
}
