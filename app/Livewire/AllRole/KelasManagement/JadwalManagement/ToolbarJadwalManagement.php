<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use Livewire\Attributes\Reactive;
use Livewire\Component;

// use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;

class ToolbarJadwalManagement extends Component
{
    use WithNilaiExcel;

    // use WithRPSShow;
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.toolbar-jadwal-management');
    }
}
