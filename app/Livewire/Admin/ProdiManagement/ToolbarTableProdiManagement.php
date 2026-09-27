<?php

namespace App\Livewire\Admin\ProdiManagement;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ToolbarTableProdiManagement extends Component
{
    #[Reactive]
    public $data;

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.toolbar-skeleton');
    }


    public function render()
    {
        return view('livewire.admin.prodi-management.prodi-toolbar.prodi-toolbar-table.toolbar-table-prodi-management');
    }
}
