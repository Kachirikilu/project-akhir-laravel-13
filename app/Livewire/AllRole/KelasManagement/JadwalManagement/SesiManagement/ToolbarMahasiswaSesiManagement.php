<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ToolbarMahasiswaSesiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.toolbar-mahasiswa-sesi-management');
    }
}
