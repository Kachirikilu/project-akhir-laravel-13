<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalUserManagement extends Component
{
    use HasToast;
    use WithProdiSearchFilters;
    use WithUserModal;

    // public $userIdModal;
    #[On('trigger-user-modal')]
    public function handleTriggerUser()
    {
    }

    #[On('open-add-user-modal')]
    public function handleAddUser($role)
    {
        $this->addUser($role);
        // $this->fetchPr();
    }

    // $this->userIdModal = $id;
    // \Log::info("Modal menerima ID: " . $this->userIdModal);
    // $this->fetchPr();

    #[On('open-edit-user-modal')]
    public function handleEditUser($id, $withRPS = false, $isRPS = false)
    {
        $this->editUser($id, $withRPS, $isRPS);
    }

    public function render()
    {
        return view('livewire.admin.user-management.modal-user-management');
    }
}
