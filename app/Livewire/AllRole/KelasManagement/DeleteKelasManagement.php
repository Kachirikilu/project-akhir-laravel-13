<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteKelasManagement extends Component
{
    use HasToast;
    use WithKelasDelete;

    public $isReady;

    #[On('open-delete-kelas-modal')]
    public function handleDeleteKelas($id, $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteKelas($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.all-role.kelas-management.delete-kelas-management');
    }
}
