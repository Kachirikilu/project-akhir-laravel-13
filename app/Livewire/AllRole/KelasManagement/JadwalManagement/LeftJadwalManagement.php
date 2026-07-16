<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class LeftJadwalManagement extends Component
{
    use HasStats;
    use HasToast;
    use WithJadwalModal;

    public $kj_id;

    public $isReady;

    #[On('open-left-jadwal-modal')]
    public function handleLeftJadwal($kj_id) {
        $this->isReady = true;
        $this->kj_id = $kj_id;
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.left-jadwal-management');
    }
}
