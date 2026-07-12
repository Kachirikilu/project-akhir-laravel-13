<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
// use App\Livewire\Global\WithMahasiswaSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithDepartemenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class AbsensiSesiManagement extends Component
{
    use HasToast;
    use WithNilaiAbsensiModal;

    public $isReady;

    #[On('open-absensi-sesi-modal')]
    public function handleAbsensiSesi()
    {
        $this->isReady = true;
    }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-sesi-management');
    }
}
