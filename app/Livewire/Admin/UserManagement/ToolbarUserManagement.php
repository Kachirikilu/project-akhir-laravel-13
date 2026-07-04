<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarUserManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.admin.user-management.toolbar-user-management');
    }
}
