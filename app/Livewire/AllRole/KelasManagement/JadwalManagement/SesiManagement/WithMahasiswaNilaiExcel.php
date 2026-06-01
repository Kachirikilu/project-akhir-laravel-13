<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\MahasiswaNilaiExport;
// use App\Models\ProgramStudi\Departemen;
// use App\Models\ProgramStudi\Fakultas;
// use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithMahasiswaNilaiExcel
{
    public function exportMahasiswaNilaiExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        // $querySesi = $this->inputSesiSearch($this->jadwal_id);
        // $queryMahasiswaNilai = $this->inputUserSearch('mahasiswa', $this->jadwal_id)->select('users.*');

        $tag = 'Kode Kelas';
        $TAG = strtoupper($tag);

        $sInput = 'Nilai Mahasiswa ';
        $sINPUT = strtoupper($tag);

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        // return Excel::download(new MahasiswaNilaiExport($queryMahasiswaNilai, $title), $fileNameSafe);
        return Excel::download(
            new MahasiswaNilaiExport(
                $this->jadwal_id,
                $title
            ),
            $fileNameSafe
        );  
    }
}
