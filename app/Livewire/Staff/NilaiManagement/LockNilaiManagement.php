<?php

namespace App\Livewire\Staff\NilaiManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class LockNilaiManagement extends Component
{
    use WithLockNilai;
    public $isReady;

    #[On('open-edit-lock-nilai-modal')]
    public function handleEditLockNilai()
    {
        $this->isReady = true;
        $this->editLockNilai();
    }

    public function render()
    {
        return view('livewire.staff.nilai-management.lock-nilai-management');
    }
}
