<?php

namespace App\Livewire\Mahasiswa\NilaiMahasiswa;

use App\Livewire\Global\HasSortir;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithNilaiFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterNilai = '';

    public $filterNilaiGG = '';

    public $totalGanjil = '';

    public $totalGenap = '';

    public function inputNilaiSearch()
    {
        $queryNilai = NilaiMahasiswa::query()
            ->with(['rps_rel', 'mahasiswa_rel']);

        $mhsId = Auth::user()->mahasiswa->id;

        $search = $this->search;

        if (! empty($search)) {
            // $queryNilai->searchNilai($search);
        }

        // Filter Tab/Pills
        // if (! empty($this->filterNilai)) {
        //     if (is_numeric($this->filterNilai)) {
        //         $queryNilai->where('semester', $this->filterNilai);
        //     }
        // }

        $this->sortFieldOrderNilai($queryNilai);

        return $queryNilai;
    }

    public function buttonMKFilter($queryNilai)
    {
        if ($this->filterNilai === '') {
            $totalMKSaya = $queryNilai->whereHas('prodis', function ($q) {
                $q->where('prodis.id', Auth::user()->pr_id);
            });
        } elseif ($this->filterNilai === 'mk-wajib') {
            $queryNilai->where('is_wajib', true);
        } elseif ($this->filterNilai === 'mk-pilihan') {
            $queryNilai->where('is_wajib', false);
        } elseif ($this->filterNilai === 'mk-universitas') {
            $queryNilai->where('level_mk', 4);
        }

        $this->totalGanjil = (clone $queryNilai)->whereRaw('semester % 2 = 1')->count();
        $this->totalGenap = (clone $queryNilai)->whereRaw('semester % 2 = 0')->count();

        if ($this->filterNilaiGG === 'mk-ganjil') {
            $queryNilai->whereRaw('semester % 2 = 1');
        } elseif ($this->filterNilaiGG === 'mk-genap') {
            $queryNilai->whereRaw('semester % 2 = 0');
        }
    }

    public function buttonMKSwitch($queryNilai)
    {
        $mapTipe = [
            'tatap-muka' => 1,
            'praktikum' => 2,
            'praktek-lapangan' => 3,
            'simulasi' => 4,
        ];

        $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

        if ($currentTabTipe) {
            $queryNilai->where('tipe_sks', $currentTabTipe);
        }
    }

    public function filterByNilai($nilai)
    {
        $this->filterNilai = $nilai;
        $this->resetPage();
    }

    public function filterByNilaiGG($nilai)
    {
        $this->filterNilaiGG = $nilai;
        $this->resetPage();
    }

    public function sortFieldOrderNilai($queryNilai)
    {
        $queryNilai->select('nilai_mahasiswa.*');

        return match ($this->sortField) {
            // 'mk' => $queryNilai->orderBy('nama_mk', $this->sortDirection),
            // 'semester' => $queryNilai->orderBy('semester', $this->sortDirection),
            // 'sks' => $queryNilai->orderBy('sks_kuliah', $this->sortDirection),
            // 'wajib' => $queryNilai->orderBy('is_wajib', $this->sortDirection),

            // 'sks_tm' => $this->applyMKSksTypeSort($queryNilai),
            // 'sks_pr' => $this->applyMKSksTypeSort($queryNilai),
            // 'sks_pl' => $this->applyMKSksTypeSort($queryNilai),
            // 'sks_sm' => $this->applyMKSksTypeSort($queryNilai),

            // 'digit_mk' => $queryNilai->orderBy('digit_mk', $this->sortDirection),
            'created_at' => $queryNilai->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryNilai->orderBy('updated_at', $this->sortDirection),

            // 'kode' => $this->applyMKKodeSort($queryNilai),

            default => $queryNilai->orderBy('nilai_mahasiswa.id', $this->sortDirection),
        };
    }

    private function applyMKSksTypeSort($queryNilai)
    {
        $typeMap = [
            'sks_tm' => 1,
            'sks_pr' => 2,
            'sks_pl' => 3,
            'sks_sm' => 4,
        ];

        $targetType = $typeMap[$this->sortField];

        return $queryNilai->orderByRaw("
            CASE WHEN tipe_sks = $targetType THEN sks_kuliah ELSE 0 END $this->sortDirection
        ");
    }
}
