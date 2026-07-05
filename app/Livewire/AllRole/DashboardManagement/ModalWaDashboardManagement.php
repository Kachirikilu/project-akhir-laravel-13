<?php

namespace App\Livewire\AllRole\DashboardManagement;

use App\Livewire\Global\HasToast;
// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
// use App\Livewire\Global\WithRPSSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalWaDashboardManagement extends Component
{
    use HasToast;
    // use WithRPSShow;
    // use WithRPSSearchFilters;
    // use WithProdiSearchFilters;
    use WithWAModal;
    use WithUserModal;

    #[On('open-edit-wa-activation-modal')]
    public function handleEditUser() {
        $this->editWA();
    }

    public function render()
    {
            return view('livewire.all-role.dashboard.modal-wa-dashboard-management');
    }
}
