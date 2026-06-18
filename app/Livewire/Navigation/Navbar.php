<?php

namespace App\Livewire\Navigation;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Route;

class Navbar extends Component
{
    public $currentRoute;

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();
    }

    // #[On('refresh-layout-sidebar')]
    // public function refreshNavbar()
    // {
    // }

    public function render()
    {
        return view('livewire.navigation.navbar');
    }
}