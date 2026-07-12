<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteUserManagement extends Component
{
    use HasToast;
    use WithUserDelete;
    public $isReady;

    #[On('open-delete-user-modal')]
    public function handleDeleteUser($id, $isTrash = false)
    {
        $this->isReady = true;
        $this->deleteUser($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.admin.user-management.delete-user-management');
    }
}
