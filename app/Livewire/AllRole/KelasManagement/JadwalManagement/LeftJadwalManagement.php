<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class LeftJadwalManagement extends Component
{
    use HasToast;
    use WithJadwalModal;

    public $jadwal_id;

    #[On('open-left-jadwal-modal')]
    public function handleLeftJadwal($jadwal_id) {
        $this->jadwal_id = $jadwal_id;
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.left-jadwal-management');
    }
}
