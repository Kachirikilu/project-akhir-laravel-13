<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteProdiManagement extends Component
{
    use HasToast;
    use WithProdiDelete;

    public $isReady;

    #[On('open-delete-prodi-modal')]
    public function handleDeleteProdi($id, $type = 'prodi', $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteProdi($id, $type, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.admin.prodi-management.delete-prodi-management');
    }
}
