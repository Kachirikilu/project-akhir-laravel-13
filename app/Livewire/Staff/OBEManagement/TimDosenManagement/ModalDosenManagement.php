<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalTimDosenManagement extends Component
{
    use HasToast;
    use WithProdiSearchFilters;
    use WithDosenSearchFilters;
    use WitTimhDosenModal;

    #[On('trigger-tim-dosen-modal')]
    public function handleTriggerTimDosen()
    {
    }

    #[On('open-add-tim-dosen-modal')]
    public function handleAddTimDosen()
    {
        $this->addTimDosen();
    }

    #[On('open-edit-tim-dosen-modal')]
    public function handleEditTimDosen($id)
    {
        $this->ediTimtDosen($id);
    }

    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.modal-tim-dosen-management');
    }
}
