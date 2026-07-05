<?php

namespace App\Livewire\AllRole\DashboardManagement;

use Livewire\Component;
use Livewire\WithFileUploads;

class WallpaperManagement extends Component
{
    use WithFileUploads;
    public $tempImage;


public function updatedTempImage()
    {
        $this->validate([
        ]);
        $path = $this->tempImage->store('wallpapers/' . auth()->id(), 'public');

        auth()->user()->wallpapers()->create([
            'path' => '/storage/' . $path,
            'is_custom' => true,
        ]);
        $this->reset('tempImage');
        $this->dispatch('refresh-wallpaper-list');
    }

    public function setActive($id) {
        auth()->user()->wallpapers()->update(['is_active' => false]);
        auth()->user()->wallpapers()->find($id)->update(['is_active' => true]);
    }
}
