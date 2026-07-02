<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;

class ToolbarSesiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.toolbar-sesi-management');
    }
}
