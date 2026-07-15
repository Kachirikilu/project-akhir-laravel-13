<?php

namespace App\Livewire\Navigation;

use Livewire\Component;
use Livewire\Attributes\On;
// use Livewire\Attributes\Url;

class Navbar extends Component
{
    // #[Url(as: 'switchTable', keep: true)]
    public $switchTableMain;
    public $routeMain;

    #[On('navbar-switch-table')]
    public function navbarSwitchTableMain($switchTableMain, $routeMain)
    {
        $this->switchTableMain = $switchTableMain;
        $this->routeMain = $routeMain;
    }

    public function render()
    {
        return view('livewire.navigation.navbar');
    }
}