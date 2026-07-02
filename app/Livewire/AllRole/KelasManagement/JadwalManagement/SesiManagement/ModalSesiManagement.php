<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
// use App\Livewire\Global\WithMahasiswaSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithDepartemenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSesiManagement extends Component
{
    use HasToast;
    use WithSesiModal;
    // use WithMahasiswaSearchFilters;
    // use WithProdiSearchFilters;
    // use WithDepartemenSearchFilters;

    // public $kelas_id;
    // public $kode_kelas;
    public $sks;

    #[On('trigger-sesi-modal')]
    public function handleTriggerSesi() {}

    #[On('open-add-sesi-modal')]
    public function handleAddSesi()
    {
        $this->addSesi();
    }

    #[On('open-edit-sesi-modal')]
    public function handleEditSesi($id, $sks)
    {
        $this->sks = $sks;
        $this->editSesi($id);
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.modal-sesi-management');
    }
}
