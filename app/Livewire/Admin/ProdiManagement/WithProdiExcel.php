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
        if ($this->switchTable == 'departemen' || $this->switchTable == '' || $this->switchTable == 'prodi') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput = $fk->fakultas_fk.'_';
                $sINPUT = strtoupper($fk->fakultas_fk.' ');
            }
            if ($this->selectedDpId && $this->switchTable == '' || $this->switchTable == 'prodi') {
                $dp = Departemen::find($this->selectedDpId);
                $sInput = $dp->departemen_dp.'_';
                $sINPUT = strtoupper($dp->departemen_dp.' ');
            }
        }

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        // if ($this->switchTable === 'fakultas') {
        //     $this->addRekapFakultasFk($queryPr, 'rekap_fk');
        //     $this->addIndexFakultasFk($queryPr, 'index_fk');
        //     $this->addAkreditasFakultasFk($queryPr, 'akreditas_fk');
        // } elseif ($this->switchTable === 'departemen') {
        //     $this->addRekapDepartemenDp($queryPr, 'rekap_dp');
        //     $this->addIndexDepartemenDp($queryPr, 'index_dp');
        //     $this->addAkreditasDepartemenDp($queryPr, 'akreditas_dp');
        // } else {
            // $this->addRekapProdiPr($queryPr, 'rekap_pr');
            // $this->addIndexProdiPr($queryPr, 'index_pr');
            // $this->addAkreditasProdiPr($queryPr, 'akreditas_pr');
        if ($this->switchTable === '' || $this->switchTable === 'prodi') {
            $this->addMataKuliahProdiPr($queryPr, 'count_mk', 'count_rps', 'count_rps_aktif', 'count_rps_draf');
            $this->buttonStrataFilter($queryPr);
        }

        if ($this->searchMode == 'complex') {
            $prodis = $this->searchOutputPr($queryPr, $this->search, null, $this->sortField, $this->sortDirection);
        } else {
            $prodis = $queryPr;
        }

        return Excel::download(new ProdiExport($prodis, $this->switchTable, $title), $fileNameSafe);
    }
}
