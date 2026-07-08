<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RpsMahasiswaManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithRPSMahasiswaFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterNilai = '';

    public $filterNilaigg = '';

    public $totalGanjilRPS = '';

    public $totalGenapRPS = '';

    public function inputRPSMahasiswaSearch($mhsId)
    {
        $queryNilai = NilaiMahasiswa::query()
            ->with(['rps_rel', 'mahasiswa_rel']);

        $queryNilai->whereHas('mahasiswa_rel', function ($q) use ($mhsId) {
            $q->where('id', $mhsId);
        });

        if ($this->ganjil_genap_url) {
            $queryNilai->where('ganjil_genap', $this->ganjil_genap_url);
        }

        if ($this->akademik_url) {
            $fixAkademik = str_replace('-', '/', $this->akademik_url);
            $queryNilai->where('tahun_akademik', $fixAkademik);
        }

        return $queryNilai;
    }

    // public function buttonNilaiFilter($queryNilai)
    // {
    //     if ($this->filterNilai === '') {
    //         $totalMKSaya = $queryNilai->whereHas('prodis', function ($q) {
    //             $q->where('prodis.id', Auth::user()->pr_id);
    //         });
    //     } elseif ($this->filterNilai === 'mk-wajib') {
    //         $queryNilai->where('is_wajib', true);
    //     } elseif ($this->filterNilai === 'mk-pilihan') {
    //         $queryNilai->where('is_wajib', false);
    //     } elseif ($this->filterNilai === 'mk-universitas') {
    //         $queryNilai->where('level_mk', 4);
    //     }

    //     $this->totalGanjilRPS = (clone $queryNilai)->whereRaw('semester % 2 = 1')->count();
    //     $this->totalGenapRPS = (clone $queryNilai)->whereRaw('semester % 2 = 0')->count();

    //     if ($this->filterNilaigg === 'mk-ganjil') {
    //         $queryNilai->whereRaw('semester % 2 = 1');
    //     } elseif ($this->filterNilaigg === 'mk-genap') {
    //         $queryNilai->whereRaw('semester % 2 = 0');
    //     }
    // }

    // public function buttonNilaiSwitch($queryNilai)
    // {
    //     $mapTipe = [
    //         'tatap-muka' => 1,
    //         'praktikum' => 2,
    //         'praktek-lapangan' => 3,
    //         'simulasi' => 4,
    //     ];

    //     $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

    //     if ($currentTabTipe) {
    //         $queryNilai->where('tipe_sks', $currentTabTipe);
    //     }
    // }

    // public function filterByNilai($nilai)
    // {
    //     $this->filterNilai = $nilai;
    //     $this->resetPage();
    // }

    // public function filterByNilaigg($nilai)
    // {
    //     $this->filterNilaigg = $nilai;
    //     $this->resetPage();
    // }
}
