<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
// use App\Livewire\Global\WithMahasiswaSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithDepartemenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ExcelNilaiSesiManagement extends Component
{
    use HasToast;
    use WithSesiModal;
    use WithNilaiExcel;
    // use WithMahasiswaSearchFilters;
    // use WithProdiSearchFilters;
    // use WithDepartemenSearchFilters;

    // public $kelas_id;
    // public $kode_kelas;

    #[On('open-excel-sesi-modal')]
    public function handleAddExcelNilaiSesi() {}

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.excel-nilai-sesi-management');
    }
}
