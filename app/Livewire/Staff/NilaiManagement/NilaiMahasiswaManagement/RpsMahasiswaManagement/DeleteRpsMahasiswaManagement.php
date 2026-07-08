<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteRpsMahasiswaManagement extends Component
{
    use HasToast;
    use WithRPSMahasiswaDelete;

    #[On('open-delete-rps-mahasiswa-modal')]
    public function handleDeleteNilai($id, $isTrash = false)
    {
        $this->deleteNilai($id, $isTrash);
    }
    
    public function render()
    {
        return view('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.delete-rps-mahasiswa-management');
    }
}
