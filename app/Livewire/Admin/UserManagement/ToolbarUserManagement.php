<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarUserManagement extends Component
{
    #[Reactive]
    public $data;
    // public $id;
    // #[Reactive]
    // public $email;
    // public $label_id1;
    // #[Reactive]
    // public $identity1;
    // public $role;
    // public $withRPS;
    // public $isTrashed;

    // #[On('refresh-data-user')]
    // #[On('refresh-table')]
    // public function refreshUsersList()
    // {
    //     $this->dispatch('$refresh')->self();
    // }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.admin.user-management.toolbar-user-management');
    }
}
