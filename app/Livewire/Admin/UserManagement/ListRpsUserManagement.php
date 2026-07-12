<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ListRpsUserManagement extends Component
{
    use HasToast;
    use WithRPSShow;
    use WithRPSSearchFilters;
    use WithProdiSearchFilters;
    use WithUserModal;

    public $withRPS;

    public $noModalRPS;

    public $isReady;

    public function mount($noModalRPS = false)
    {
        $this->noModalRPS = $noModalRPS;
    }


    #[On('refresh-data-rps-user-rps')]
    public function handleRefreshShowUser()
    {
        $this->isReady = true;
        if ($this->selected_id_user) {
            $this->editUser($this->selected_id_user, 1, 1);
        }
    }

    #[On('open-list-rps-user-modal')]
    public function handleShowUser($id, $withRPS = false, $isRPS = false, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->withRPS = $withRPS;
        $this->editUser($id, $withRPS, $isRPS);
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.admin.user-management.list-rps-user-management', ['user_rps_modal_paginator' => $this->user_rps_modal_paginator]);
    }
}
