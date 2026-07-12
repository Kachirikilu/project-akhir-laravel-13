<?php

namespace App\Livewire\AllRole\DashboardManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Admin\UserManagement\WithUserModal;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalWaDashboardManagement extends Component
{
    use HasToast;
    use WithWAModal;
    use WithUserModal;

    public $isReady;

    #[On('open-edit-wa-activation-modal')]
    public function handleEditUser() {
        $this->isReady = true;
        $this->editWA();
    }

    public function render()
    {
            return view('livewire.all-role.dashboard-management.modal-wa-dashboard-management');
    }
}
