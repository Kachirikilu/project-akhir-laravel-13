<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\MahasiswaNilaiExport;
// use App\Models\ProgramStudi\Departemen;
// use App\Models\ProgramStudi\Fakultas;
// use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithMKExcel
{
    public function exportMKExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $queryMK = $this->inputMKSearch();
        $this->buttonMKSwitch($queryMK);
        $this->buttonMKFilter($queryMK);

        $tag = 'Kode Kelas';
        $TAG = strtoupper($tag);

        $sInput = 'Nilai Mahasiswa';
        $sINPUT = strtoupper($tag);

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        return Excel::download(new MahasiswaNilaiExport($queryMK, $this->switchTable, $this->filterMK, $this->selectedPrId, $this->selectedDpId, $this->selectedFkId, $title), $fileNameSafe);
    }
}
