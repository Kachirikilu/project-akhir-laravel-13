<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use Livewire\Component;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use Livewire\Attributes\Reactive;

class ToolbarRpsMahasiswaManagement extends Component
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
        return view('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.toolbar-rps-mahasiswa-management');
    }
}
