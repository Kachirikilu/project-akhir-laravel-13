<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
// use App\Livewire\Global\WithMahasiswaSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithDepartemenSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class NilaiAbsensiSesiManagement extends Component
{
    use HasToast;
    use WithNilaiAbsensiModal;
    // use WithMahasiswaSearchFilters;
    // use WithProdiSearchFilters;
    // use WithDepartemenSearchFilters;

    // public $kelas_id;
    // public $kode_kelas;
    public $kode_rps;
    public $kode_jadwal;
    public $kode_wilayah;
    public $mk;
    public $sks;
    public $count_sesi = 16;


    #[On('open-edit-nilai-absensi-modal')]
    public function handleEditNilaiAbsensi($id, $jadwal_id, $count_sesi)
    {
        $this->count_sesi = $count_sesi;
        $this->editNilaiAbsensi($id, $jadwal_id);
    }

    // #[On('open-edit-nilai-absensi-modal')]
    // public function handleEditNilaiAbsensi($id, $jadwal, $kelas, $count_sesi)
    // {
    //     dd($jadwal, $kelas);
    //     $this->kode_rps = $kode_rps;
    //     $this->kode_jadwal = $kode_jadwal;
    //     $this->kode_wilayah = $kode_wilayah;
    //     $this->mk = $mk;
    //     $this->sks = $sks;
    //     $this->count_sesi = $count_sesi;
    //     $this->editNilaiAbsensi($id, $jadwal['id']);
    // }

    public function render()
    {
        return view('livewire.all-role.kelas-management.jadwal-management.sesi-management.nilai-absensi-sesi-management');
    }
}
