<?php

namespace App\Livewire\Admin\ProdiManagement;

use App\Exports\ProdiExport;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use Maatwebsite\Excel\Facades\Excel;

trait WithProdiExcel
{
    public function exportProdiExcel()
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $filter = '';
        if ($this->filterPr !== '') {
            $filter = ' '.ucwords($this->filterPr);
        }

        $tag = 'Program Studi'.$filter;
        $TAG = strtoupper($tag);

        if ($this->switchTable == 'fakultas') {
            $queryProdi = $this->inputFkSearch();
            $tag = 'Fakultas';
        } elseif ($this->switchTable == 'departemen') {
            $queryProdi = $this->inputDpSearch();
            $tag = 'Departemen';
        } else {
            $queryProdi = $this->inputPrSearch();
            $this->buttonStrataFilter($queryProdi);
        }

        $sInput = '';
        $sINPUT = '';
        if ($this->switchTable == 'departemen' || $this->switchTable == 'prodi') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput = $fk->fakultas_fk.'_';
                $sINPUT = strtoupper($fk->fakultas_fk.' ');
            }
            if ($this->selectedDpId && $this->switchTable == 'prodi') {
                $dp = Departemen::find($this->selectedDpId);
                $sInput = $dp->departemen_dp.'_';
                $sINPUT = strtoupper($dp->departemen_dp.' ');
            }
        }

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        return Excel::download(new ProdiExport($queryProdi, $this->switchTable, $title), $fileName);
    }
}
