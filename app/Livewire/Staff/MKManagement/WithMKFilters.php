<?php

namespace App\Livewire\Staff\MKManagement;

use App\Models\Akademik\MataKuliah;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithMKFilters
{
    use WithPagination;

    public $filterMK = '';

    public $filterMKgg = '';

    public $totalGanjil = '';

    public $totalGenap = '';

    public function inputMKSearch()
    {
        $queryMK = MataKuliah::query()
            ->with(['prodis', 'prodis.dp_rel', 'prodis.dp_rel.fk_rel']);

        // Filter Dropdown Silsilah (Tetap di luar closure search)
        if ($this->filterMK !== '' && $this->filterMK !== 'mk_universitas') {
            if (! empty($this->selectedPrId)) {
                $queryMK->whereHas('prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
            }
            if (! empty($this->selectedDpId)) {
                $queryMK->whereHas('prodis', fn ($q) => $q->where('dp_id', $this->selectedDpId));
            }
            if (! empty($this->selectedFkId)) {
                $queryMK->whereHas('prodis.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
            }
        }

        // Filter Tab/Pills
        // if (! empty($this->filterMK)) {
        //     if (is_numeric($this->filterMK)) {
        //         $queryMK->where('semester', $this->filterMK);
        //     }
        // }

        if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
            $search = $this->search;

            if (! empty($search)) {
                $queryMK->searchMK($search);
            }

            $this->sortFieldOrderMK($queryMK);
        }

        return $queryMK;
    }

    public function buttonMKFilter($queryMK)
    {
        if ($this->filterMK === '') {
            $totalMKSaya = $queryMK->whereHas('prodis', function ($q) {
                $q->where('prodis.id', Auth::user()->pr_id);
            });
        } elseif ($this->filterMK === 'mk-wajib') {
            $queryMK->where('is_wajib', true);
        } elseif ($this->filterMK === 'mk-pilihan') {
            $queryMK->where('is_wajib', false);
        } elseif ($this->filterMK === 'mk-universitas') {
            $queryMK->where('level_mk', 4);
        }

        $this->totalGanjil = (clone $queryMK)->whereRaw('semester % 2 = 1')->count();
        $this->totalGenap = (clone $queryMK)->whereRaw('semester % 2 = 0')->count();

        if ($this->filterMKgg === 'mk-ganjil') {
            $queryMK->whereRaw('semester % 2 = 1');
        } elseif ($this->filterMKgg === 'mk-genap') {
            $queryMK->whereRaw('semester % 2 = 0');
        }
    }

    public function buttonMKSwitch($queryMK)
    {
        $mapTipe = [
            'tatap-muka' => 1,
            'praktikum' => 2,
            'praktek-lapangan' => 3,
            'simulasi' => 4,
        ];

        $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

        if ($currentTabTipe) {
            $queryMK->where('tipe_sks', $currentTabTipe);
        }
    }

    public function filterByMK($mk)
    {
        $this->filterMK = $mk;
        $this->resetPage();
    }

    public function filterByMKgg($mk)
    {
        $this->filterMKgg = $mk;
        $this->resetPage();
    }

    public function sortFieldOrderMK($queryMK)
    {
        $queryMK->select('mata_kuliahs.*');

        return match ($this->sortField) {
            'mk' => $queryMK->orderBy('nama_mk', $this->sortDirection),
            'semester' => $queryMK->orderBy('semester', $this->sortDirection),
            'sks' => $queryMK->orderBy('sks_kuliah', $this->sortDirection),
            'wajib' => $queryMK->orderBy('is_wajib', $this->sortDirection),

            'sks_tm' => $this->applyMKSksTypeSort($queryMK),
            'sks_pr' => $this->applyMKSksTypeSort($queryMK),
            'sks_pl' => $this->applyMKSksTypeSort($queryMK),
            'sks_sm' => $this->applyMKSksTypeSort($queryMK),

            'digit_mk' => $queryMK->orderBy('digit_mk', $this->sortDirection),
            'created_at' => $queryMK->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryMK->orderBy('updated_at', $this->sortDirection),

            'kode' => $this->applyMKKodeSort($queryMK),

            default => $queryMK->orderBy('mata_kuliahs.id', $this->sortDirection),
        };
    }

    private function applyMKSksTypeSort($queryMK)
    {
        $typeMap = [
            'sks_tm' => 1,
            'sks_pr' => 2,
            'sks_pl' => 3,
            'sks_sm' => 4,
        ];

        $targetType = $typeMap[$this->sortField];

        return $queryMK->orderByRaw("
            CASE WHEN tipe_sks = $targetType THEN sks_kuliah ELSE 0 END $this->sortDirection
        ");
    }
}
