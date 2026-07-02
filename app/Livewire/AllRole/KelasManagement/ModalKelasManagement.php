<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalKelasManagement extends Component
{
    use HasToast;
    use WithKelasModal;
    use WithRPSSearchFilters;
    use WithProdiSearchFilters;
    use WithDepartemenSearchFilters;

    public $isJadwal;

    public function mount($isJadwal = false) {
        $this->isJadwal = $isJadwal;
    }

    #[On('trigger-kelas-modal')]
    public function handleTriggerKelas() {}

    #[On('open-add-kelas-modal')]
    public function handleAddKelas()
    {
        $this->addKelas();
    }

    #[On('open-edit-kelas-modal')]
    public function handleEditKelas($id)
    {
        $this->editKelas($id);
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.modal-kelas-management');
    }
}
