<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteJadwalManagement extends Component
{
    use HasToast;
    use WithJadwalDelete;

    public $isReady;

    #[On('open-delete-jadwal-modal')]
    public function handleDeleteJadwal($id, $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteJadwal($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.delete-jadwal-management');
    }
}
