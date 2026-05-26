<?php

namespace App\Livewire\Navigation;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileDropdown extends Component
{
    public string $userName = '';
    public string $userEmail = '';
    public string $userInitials = '';

    #[On('profile-updated')]
    public function loadUserData(): void
    {
        $user = Auth::user()->fresh(['admin', 'dosen', 'mahasiswa']);
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userInitials = $user->initials();
    }

    public function mount(): void
    {
        $this->loadUserData();
    }

    public function render()
    {
        return view('livewire.navigation.profile-dropdown'); 
    }
}