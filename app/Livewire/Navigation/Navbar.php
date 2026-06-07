<?php

namespace App\Livewire\Navigation;
use Livewire\Component;
use Livewire\Attributes\On;

class Navbar extends Component
{
    public $switchTable = 'rps';

    #[On('switch-table-changed')]
    public function setSwitchTable($table)
    {
        $this->switchTable = $table;
    }

    public function render()
    {
        return view('livewire.navigation.navbar');
    }
}