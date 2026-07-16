<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class JoinJadwalManagement extends Component
{
    use HasStats;
    use HasToast;
    use WithJadwalModal;

    public $kj_id;

    public $isReady;

    #[On('open-join-jadwal-modal')]
    public function handleJoinJadwal() {
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.join-jadwal-management');
    }
}
