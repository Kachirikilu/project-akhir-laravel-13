<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use App\Models\Akademik\TimDosen;
use App\Livewire\Global\HasSortir;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithTimDosenFilters
{
    use WithPagination;
    use HasSortir;

    public $filterTimDosen = '';

    public function inputTimDosenSearch()
    {
        $queryTimDosen = TimDosen::query()->with([
            'rps', 'rps.mk_rel', 'dosens',
            'pr_rel', 'pr_rel.dp_rel', 'pr_rel.dp_rel.fk_rel'
        ]);


        if ($this->switchTable === 'tim-dosen') {
            if (! empty($this->selectedPrId)) {
                $queryTimDosen->where(function ($q) {
                    $q->whereRelation('pr_rel', 'prodis.id', $this->selectedPrId);
                });
            }
            if (! empty($this->selectedRPSId)) {
                $queryTimDosen->where(function ($q) {
                    $q->whereRelation('rps', 'rps.id', $this->selectedRPSId);
                });
            }
            if (! empty($this->selectedMKId)) {
                $queryTimDosen->where(function ($q) {
                    $q->whereRelation('rps.mk_rel', 'mata_kuliahs.id', $this->selectedMKId);
                });
            }

            if ($this->hasProperty('searchMode') && $this->searchMode == 'simple') {
                $search = $this->search;
                if (! empty($search)) {
                    $queryTimDosen->searchTimDosen($search);
                }
                $this->sortFieldOrderTimDosen($queryTimDosen);
            }
        }

        return $queryTimDosen;

    }

    public function buttonTimDosenFilter($queryTimDosen)
    {
        
                // 'tim-dosen-saya' => '👥',
                // 'tim-dosen-prodi' => '🏛️',
                // 'tim-dosen-all' => '👥',
                // 'tim-dosen-rps' => '✅',
                // 'tim-dosen-non-rps' => '❌',

        $userId = Auth::id();

        if (Auth::user()->dosen && $this->filterTimDosen == '') {
            $queryTimDosen->where(function ($q) use ($userId) {
                $q->whereHas('dosens', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            });
        } elseif ((Auth::user()->admin && $this->filterTimDosen == '') || (Auth::user()->dosen && ($this->filterTimDosen == 'tim-dosen-prodi' || $this->filterTimDosen == ''))) {
            $queryTimDosen->whereHas('pr_rel', function ($q) {
                $q->where('id', auth()->user()->pr_id);
            });
        } elseif ($this->filterTimDosen == 'tim-dosen-rps') {
            $queryTimDosen->whereHas('rps');
        } elseif ($this->filterTimDosen == 'tim-dosen-non-rps') {
            $queryTimDosen->whereDoesntHave('rps');
        }

    }

    public function filterByTimDosen($tim)
    {
        $this->filterTimDosen = $tim;
        $this->resetPage();
    }
    
    public function sortFieldOrderTimDosen($queryTimDosen)
    {
        $queryTimDosen->select('tim_dosens.*');

        match ($this->sortField) {
            'kode'      => $queryTimDosen->orderBy('kode_tim_dosen', $this->sortDirection),
            'nama_tim'  => $queryTimDosen->orderBy('nama_tim', $this->sortDirection),
            
            'ketua_tim', 'nama_ketua' => $this->applyKetuaSort($queryTimDosen, 'name'),
            'nip_ketua'               => $this->applyKetuaSort($queryTimDosen, 'nip'),
            
            'count_dosen' => $queryTimDosen->orderByRaw("(
                SELECT count(*) 
                FROM tim_dosen_pivot_dosen 
                WHERE tim_dosen_pivot_dosen.tim_dosen_id = tim_dosens.id
            ) " . $this->sortDirection),

            'count_koordinator' => $queryTimDosen->orderByRaw("(
                SELECT count(*) 
                FROM tim_dosen_pivot_dosen 
                WHERE tim_dosen_pivot_dosen.tim_dosen_id = tim_dosens.id 
                AND tim_dosen_pivot_dosen.peran = 'Koordinator'
            ) " . $this->sortDirection),

            'count_pengajar' => $queryTimDosen->orderByRaw("(
                SELECT count(*) 
                FROM tim_dosen_pivot_dosen 
                WHERE tim_dosen_pivot_dosen.tim_dosen_id = tim_dosens.id 
                AND tim_dosen_pivot_dosen.peran = 'Pengajar'
            ) " . $this->sortDirection),

            'count_asisten' => $queryTimDosen->orderByRaw("(
                SELECT count(*) 
                FROM tim_dosen_pivot_dosen 
                WHERE tim_dosen_pivot_dosen.tim_dosen_id = tim_dosens.id 
                AND tim_dosen_pivot_dosen.peran = 'Asisten'
            ) " . $this->sortDirection),

            'count_rps' => $queryTimDosen->orderBy('count_rps', $this->sortDirection),
            'total_sks' => $queryTimDosen->orderBy('total_sks', $this->sortDirection),

            'program_studi' => $this->applyProdiSort(
                $queryTimDosen->leftJoin('prodis', 'tim_dosens.pr_id', '=', 'prodis.id'),
                'prodis.strata',
                'prodis.nama_pr'
            ),
            'created_at' => $queryTimDosen->orderBy('created_at', $this->sortDirection),
            'updated_at' => $queryTimDosen->orderBy('updated_at', $this->sortDirection),
            default      => $queryTimDosen->orderBy('id', $this->sortDirection),
        };

        return $queryTimDosen;
    }
}
