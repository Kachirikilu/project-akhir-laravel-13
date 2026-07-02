<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMahasiswaSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalJadwalManagement extends Component
{
    use HasToast;
    use WithJadwalModal;
    use WithMahasiswaSearchFilters;

    public $kelas_id;
    public $kode_kelas;
    public $sks;

    public $isSesi;

    public function mount($isSesi = false) {
        $this->isSesi = $isSesi;
    }

    #[On('trigger-jadwal-modal')]
    public function handleTriggerJadwal() {}

    #[On('open-add-jadwal-modal')]
    public function handleAddJadwal($kelas_id, $kode_kelas, $sks)
    {
        $this->kelas_id = $kelas_id;
        $this->kode_kelas = $kode_kelas;
        $this->sks = $sks;
        $this->addJadwal();
    }

    #[On('open-edit-jadwal-modal')]
    public function handleEditJadwal($id, $kelas_id, $kode_kelas, $sks)
    {
        $this->kelas_id = $kelas_id;
        $this->kode_kelas = $kode_kelas;
        $this->sks = $sks;
        $this->editJadwal($id);
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.modal-jadwal-management');
    }
}
