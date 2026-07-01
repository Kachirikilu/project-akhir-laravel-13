<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarUserManagement extends Component
{
    public $id;
    public $email;
    public $label_id1;
    public $identity1;
    public $role;
    public $isTrashed;

    public function mount($id, $email, $label_id1, $identity1, $role, $isTrashed) 
    {
        $this->id = $id;
        $this->email = $email;
        $this->label_id1 = $label_id1;
        $this->identity1 = $identity1;
        $this->role = $role;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }


    public function render()
    {
        return view('livewire.admin.user-management.toolbar-user-management');
    }
}
