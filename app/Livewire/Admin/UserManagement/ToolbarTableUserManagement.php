<?php

namespace App\Livewire\Admin\UserManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarTableUserManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }

    public function render()
    {
        return view('livewire.admin.user-management.user-toolbar.user-toolbar-table.toolbar-table-user-management');
    }
}
