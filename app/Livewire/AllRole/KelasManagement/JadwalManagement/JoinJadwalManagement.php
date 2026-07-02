<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class JoinJadwalManagement extends Component
{
    use HasToast;
    use WithJadwalModal;

    public $jadwal_id;


    #[On('open-join-jadwal-modal')]
    public function handleJoinJadwal() {}

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.join-jadwal-management');
    }
}
