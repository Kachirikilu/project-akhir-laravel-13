<?php

namespace App\Livewire\Staff\MKManagement;

use App\Exports\MKExport;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithMKExcel
{
    public function exportMKExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $filter = '';
        $FILTER = '';
        if ($this->filterMK == 'mk-wajib') {
            $filter = ' Wajib';
            $FILTER = strtoupper($filter);
        } elseif ($this->filterMK == 'mk-pilihan') {
            $filter = ' Pilihan';
            $FILTER = strtoupper($filter);
        }
        $filter2 = '';
        $FILTER2 = '';
        if ($this->switchTable == 'tatap-muka') {
            $filter2 = '_Tatap Muka';
            $FILTER2 = ' TATAP MUKA';
        } elseif ($this->switchTable == 'praktikum') {
            $filter2 = '_Praktikum';
            $FILTER2 = ' PRAKTIKUM';
        } elseif ($this->switchTable == 'praktek-lapangan') {
            $filter2 = '_Praktek Lapangan';
            $FILTER2 = ' PRAKTEK LAPANGAN';
        } elseif ($this->switchTable == 'simulasi') {
            $filter2 = '_Simulasi';
            $FILTER2 = ' SIMULASI';
        }

        $tag = 'Mata Kulliah'.$filter.$filter2;
        $TAG = strtoupper($tag).$FILTER.$FILTER2;

        $queryMK = $this->inputMKSearch();
        $this->buttonMKSwitch($queryMK);
        $this->buttonMKFilter($queryMK);

        $sInput = '';
        $sINPUT = '';
        if ($this->filterMK !== '' && $this->filterMK !== 'mk-saya' && $this->filterMK !== 'mk-prodi' && $this->filterMK !== 'mk_universitas') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput = $fk->fakultas_fk.' ';
                $sINPUT = strtoupper($fk->fakultas_fk.' ');
            } elseif ($this->selectedDpId) {
                $dp = Departemen::find($this->selectedDpId);
                $sInput = $dp->departemen_dp.' ';
                $sINPUT = strtoupper($dp->departemen_dp.' ');
            } elseif ($this->selectedPrId && $this->filterMK !== '') {
                $pr = Prodi::find($this->selectedPrId);
                $sInput = $pr->prodi.'_';
                $sINPUT = strtoupper($pr->prodi_pr.' ');
            }
        }

        if ($this->filterMK == '' && Auth::user()->dosen) {
            $name = Auth::user()->name;
            $sInput = 'Dosen '.$name.'_';
            $sINPUT = strtoupper($name.' ');
        } elseif (($this->filterMK == '' && Auth::user()->admin) || ($this->filterMK == 'mk-prodi' && Auth::user()->dosen)) {
            $pr = Auth::user()->prodi;
            $pr_pr = Auth::user()->prodi_pr;
            $sInput = $pr.'_';
            $sINPUT = strtoupper($pr_pr.' ');
        }

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        if ($this->searchMode == 'full') {
            $mks = $this->searchOutputMK($queryMK, $this->search, null, $this->sortField, $this->sortDirection);
        } else {
            $mks = $queryMK;
        }

        return Excel::download(new MKExport($mks, $this->filterMK, $this->selectedPrId, $this->selectedDpId, $this->selectedFkId, $title), $fileNameSafe);
    }
}
