<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Exports\MahasiswaExport;
use App\Exports\NilaiMahasiswaExport;
use App\Models\Auth\Mahasiswa;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait WithNilaiMahasiswaExcel
{
    public function exportRekapMahasiswaExcel()
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

        $filter = $this->filterStatus ?: '';

        $queryUser = $this->inputUserSearch('mahasiswa');
        $tag = 'Capaian Mahasiswa';
        $TAG = strtoupper($tag);

        $filter = '';
        if ($this->filterStatus == 'mahasiswa-aktif') {
            $filter = ' Aktif';
        } elseif ($this->filterStatus == 'mahasiswa-non-aktif') {
            $filter = ' Tidak Aktif';
        }

        $sInput .= $filter;
        $sINPUT .= strtoupper($filter);

        if ($this->filterStatus !== '') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput .= '_'.$fk->fakultas_fk.'_';
                $sINPUT .= strtoupper(' '.$fk->fakultas_fk.' ');
            } elseif ($this->selectedDpId) {
                $dp = Departemen::find($this->selectedDpId);
                $sInput .= '_'.$dp->departemen_dp.'_';
                $sINPUT .= strtoupper(' '.$dp->departemen_dp.' ');
            } elseif ($this->selectedPrId) {
                $pr = Prodi::find($this->selectedPrId);
                $sInput .= '_'.$pr->prodi.'_';
                $sINPUT .= strtoupper(' '.$pr->prodi_pr.' ');
            }
        } else {
            $sInput = '_'.Auth::user()->prodi.'_';
            $sINPUT = strtoupper(' '.Auth::user()->prodi_pr.' ');
        }

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        if (empty($this->filterAngkatan)) {
            if (! empty($this->searchAngkatan)) {
                $sInput .= '_Angkatan '.$this->searchAngkatan;
                $sINPUT .= ' ANGKATAN '.$this->searchAngkatan;
            }
        } else {
            $sInput .= '_Angkatan '.$this->filterAngkatan;
            $sINPUT .= ' ANGKATAN '.$this->filterAngkatan;
        }

        $fileName = 'Rekap Nilai_'.$tag.$sInput.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'REKAP '.$TAG.$sINPUT.' '.$UNIV;

        $fileNameSafe = str_replace('/', '-', $fileName);

        if ($this->searchMode == 'complex') {
            $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, null, $this->sortField, $this->sortDirection, null, 1);
        } else {
            $users = $queryUser;
        }

        return Excel::download(new MahasiswaExport($users, $title), $fileNameSafe);
    }

    public function exportNilaiMahasiswaExcel($mhsId, $gg = null, $aka = null)
    {
        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $ggaka = null;
        $ggAKA = null;
        if ($aka) {
            $ggaka .= $gg.' '.$aka.'_';
            $ggAKA .= strtoupper($gg.' '.$aka.' ');
        }

        $mhs = Mahasiswa::find($mhsId);
        $name = $mhs->name;
        $nim = $mhs->nim;
        $pr = $mhs->pr_rel->prodi_pr;

        $NAME = strtoupper($name);
        $NIM = strtoupper($nim);
        $PR = strtoupper($pr);

        $fileName = 'Rekap Nilai_'.$name.'_NIM '.$nim.'_'.$ggaka.$pr.'_'.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'REKAP NILAI '.$NAME.' [NIM '.$NIM.'] '.$ggAKA.$PR.' '.$UNIV;

        $fileNameSafe = str_replace('/', '-', $fileName);

        return Excel::download(new NilaiMahasiswaExport($mhsId, $gg, $aka, $title), $fileNameSafe);
    }
}
