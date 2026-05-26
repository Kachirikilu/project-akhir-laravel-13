<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

trait WithRPSFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterRPS = '';

    public $searchBobotRPS = '';

    public function updatingSearchBobotRPS()
    {
        $this->resetPage();
    }

    public function resetInputBobotRPS()
    {
        $this->reset('searchBobotRPS');
        $this->resetPage();
    }

    public function inputRPSSearch()
    {
        $queryRPS = RPS::query()
            ->with([
                'mk_rel.prodis',
                'mk_rel.prodis.dp_rel', 'mk_rel.prodis.dp_rel.fk_rel',
                'cpls', 'refs', 
                'cpmks', 'cpmks.cpls', 'cpmks.refs',
                'cpmks.scpmks', 'cpmks.scpmks.refs'
            ]);

        if ($this->switchTable === 'rps') {
            $search = $this->search;

            if (! empty($search)) {
                $queryRPS->searchRPS($search);
            }

            if (! empty($this->searchBobotRPS)) {
                $queryRPS->searchRPS($this->searchBobotRPS, true);
            }

            $this->sortFieldOrderRPS($queryRPS);

            if (! empty($this->selectedPrId)) {
                $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
            }
            // if (! empty($this->selectedDpId)) {
            //     $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('dp_id', $this->selectedDpId));
            // }
            // if (! empty($this->selectedFkId)) {
            //     $queryRPS->whereHas('mk_rel.prodis.dp_rel', fn ($q) => $q->where('fk_id', $this->selectedFkId));
            // }
            if (! empty($this->selectedMKId)) {
                $queryRPS->where('rps.mk_id', $this->selectedMKId);
            }
            if (! empty($this->selectedDosenId)) {
                $queryRPS->whereHas('dosens', function ($q) {
                    $q->where('dosens.id', $this->selectedDosenId);
                });
            }
        }

        return $queryRPS;
    }

    // public function inputRPSSearch()
    // {
    //     $queryRPS = RPS::query()
    //         ->with([
    //             'mk_rel.prodis',
    //             'mk_rel.prodis.dp_rel', 'mk_rel.prodis.dp_rel.fk_rel',
    //             'cpls', 'refs', 
    //             'cpmks', 'cpmks.cpls', 'cpmks.refs',
    //             'cpmks.scpmks', 'cpmks.scpmks.refs'
    //         ]);
        
    //     if ($this->switchTable === 'rps') {
    //         $this->sortFieldOrderRPS($queryRPS);

    //         if (!empty($this->selectedPrId)) {
    //             $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $this->selectedPrId));
    //         }
    //         if (!empty($this->selectedMKId)) {
    //             $queryRPS->where('rps.mk_id', $this->selectedMKId);
    //         }
    //         if (!empty($this->selectedDosenId)) {
    //             $queryRPS->whereHas('dosens', function ($q) {
    //                 $q->where('dosens.id', $this->selectedDosenId);
    //             });
    //         }
    //     }

    //     return $queryRPS;
    // }

    // public function searchOutputRPS($queryRPS)
    // {
    //     $accessorFields = ['kode', 'cpmks_count', 'count_scpmk', 'total_bobot'];
    //     $search = trim($this->search);
    //     $searchLower = '%' . strtolower($search) . '%';
    //     $searchTerm = '%' . $search . '%';
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     if (!empty($search) || in_array($this->sortField, $accessorFields)) {
            
    //         $dbMatchedIds = [];
    //         if (!empty($search)) {
    //             $dbMatchedIds = (clone $queryRPS)->searchRPS($search)
    //                 ->pluck('rps.id')
    //                 ->toArray();
    //         }
    //         $allRPS = $queryRPS->get();

    //         if (!empty($search)) {
    //             $pureSearchLower = strtolower($search);

    //             $allRPS = $allRPS->filter(function ($rps) use ($pureSearchLower, $searchClean, $dbMatchedIds) {
    //                 $matchRPSId = in_array($rps->id, $dbMatchedIds);

    //                 // 1. Match Kode (Mengabaikan "-" dan karakter spesial lainnya)
    //                 $matchKode = false;
    //                 if (!empty($searchClean) && !is_null($rps->kode)) {
    //                     $kodeClean = preg_replace('/[^A-Za-z0-9]/', '', $rps->kode);
    //                     $matchKode = str_contains(strtolower($kodeClean), strtolower($searchClean));
    //                 }

    //                 // 2. Match Jumlah CPMK
    //                 $matchCountCPMK = false;
    //                 if (is_numeric($pureSearchLower)) {
    //                     $matchCountCPMK = (int)$rps->cpmks_count === (int)$pureSearchLower;
    //                 } elseif (preg_match('/(\d+)\s*(cpmk|cpm)$/i', $pureSearchLower, $matches)) {
    //                     $matchCountCPMK = (int)$rps->cpmks_count === (int)$matches[1];
    //                 }

    //                 // 3. Match Jumlah SCPMK
    //                 $matchCountSCPMK = false;
    //                 if (is_numeric($pureSearchLower)) {
    //                     $matchCountSCPMK = (int)$rps->count_scpmk === (int)$pureSearchLower;
    //                 } elseif (preg_match('/(\d+)\s*(pert|scpm|sub-?c)/i', $pureSearchLower, $matches)) {
    //                     $matchCountSCPMK = (int)$rps->count_scpmk === (int)$matches[1];
    //                 }

    //                 // 4. Match Total Bobot
    //                 $matchTotalBobot = false;
    //                 $cleanSearchNumber = preg_replace('/[^0-9.]/', '', $pureSearchLower);
    //                 if (is_numeric($cleanSearchNumber) && !empty($cleanSearchNumber)) {
    //                     $matchTotalBobot = abs((float)$rps->total_bobot - (float)$cleanSearchNumber) < 0.01;
    //                 }

    //                 return $matchRPSId || $matchKode || $matchCountCPMK || $matchCountSCPMK || $matchTotalBobot;
    //             });
    //         }

    //         // 5. Proses Sorting Data
    //         $fieldToSort = in_array($this->sortField, $accessorFields) ? $this->sortField : 'id';
            
    //         $sortedRPS = $this->sortDirection === 'asc'
    //             ? $allRPS->sortBy(fn ($rps) => $rps->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE)
    //             : $allRPS->sortByDesc(fn ($rps) => $rps->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE);

    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;
    //         return new LengthAwarePaginator(
    //             $sortedRPS->forPage($currentPage, $this->perPage)->values(),
    //             $sortedRPS->count(),
    //             $this->perPage,
    //             $currentPage,
    //             ['path' => Paginator::resolveCurrentPath()]
    //         );
    //     } else {
    //         return $queryRPS->paginate($this->perPage);
    //     }
    // }

    public function buttonRPSFilter($queryRPS, $currentYear, $fiveYearsAgoYear)
    {
        if (Auth::user()?->dosen) {
            if ($this->filterRPS === '') {
                $queryRPS->whereHas('dosens', function ($q) {
                    $q->where('dosens.id', Auth::user()->dosen->id);
                });
            } elseif ($this->filterRPS === 'rps-prodi') {
                $queryRPS->whereHas('mk_rel.prodis', function ($q) {
                    $q->where('prodis.id', Auth::user()->pr_id);
                });
            }
        } else {
            if ($this->filterRPS === '' || $this->filterRPS === 'rps-prodi') {
                $queryRPS->whereHas('mk_rel.prodis', function ($q) {
                    $q->where('prodis.id', Auth::user()->pr_id);
                });
            }
        }

        if ($this->filterRPS === 'rps-akademik') {
            $queryRPS->where('akademik', 'like', '%'.$currentYear.'%');
        } elseif ($this->filterRPS === 'rps-rev-new') {
            $queryRPS->whereYear('revisi', $currentYear);
        } elseif ($this->filterRPS === 'rps-aktif') {
            $queryRPS->where('is_draf', false);
        } elseif ($this->filterRPS === 'rps-draf') {
            $queryRPS->where('is_draf', true);
        } elseif ($this->filterRPS === 'rps-older-5') {
            $queryRPS->whereRaw('RIGHT(akademik, 4) < ?', [$fiveYearsAgoYear]);
        }
    }

    public function filterByRPS($rps)
    {
        $this->filterRPS = $rps;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterRPS', 'filterCPMK', 'filterSCPMK', 'filterCPL', 'filterRef']);
        $this->resetPage();
    }

    public function sortFieldOrderRPS($queryRPS)
    {
        $queryRPS->select('rps.*')->withCount('cpmks');

        return match ($this->sortField) {
            'mk' => $queryRPS->join('mata_kuliahs', 'rps.mk_id', '=', 'mata_kuliahs.id')
                ->orderBy('mata_kuliahs.nama_mk', $this->sortDirection),

            'kode' => $this->applyRPSKodeSort($queryRPS),

            'akademik' => $queryRPS->orderBy('akademik', $this->sortDirection),

            'kode_mk' => $this->applyMKKodeSort($queryRPS, 'rps.mk_id'),

            'is_wajib' => $queryRPS->orderBy(
                MataKuliah::select('is_wajib')
                    ->whereColumn('mata_kuliahs.id', 'rps.mk_id')
                    ->limit(1),
                $this->sortDirection
            ),

            'sks' => $queryRPS->orderBy(
                MataKuliah::select('sks_kuliah')
                    ->whereColumn('mata_kuliahs.id', 'rps.mk_id')
                    ->limit(1),
                $this->sortDirection
            ),

            'sks_text' => $queryRPS->orderBy(
                MataKuliah::selectRaw("
                        CASE 
                            WHEN tipe_sks = 'Tatap Muka' THEN 1
                            WHEN tipe_sks = 'Praktikum' THEN 2
                            WHEN tipe_sks = 'Praktek Lapangan' THEN 3
                            WHEN tipe_sks = 'Simulasi' THEN 4
                            ELSE 5
                        END
                    ")
                    ->whereColumn('mata_kuliahs.id', 'rps.mk_id')
                    ->limit(1),
                $this->sortDirection
            ),

            // 'count_cpmk' => $queryRPS->orderBy(
            //     DB::table('rps_pivot_cpmk')
            //         ->selectRaw('count(*)')
            //         ->whereColumn('rps_pivot_cpmk.rps_id', 'rps.id'),
            //     $this->sortDirection
            // ),
            'count_cpmk'    => $queryRPS
                ->withCount('cpmks')
                ->orderBy('cpmks_count', $this->sortDirection),

            'count_scpmk' => $queryRPS->orderBy(
                DB::table('rps_pivot_cpmk')
                    ->join('cpmk_pivot_scpmk', 'rps_pivot_cpmk.cpmk_id', '=', 'cpmk_pivot_scpmk.cpmk_id')
                    ->selectRaw('count(cpmk_pivot_scpmk.scpmk_id)')
                    ->whereColumn('rps_pivot_cpmk.rps_id', 'rps.id'),
                $this->sortDirection
            ),

            'total_bobot' => $queryRPS->orderBy(
                DB::raw('(
                    COALESCE((
                        SELECT SUM(sub_cpmks.bobot)
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                    ), 0)
                    + CASE WHEN EXISTS(
                        SELECT 1
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                        AND UPPER(sub_cpmks.metode) = \'UTS\'
                    ) THEN 0 ELSE COALESCE(rps.bobot_uts, 0) END
                    + CASE WHEN EXISTS(
                        SELECT 1
                        FROM rps_pivot_cpmk
                        JOIN cpmk_pivot_scpmk ON rps_pivot_cpmk.cpmk_id = cpmk_pivot_scpmk.cpmk_id
                        JOIN sub_cpmks ON cpmk_pivot_scpmk.scpmk_id = sub_cpmks.id
                        WHERE rps_pivot_cpmk.rps_id = rps.id
                        AND UPPER(sub_cpmks.metode) IN (\'UAS\', \'LAPORAN AKHIR\', \'HASIL PROYEK\', \'HASIL PROJEK\')
                    ) THEN 0 ELSE COALESCE(rps.bobot_uas, 0) END
                )'),
                $this->sortDirection
            ),

            'is_draf' => $queryRPS->orderBy('is_draf', $this->sortDirection),
            'revisi' => $queryRPS->orderBy('revisi', $this->sortDirection),
            'created_at' => $queryRPS->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryRPS->orderBy('updated_at', $this->sortDirection),

            default => $queryRPS->orderBy('id', $this->sortDirection),
        };
    }
}
