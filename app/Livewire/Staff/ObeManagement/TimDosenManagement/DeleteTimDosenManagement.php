<?php

namespace App\Livewire\Staff\ObeManagement\TimDosenManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteTimDosenManagement extends Component
{
    use HasToast;
    use WithTimDosenDelete;

    #[On('open-delete-tim-dosen-modal')]
    public function handleDeleteTimDosen($id, $isTrash = false)
    {
        $this->deleteTimDosen($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.delete-tim-dosen-management');
    }
}
