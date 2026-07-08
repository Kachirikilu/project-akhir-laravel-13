<?php

namespace App\Livewire\Staff\ObeManagement;

use App\Exports\CPLExport;
use App\Exports\CPMKExport;
use App\Exports\DosenExport;
use App\Exports\ReferensiExport;
use App\Exports\RPSExport;
use App\Exports\SubCPMKExport;
use App\Exports\TimDosenExport;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithOBEExcel
{
    public function exportOBEExcel()
    {
        $selectMK = (property_exists($this, 'selectedMKId')) ? $this->selectedMKId : false;
        $selectRPS = (property_exists($this, 'selectedRPSId')) ? $this->selectedRPSId : false;
        $selectCPL = (property_exists($this, 'selectedCPLId')) ? $this->selectedCPLId : false;
        $selectCPMK = (property_exists($this, 'selectedCPMKId')) ? $this->selectedCPMKId : false;
        $selectSCPMK = (property_exists($this, 'selectedSCPMKId')) ? $this->selectedSCPMKId : false;
        $selectRef = (property_exists($this, 'selectedRefId')) ? $this->selectedRefId : false;
        $selectTimDosen = (property_exists($this, 'selectedTimDosenId')) ? $this->selectedTimDosenId : false;

        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $now = now();
        $sixMonthsAgo = now()->subMonths(6);
        $currentYear = now()->year;
        $threeYearsAgo = now()->subYears(3);
        $fiveYearsAgo = now()->subYears(5);
        $tenYearsAgo = now()->subYears(10);

        $sInput = '';
        $sINPUT = '';

        $currentYear = date('Y');
        $suffix = '';

        if ($this->switchTable == 'rps') {
            // 1. Logika Filter RPS
            $suffix = match ($this->filterRPS) {
                'rps-akademik' => " $currentYear",
                'rps-rev-new' => ' Baru Direvisi '.($currentYear - 1)."-$currentYear",
                'rps-aktif' => ' Aktif',
                'rps-draft' => ' Draft',
                default => ''
            };
            // 2. Logika Nama Dosen
            if (Auth::user()->dosen && $this->filterRPS == '') {
                $suffix .= '_Dosen '.Auth::user()->name;
            } elseif ($this->selectedDosenId) {
                $dosen = User::whereHas('dosen', fn ($q) => $q->where('dosens.id', $this->selectedDosenId))->first();
                $suffix .= '_Dosen '.($dosen->name ?? '');
            }
        }

        // 4. Finalisasi Variabel
        $sInput .= $suffix;
        $sINPUT .= strtoupper($suffix);

        if (($this->switchTable == 'dosen' && $this->filterStatus == '') || ($this->switchTable == 'rps' && ((Auth::user()->admin && $this->filterRPS == '') || (Auth::user()->dosen && $this->filterRPS == 'rps-prodi')))) {
            $pr = Auth::user()->prodi;
            $pr_pr = Auth::user()->prodi_pr;
            $sInput .= '_'.$pr;
            $sINPUT .= strtoupper(' '.$pr_pr);
        }

        if ($this->selectedFkId && $this->switchTable == 'dosen') {
            if ($this->filterStatus !== '') {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput .= '_'.$fk->fakultas_fk;
                $sINPUT .= strtoupper(' '.$fk->fakultas_fk);
            }
        } elseif ($this->selectedPrId && ($this->switchTable == 'rps' || $this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi' || $this->switchTable == 'tim-dosen' || $this->switchTable == 'dosen')) {
            if ($this->filterStatus !== '' || $this->switchTable !== 'dosen') {
                if ($this->switchTable != 'rps' || $this->filterRPS != '' || $this->selectedPrId != Auth::user()->pr_id) {
                    $pr = Prodi::find($this->selectedPrId);

                    $sInput .= '_'.$pr->prodi;
                    if ($this->switchTable == 'rps' && ((Auth::user()->admin && $this->filterRPS == '') || (Auth::user()->dosen && $this->filterRPS == 'rps-prodi'))) {
                        $sINPUT .= strtoupper(' & '.$pr->prodi_pr);
                    } else {
                        $sINPUT .= strtoupper(' '.$pr->prodi_pr);
                    }
                }
            }
        }

        if ($this->switchTable == 'rps') {
            if ($selectMK) {
                $mk = MataKuliah::find($selectMK);
                // $kodeMK = str_replace('-', '', $mk->kode);
                $sInput .= '_Mata Kuliah '.$mk->kode;
                $sINPUT .= strtoupper(' Mata Kuliah '.$mk->kode);
            }
        }

        if ($selectRPS && ($this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi' || $this->switchTable == 'dosen')) {
            $rps = RPS::find($selectRPS);
            // $kodeRPS = str_replace('-', '', $rps->kode);
            $sInput .= '_RPS '.$rps->kode;
            $sINPUT .= strtoupper(' RPS '.$rps->kode);
        }

        if ($selectCPL && $this->switchTable == 'cpmk') {
            $cpl = CPL::find($selectCPL);
            // $kodeCPL = str_replace('-', '', $cpl->kode);
            $sInput .= '_CPL '.$cpl->kode;
            $sINPUT .= strtoupper(' CPL '.$cpl->kode);
        }

        if ($selectCPMK && ($this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi')) {
            $cpmk = CPMK::find($selectCPMK);
            // $kodeCPMK = str_replace('-', '', $cpmk->kode);
            $sInput .= '_CPMK '.$cpmk->kode;
            $sINPUT .= strtoupper(' CPMK '.$cpmk->kode);
        }

        if ($selectSCPMK && $this->switchTable == 'referensi') {
            $scpmk = SubCPMK::find($selectSCPMK);
            // $kodeSCPMK = str_replace('-', '', $scpmk->kode);
            $sInput .= '_Sub-CPMK '.$scpmk->kode;
            $sINPUT .= strtoupper(' Sub CPMK '.$scpmk->kode);
        }

        switch ($this->switchTable) {
            case 'rps':
                $queryOBE = $this->inputRPSSearch();
                $tag = 'RPS';
                $TAG = 'RENCANA PEMBELAJARAN SEMESTER';
                $this->buttonRPSFilter($queryOBE, $currentYear, $fiveYearsAgo->year);
                break;
            case 'cpl':
                $queryOBE = $this->inputCPLSearch();
                $tag = 'CPL';
                $TAG = 'CAPAIAN PEMBELAJARAN LULUSAN';
                $this->addCountRpsCpl(
                    $queryOBE,
                    null,
                    'count_rps'
                );
                $this->buttonCPLFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'cpmk':
                $queryOBE = $this->inputCPMKSearch();
                $tag = 'CPMK';
                $TAG = 'CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->buttonCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'sub-cpmk':
                $queryOBE = $this->inputSCPMKSearch();
                $tag = 'Sub-CPMK';
                $TAG = 'SUB CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->buttonSCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'referensi':
                $queryOBE = $this->inputRefSearch();
                $tag = 'Referensi';
                $TAG = strtoupper($tag);
                $this->buttonRefFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $threeYearsAgo->year, $fiveYearsAgo->year, $tenYearsAgo->year);
                break;
            case 'tim-dosen':
                $queryOBE = $this->inputTimDosenSearch();
                $tag = 'Tim Dosen';
                $TAG = strtoupper($tag);
                $this->buttonTimDosenFilter($queryOBE);
                $this->addCountRpsTimDosen(
                    $queryOBE,
                    'count_rps'
                );
                $this->addTotalSksTimDosen(
                    $queryOBE,
                    'total_sks'
                );
                break;
            case 'dosen':
                $queryOBE = $this->inputUserSearch('dosen');
                $tag = 'Dosen';
                $TAG = strtoupper($tag);
                $this->addCountRpsDosen(
                    $queryOBE,
                    'count_rps'
                );
                $this->addTotalSksDosen(
                    $queryOBE,
                    'total_sks'
                );
                break;
        }

        $fileName = 'Data_'.$tag.$sInput.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.$sINPUT.' '.$UNIV;

        if ($this->searchMode == 'full') {
            switch ($this->switchTable) {
                case 'rps':
                    $obes = $this->searchOutputRPS($queryOBE, $this->search, $this->searchBobotRPS, null, $this->sortField, $this->sortDirection);
                    break;
                case 'cpl':
                    $obes = $this->searchOutputCPL($queryOBE, $this->search, null, $this->sortField, $this->sortDirection);
                    break;
                case 'cpmk':
                    $obes = $this->searchOutputCPMK($queryOBE, $this->search, $this->searchBobotCPMK, null, $this->sortField, $this->sortDirection);
                    break;
                case 'sub-cpmk':
                    $obes = $this->searchOutputSCPMK($queryOBE, $this->search, $this->searchBobotSCPMK, null, $this->sortField, $this->sortDirection);
                    break;
                case 'referensi':
                    $obes = $this->searchOutputRef($queryOBE, $this->search, null, $this->sortField, $this->sortDirection);
                    break;
                case 'tim-dosen':
                    $obes = $this->searchOutputTimDosen($queryOBE, $this->search, null, $this->sortField, $this->sortDirection);
                    break;
                case 'dosen':
                    $obes = $this->searchOutputUser($queryOBE, $this->search, null, null, $this->sortField, $this->sortDirection, null, 1);
                    break;
            }
        } else {
            $obes = $queryOBE;
        }

        switch ($this->switchTable) {
            case 'rps':
                return Excel::download(new RPSExport($obes, $title), $fileNameSafe);
                break;
            case 'cpl':
                return Excel::download(new CPLExport($obes, $title), $fileName);
                break;
            case 'cpmk':
                return Excel::download(new CPMKExport($obes, $title), $fileNameSafe);
                break;
            case 'sub-cpmk':
                return Excel::download(new SubCPMKExport($obes, $title), $fileNameSafe);
                break;
            case 'referensi':
                return Excel::download(new ReferensiExport($obes, $title), $fileNameSafe);
                break;
            case 'tim-dosen':
                return Excel::download(new TimDosenExport($obes, $title), $fileNameSafe);
                break;
            case 'dosen':
                return Excel::download(new DosenExport($obes, $title), $fileNameSafe);
                break;
        }
    }
}
