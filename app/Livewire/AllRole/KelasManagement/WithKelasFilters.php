<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithKelasFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterKelas = '';

    public $searchBobotKelas = '';

    public function updatingSearchBobotKelas()
    {
        $this->resetPage();
    }

    public function resetInputBobotKelas()
    {
        $this->reset('searchBobotKelas');
        $this->resetPage();
    }

    public function inputKelasSearch()
    {
        $queryKelas = Kelas::query()
            ->with(['jadwals', 'rps_rel.mk_rel.prodis', 'rps_rel.mk_rel.prodis.dp_rel', 'rps_rel.mk_rel.prodis.dp_rel.fk_rel']);

        $search = $this->search;

        if (! empty($search)) {
            $queryKelas->searchKelas($search);
        }

        if (! empty($this->selectedPrId)) {
            $queryKelas->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
        }
        if (! empty($this->selectedDpId)) {
            $queryKelas->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $this->selectedDpId));
        }
        if (! empty($this->selectedFkId)) {
            $queryKelas->whereHas('pr_rel.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
        }
        if (! empty($this->selectedRpsId)) {
            $queryKelas->whereHas('rps_rel', fn ($q) => $q->where('id', $this->selectedRpsId));
        }
        if (! empty($this->selectedMKId)) {
            $queryKelas->whereHas('rps_rel', fn ($q) => $q->where('mk_id', $this->selectedMKId));
        }
        if (! empty($this->selectedDosenId)) {
            $queryKelas->whereHas('rps_rel.dosens', function ($q) {
                $q->where('dosens.id', $this->selectedDosenId);
            });
            $queryKelas->orWhereHas('jadwals.sesis.dosens', function ($q) {
                $q->where('dosens.id', $this->selectedDosenId);
            });
        }

        $this->sortFieldOrderKelas($queryKelas);

        return $queryKelas;
    }

    // public function buttonKelasFilter($queryKelas, $currentYear, $fiveYearsAgoYear)
    // {
    //     if (! empty($this->selectedPrId)) {
    //         $queryKelas->whereHas('rps_rel.mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
    //     }
    // }
    public function buttonKelasFilter($queryKelas)
    {
        if (Auth::user()?->dosen || Auth::user()?->mahasiswa) {
            if ($this->filterKelas === '') {
                if (Auth::user()?->dosen) {
                    $queryKelas->whereHas('rps_rel.dosens', function ($q) {
                        $q->where('dosens.id', Auth::user()->dosen->id);
                    });
                    $queryKelas->orWhereHas('jadwals.sesis.dosens', function ($q) {
                        $q->where('dosens.id', Auth::user()->dosen->id);
                    });
                } else {
                    if ($this->filterKelas === '') {
                        $queryKelas->whereHas('jadwals.mahasiswas', function ($q) {
                            $q->where('mahasiswas.id', Auth::user()->mahasiswa->id);
                        });
                    }
                }
            } elseif ($this->filterKelas === 'kelas-prodi') {
                $queryKelas->whereHas('pr_rel', function ($q) {
                    $q->where('prodis.id', Auth::user()->pr_id);
                });
            }
        } else {
            if ($this->filterKelas === '' || $this->filterKelas === 'kelas-prodi') {
                $queryKelas->whereHas('pr_rel', function ($q) {
                    $q->where('prodis.id', Auth::user()->pr_id);
                });
            }
        }

        if ($this->filterKelas === 'kelas-wajib') {
            $queryKelas->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.is_wajib', true);
            });
        } elseif ($this->filterKelas === 'kelas-pilihan') {
            $queryKelas->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.is_wajib', false);
            });
        } elseif ($this->filterKelas === 'kelas-universitas') {
            $queryKelas->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.level_mk', 4);
            });
        }

    }

    public function filterByKelas($kelas)
    {
        $this->filterKelas = $kelas;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterKelas', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef']);
        $this->resetPage();
    }

    public function sortFieldOrderKelas($queryKelas)
    {
        $queryKelas->select('kelas.*');

        return match ($this->sortField) {
            'kode' => $queryKelas->orderBy('kelas.kode_kelas', $this->sortDirection),
            'kode_rps' => $this->applyRPSKodeSort(
                $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id'),
                'rps'
            ),
            'kelas' => $queryKelas->orderBy('kelas.nama_kelas', $this->sortDirection),
            'prodi' => $this->applyProdiSort(
                $queryKelas->leftJoin('prodis', 'prodis.id', '=', 'kelas.pr_id'),
                'prodis.strata',
                'prodis.nama_pr'
            ),
            'hari_pelaksanaan' => $queryKelas->orderBy(
                KelasJadwal::select('hari_pelaksanaan')->whereColumn('kelas_id', 'kelas.id')->orderBy('hari_pelaksanaan', $this->sortDirection)->limit(1),
                $this->sortDirection
            ),
            'jam_pelaksanaan' => $queryKelas->orderBy(
                KelasJadwal::select('jam_mulai')->whereColumn('kelas_id', 'kelas.id')->orderBy('jam_mulai', $this->sortDirection)->limit(1),
                $this->sortDirection
            ),
            'kapasitas' => $queryKelas->orderBy(
                KelasJadwal::selectRaw('SUM(kapasitas)')->whereColumn('kelas_id', 'kelas.id')->limit(1),
                $this->sortDirection
            ),
            'tanggal_pelaksanaan' => $queryKelas->orderBy(
                KelasJadwal::select('tanggal_mulai')->whereColumn('kelas_id', 'kelas.id')->orderBy('tanggal_mulai', $this->sortDirection)->limit(1),
                $this->sortDirection
            ),

            'kode_mk' => $this->applyMKKodeSort(
                $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                    ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id'),
                'mata_kuliahs.id'
            ),
            'mk' => $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->orderBy('mata_kuliahs.nama_mk', $this->sortDirection),
            'semester' => $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->orderBy('mata_kuliahs.semester', $this->sortDirection),
            'sks' => $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->orderBy('mata_kuliahs.sks_kuliah', $this->sortDirection),
            'sks_text' => $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->orderBy('mata_kuliahs.tipe_sks', $this->sortDirection),
            'is_wajib' => $queryKelas->leftJoin('rps', 'rps.id', '=', 'kelas.rps_id')
                ->leftJoin('mata_kuliahs', 'mata_kuliahs.id', '=', 'rps.mk_id')
                ->orderBy('mata_kuliahs.is_wajib', $this->sortDirection),

            'created_at' => $queryKelas->orderBy('kelas.created_at', $this->sortDirection),
            'updated_at' => $queryKelas->orderBy('kelas.updated_at', $this->sortDirection),

            default => $queryKelas->orderBy('kelas.id', $this->sortDirection),
        };
    }
}
