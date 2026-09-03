<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;

use Livewire\Attributes\On;
use Livewire\Component;

class ModalProdiManagement extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithDosenSearchFilters;

    use WithProdiModal;


    public function mount()
    {
        for ($i = 0; $i < 6; $i++) {
            $this->dosenNameSearchArray[$i] = $this->dosenNameSearchArray[$i] ?? '';
            $this->dosen_id_array[$i] = $this->dosen_id_array[$i] ?? null;
            $this->dosen_items_array[$i] = $this->dosen_items_array[$i] ?? null;
        }
    }

    public $isReady;

    #[On('trigger-prodi-modal')]
    public function handleTriggerProdi()
    {
        // $this->isReady = true;
    }

    #[On('open-add-prodi-modal')]
    public function handleAddProdi($type)
    {
        $this->isReady = true;
        $this->addProdi($type);
    }

    #[On('open-edit-prodi-modal')]
    public function handleEditProdi($id, $type)
    {
        $this->isReady = true;
        $this->editProdi($id, $type);
    }

    public function render()
    {
        return view('livewire.admin.prodi-management.modal-prodi-management');
    }
}
