<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class NilaiAbsensiSesiManagement extends Component
{
    use HasToast;
    use WithNilaiAbsensiModal;
  
    public $kode_rps;
    public $kode_jadwal;
    public $kode_wilayah;
    public $mk;
    public $sks;
    public $count_sesi = 16;

    public $isReady;

    #[On('open-edit-nilai-absensi-modal')]
    public function handleEditNilaiAbsensi($id, $kj_id, $count_sesi)
    {
        $this->isReady = true;
        $this->count_sesi = $count_sesi;
        $this->editNilaiAbsensi($id, $kj_id);
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.nilai-absensi-sesi-management');
    }
}
