<?php

namespace App\Livewire\Admin\ProdiManagement\CapaianManagement;

use App\Exports\CPLExport;
use App\Exports\CPMKExport;
use App\Exports\MahasiswaExport;
use App\Exports\RPSExport;
use App\Exports\SubCPMKExport;
use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithRekapExcel
{
    public function exportRekapExcel()
    {
        $prodi = $this->prodi->prodi ?? null;
        $univ = $prodi.'_'.env('UNIVERSITAS');
        $UNIV = strtoupper($prodi.' '.env('UNIVERSITAS'));

        $prId = $this->pr_id_url;

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
                $dosen = User::whereHas('mahasiswa', fn ($q) => $q->where('dosens.id', $this->selectedDosenId))->first();
                $suffix .= '_Dosen '.($dosen->name ?? '');
            }
        }

        // 3. Logika Filter Berdasarkan Waktu (CPMK, SCPMK, CPL, REF)
        // $filter = $this->filterCPMK ?: $this->filterSCPMK ?: $this->filterCPL ?: '';

        // $suffix .= match (true) {
        //     str_ends_with($filter, '-month') => '_'.date('M Y'),
        //     str_ends_with($filter, '-6-months') => '_'.now()->subMonths(5)->format('M Y').' - '.now()->format('M Y'),
        //     str_ends_with($filter, '-year') => " $currentYear",
        //     str_ends_with($filter, '-older-5') => ' Keluaran '.($currentYear - 5).' Ke Bawah',
        //     $filter == 'ref-2-3-years' => ' '.($currentYear - 2).'-'.($currentYear - 3),
        //     $filter == 'ref-4-5-years' => ' '.($currentYear - 4).'-'.($currentYear - 5),
        //     $filter == 'ref-6-10-years' => ' '.($currentYear - 6).'-'.($currentYear - 10),
        //     $filter == 'ref-older-10' => ' Keluaran '.($currentYear - 10).' Ke Bawah',
        //     default => ''
        // };

        // 4. Finalisasi Variabel
        $sInput .= $suffix;
        $sINPUT .= strtoupper($suffix);

        // if (($this->switchTable == 'mahasiswa' && $this->filterStatus == '') || ($this->switchTable == 'rps' && ((Auth::user()->admin && $this->filterRPS == '') || (Auth::user()->dosen && $this->filterRPS == 'rps-prodi')))) {
        //     $pr = Auth::user()->prodi;
        //     $pr_pr = Auth::user()->prodi_pr;
        //     $sInput .= '_'.$pr;
        //     $sINPUT .= strtoupper(' '.$pr_pr);
        // }

        // if ($this->selectedFkId && $this->switchTable == 'mahasiswa') {
        //     if ($this->filterStatus !== '') {
        //         $fk = Fakultas::find($this->selectedFkId);
        //         $sInput .= '_'.$fk->fakultas_fk;
        //         $sINPUT .= strtoupper(' '.$fk->fakultas_fk);
        //     }
        // } elseif ($this->selectedPrId && ($this->switchTable == 'rps' || $this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl' || $this->switchTable == 'referensi' || $this->switchTable == 'mahasiswa')) {
        //     if ($this->filterStatus !== '' || $this->switchTable !== 'mahasiswa') {
        //         if ($this->switchTable != 'rps' || $this->filterRPS != '' || $this->selectedPrId != Auth::user()->pr_id) {
        //             $pr = Prodi::find($this->selectedPrId);

        //             $sInput .= '_'.$pr->prodi;
        //             if ($this->switchTable == 'rps' && ((Auth::user()->admin && $this->filterRPS == '') || (Auth::user()->dosen && $this->filterRPS == 'rps-prodi'))) {
        //                 $sINPUT .= strtoupper(' & '.$pr->prodi_pr);
        //             } else {
        //                 $sINPUT .= strtoupper(' '.$pr->prodi_pr);
        //             }
        //         }
        //     }
        // }

        if ($this->switchTable == 'rps') {
            if ($this->selectedCPLId) {
                $cpl = CPL::find($this->selectedCPLId);
                // $kodeCPL = str_replace('-', '', $cpl->kode);
                $sInput .= '_CPL '.$cpl->kode;
                $sINPUT .= strtoupper(' CPL '.$cpl->kode);
            }
        }

        if ($this->selectedRPSId && ($this->switchTable == 'cpmk' || $this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl')) {
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

        if ($this->selectedCPMKId && ($this->switchTable == 'sub-cpmk' || $this->switchTable == 'cpl')) {
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
                $queryOBE = $this->inputRPSSearch(null, null, 1);
                $tag = 'Capaian RPS';
                $TAG = 'CAPAIAN RENCANA PEMBELAJARAN SEMESTER';
                $this->addRekapProdi($queryOBE, $this->pr_id_url, 'rekap_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                $this->addIndexProdi($queryOBE, $this->pr_id_url, 'index_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                $this->addAkreditasProdi($queryOBE, $this->pr_id_url, 'mutu_rps_pr', 'rekap_rps_prodi', 'rps_id', 'rps');
                $this->buttonRPSFilter($queryOBE, $currentYear, $fiveYearsAgo->year, $prId);
                break;
            case 'cpl':
            case 'capaian':
                $queryOBE = $this->inputCPLSearch($prId);
                $tag = 'CPL';
                $TAG = 'CAPAIAN PEMBELAJARAN LULUSAN';
                $this->addCountRpsCpl(
                    $queryOBE,
                    null,
                    'count_rps'
                );
                $this->addCountRpsCpl($queryOBE, $this->pr_id_url, 'count_rps_pr');
                $this->addCountRpsCpl($queryOBE, null, 'count_rps');

                $this->addRekapProdi($queryOBE, $this->pr_id_url, 'rekap_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                $this->addIndexProdi($queryOBE, $this->pr_id_url, 'index_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                $this->addAkreditasProdi($queryOBE, $this->pr_id_url, 'mutu_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                $this->buttonCPLFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'cpmk':
                $queryOBE = $this->inputCPMKSearch($prId);
                $tag = 'Capaian CPMK';
                $TAG = 'CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->addRekapProdi($queryOBE, $this->pr_id_url, 'rekap_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                $this->addIndexProdi($queryOBE, $this->pr_id_url, 'index_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                $this->addAkreditasProdi($queryOBE, $this->pr_id_url, 'mutu_cpmk_pr', 'rekap_cpmk_prodi', 'cpmk_id', 'cpmks');
                $this->buttonCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'sub-cpmk':
            case 'sub-capaian':
                $queryOBE = $this->inputSCPMKSearch($prId);
                $tag = 'Capaian Sub-CPMK';
                $TAG = 'SUB CAPAIAN PEMBELAJARAN MATA KULIAH';
                $this->addRekapProdi($queryOBE, $this->pr_id_url, 'rekap_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                $this->addIndexProdi($queryOBE, $this->pr_id_url, 'index_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                $this->addAkreditasProdi($queryOBE, $this->pr_id_url, 'mutu_scpmk_pr', 'rekap_scpmk_prodi', 'scpmk_id', 'sub_cpmks');
                $this->buttonSCPMKFilter($queryOBE, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);
                break;
            case 'mahasiswa':
                $queryOBE = $this->inputUserSearch('mahasiswa', null, $prId);
                $tag = 'Capaian Mahasiswa';
                $TAG = strtoupper($tag);
                break;
        }


        if ($this->switchTable == 'mahasiswa') {
            if (empty($this->filterAngkatan)) {
                if (! empty($this->searchAngkatan)) {
                    $sInput .= '_Angkatan '.$this->searchAngkatan;
                    $sINPUT .= ' ANGKATAN '.$this->searchAngkatan;
                }
            } else {
                $sInput .= '_Angkatan '.$this->filterAngkatan;
                $sINPUT .= ' ANGKATAN '.$this->filterAngkatan;
            }
        }

        $fileName = 'Rekap_'.$tag.$sInput.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'REKAP '.$TAG.$sINPUT.' '.$UNIV;

        $fileNameSafe = str_replace('/', '-', $fileName);

        if ($this->searchMode == 'complex') {
            switch ($this->switchTable) {
                case 'rps':
                    $obes = $this->searchOutputRPS($queryOBE, $this->search, $this->searchBobotRPS, null, $this->sortField, $this->sortDirection);
                    break;
                case 'cpl':
                case 'capaian':
                    $obes = $this->searchOutputCPL($queryOBE, $this->search, null, $this->sortField, $this->sortDirection);
                    break;
                case 'cpmk':
                    $obes = $this->searchOutputCPMK($queryOBE, $this->search, $this->searchBobotCPMK, null, $this->sortField, $this->sortDirection);
                    break;
                case 'sub-cpmk':
                case 'sub-capaian':
                    $obes = $this->searchOutputSCPMK($queryOBE, $this->search, $this->searchBobotSCPMK, null, $this->sortField, $this->sortDirection);
                    break;
                case 'mahasiswa':
                    $obes = $this->searchOutputUser($queryOBE, $this->search, $this->searchAngkatan, null, $this->sortField, $this->sortDirection, null, 1);
                    break;
            }
        } else {
            $obes = $queryOBE;
        }

        switch ($this->switchTable) {
            case 'rps':
                return Excel::download(new RPSExport($obes, $title, 1), $fileNameSafe);
                break;
            case 'cpl':
            case 'capaian':
                return Excel::download(new CPLExport($obes, $title, 1), $fileName);
                break;
            case 'cpmk':
                return Excel::download(new CPMKExport($obes, $title, 1), $fileNameSafe);
                break;
            case 'sub-cpmk':
            case 'sub-capaian':
                return Excel::download(new SubCPMKExport($obes, $title, 1), $fileNameSafe);
                break;
            case 'mahasiswa':
                return Excel::download(new MahasiswaExport($obes, $title), $fileNameSafe);
                break;
        }
    }
}
