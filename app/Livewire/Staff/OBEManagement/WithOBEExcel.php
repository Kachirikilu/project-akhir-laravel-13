<?php

namespace App\Livewire\Staff\OBEManagement;

use App\Exports\CPLExport;
use App\Exports\CPMKExport;
use App\Exports\DosenExport;
use App\Exports\ReferensiExport;
use App\Exports\RPSExport;
use App\Exports\SubCPMKExport;
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

        // 3. Logika Filter Berdasarkan Waktu (CPMK, SCPMK, CPL, REF)
        $filter = $this->filterCPMK ?: $this->filterSCPMK ?: $this->filterCPL ?: $this->filterRef ?: '';

        $suffix .= match (true) {
            str_ends_with($filter, '-month') => '_'.date('M Y'),
            str_ends_with($filter, '-6-months') => '_'.now()->subMonths(5)->format('M Y').' - '.now()->format('M Y'),
            str_ends_with($filter, '-year') => " $currentYear",
            str_ends_with($filter, '-older-5') => ' Keluaran '.($currentYear - 5).' Ke Bawah',
            $filter == 'ref-2-3-years' => ' '.($currentYear - 2).'-'.($currentYear - 3),
            $filter == 'ref-4-5-years' => ' '.($currentYear - 4).'-'.($currentYear - 5),
            $filter == 'ref-6-10-years' => ' '.($currentYear - 6).'-'.($currentYear - 10),
            $filter == 'ref-older-10' => ' Keluaran '.($currentYear - 10).' Ke Bawah',
            default => ''
        };

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
        } elseif ($this->selectedPrId && ($this->switchTable == 'rps' || $this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi' || $this->switchTable == 'dosen')) {
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
            if ($this->selectedMKId) {
                $mk = MataKuliah::find($this->selectedMKId);
                // $kodeMK = str_replace('-', '', $mk->kode);
                $sInput .= '_Mata Kuliah '.$mk->kode;
                $sINPUT .= strtoupper(' Mata Kuliah '.$mk->kode);
            }
        }

        if ($this->selectedRPSId && ($this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi' || $this->switchTable == 'dosen')) {
            $rps = RPS::find($this->selectedRPSId);
            // $kodeRPS = str_replace('-', '', $rps->kode);
            $sInput .= '_RPS '.$rps->kode;
            $sINPUT .= strtoupper(' RPS '.$rps->kode);
        }

        if ($this->selectedCPLId && $this->switchTable == 'cpmk') {
            $cpl = CPL::find($this->selectedCPLId);
            // $kodeCPL = str_replace('-', '', $cpl->kode);
            $sInput .= '_CPL '.$cpl->kode;
            $sINPUT .= strtoupper(' CPL '.$cpl->kode);
        }

        if ($this->selectedCPMKId && ($this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi')) {
            $cpmk = CPMK::find($this->selectedCPMKId);
            // $kodeCPMK = str_replace('-', '', $cpmk->kode);
            $sInput .= '_CPMK '.$cpmk->kode;
            $sINPUT .= strtoupper(' CPMK '.$cpmk->kode);
        }

        if ($this->selectedSCPMKId && $this->switchTable == 'referensi') {
            $scpmk = SubCPMK::find($this->selectedSCPMKId);
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
                return Excel::download(new ReferensiExport($obes, $title), $fileName);
                break;
            case 'dosen':
                return Excel::download(new DosenExport($obes, $title), $fileName);
                break;
        }
    }
}
