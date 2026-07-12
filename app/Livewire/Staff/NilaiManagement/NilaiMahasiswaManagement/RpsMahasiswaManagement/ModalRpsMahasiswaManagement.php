<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalRpsMahasiswaManagement extends Component
{
    use HasToast;
    use WithRPSMahasiswaModal;

    public $isReady;
  
    #[On('open-edit-rps-mahasiswa-modal')]
    public function handleEditRPSMahasiswa() {
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.modal-rps-mahasiswa-management');
    }
}
