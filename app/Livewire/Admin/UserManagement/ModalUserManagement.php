<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalUserManagement extends Component
{
    use HasToast;
    use WithRPSShow;
    use WithRPSSearchFilters;
    use WithProdiSearchFilters;
    use WithUserModal;

    public $withRPS;

    public $parent;

    #[On('refresh-data-rps-user')]
    public function handleRefreshUser()
    {
        if ($this->selected_id_user) {
            $this->editUser($this->selected_id_user, 1);
        }
    }

    // public $userIdModal;
    #[On('trigger-user-modal')]
    public function handleTriggerUser()
    {
    }

    #[On('open-add-user-modal')]
    public function handleAddUser($role, $parent = null)
    {
        $this->parent = $parent;
        $this->addUser($role);
        // $this->fetchPr();
    }

    // $this->userIdModal = $id;
    // \Log::info("Modal menerima ID: " . $this->userIdModal);
    // $this->fetchPr();

    #[On('open-edit-user-modal')]
    public function handleEditUser($id, $withRPS = false, $isRPS = false, $parent = null)
    {
        $this->parent = $parent;
        $this->withRPS = $withRPS;
        $this->editUser($id, $withRPS, $isRPS);
    }

    public function loadingRPSList() {}

    public function render()
    {
        if ($this->withRPS == true) {
            return view('livewire.admin.user-management.modal-user-management', ['user_rps_modal_paginator' => $this->user_rps_modal_paginator]);
        } else {
            return view('livewire.admin.user-management.modal-user-management');
        }
    }
}
