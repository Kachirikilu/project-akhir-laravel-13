<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteUserManagement extends Component
{
    use HasToast;
    use WithUserDelete;

    #[On('open-delete-user-modal')]
    public function handleDeleteUser($id, $isTrash = false)
    {
        $this->deleteUser($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.admin.user-management.delete-user-management');
    }
}
