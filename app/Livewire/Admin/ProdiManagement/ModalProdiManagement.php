<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalProdiManagement extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithProdiModal;

    #[On('trigger-prodi-modal')]
    public function handleTriggerProdi()
    {
    }

    #[On('open-add-prodi-modal')]
    public function handleAddProdi($type)
    {
        $this->addProdi($type);
    }

    #[On('open-edit-prodi-modal')]
    public function handleEditProdi($id, $type)
    {
        $this->editProdi($id, $type);
    }

    public function render()
    {
        return view('livewire.admin.prodi-management.modal-prodi-management');
    }
}
