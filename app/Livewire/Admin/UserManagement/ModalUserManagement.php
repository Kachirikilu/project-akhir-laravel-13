<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalUserManagement extends Component
{
    use HasToast;
    use WithRPSShow;
    use WithRPSSearchFilters;
    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithUserModal;

    public $withRPS;

    public $parent;

    public $isReady;

    #[On('refresh-data-rps-user')]
    public function handleRefreshUser()
    {
        $this->isReady = true;
        if ($this->selected_id_user) {
            $this->editUser($this->selected_id_user, 1);
        }
    }

    // public $userIdModal;
    #[On('trigger-user-modal')]
    public function handleTriggerUser()
    {
        // $this->isReady = true;
    }

    #[On('open-add-user-modal')]
    public function handleAddUser($role, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addUser($role);
        // $this->fetchPr();
    }

    #[On('open-edit-user-modal')]
    public function handleEditUser($id, $withRPS = false, $isRPS = false, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->withRPS = $withRPS;
        $this->editUser($id, $withRPS, $isRPS);
    }

    public function loadingRPSsList() {}

    public function render()
    {
        if ($this->withRPS == true) {
            return view('livewire.admin.user-management.modal-user-management', ['user_rps_modal_paginator' => $this->user_rps_modal_paginator]);
        } else {
            return view('livewire.admin.user-management.modal-user-management');
        }
    }
}
