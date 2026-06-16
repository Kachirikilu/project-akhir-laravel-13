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
            $queryPr = $this->inputFkSearch();
            $tag = 'Fakultas';
        } elseif ($this->switchTable == 'departemen') {
            $queryPr = $this->inputDpSearch();
            $tag = 'Departemen';
        } else {
            $queryPr = $this->inputPrSearch();
            $this->buttonStrataFilter($queryPr);
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
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        if ($this->switchTable === 'prodi') {
            $this->addRekapProdi($queryPr, 'rekap_pr');
            $this->addIndexProdi($queryPr, 'index_pr');
            $this->addMutuProdi($queryPr, 'akreditas_pr');
            $this->buttonStrataFilter($queryPr);
        } elseif ($this->switchTable === 'departemen') {
            $this->addRekapDepartemen($queryPr, 'rekap_dp');
            $this->addIndexDepartemen($queryPr, 'index_dp');
            $this->addAkreditasDepartemen($queryPr, 'akreditas_dp');
        } elseif ($this->switchTable === 'fakultas') {
            $this->addRekapFakultas($queryPr, 'rekap_fk');
            $this->addIndexFakultas($queryPr, 'index_fk');
            $this->addAkreditasFakultas($queryPr, 'akreditas_fk');
        }

        if ($this->searchMode == 'full') {
            $prodis = $this->searchOutputPr($queryPr, $this->search, null, $this->sortField, $this->sortDirection);
        } else {
            $prodis = $queryPr;
        }

        return Excel::download(new ProdiExport($prodis, $this->switchTable, $title), $fileNameSafe);
    }
}
