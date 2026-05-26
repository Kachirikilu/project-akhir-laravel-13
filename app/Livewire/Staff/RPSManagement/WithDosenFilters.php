<?php

namespace App\Livewire\Staff\RPSManagement;

use App\Models\Auth\User;
use App\Models\Auth\Dosen;
use Livewire\WithPagination;

trait WithDosenFilters
{
    use WithPagination;

    public $filterDosen = '';

    public function inputDosenSearch()
    {
        $queryDosen = Dosen::query()->with(['user', 'pr_rel', 'rps', 'scpmks', 'sesiMengajars.jadwal.kelas_rel']);

        if ($this->switchTable === 'dosen') {

            $search = '%'.trim($this->search).'%';

            if (! empty($this->search)) {
                $queryDosen->where('dosens.name', 'like', $search)
                    ->orWhere('dosens.nip', 'like', $search)
                    ->orWhere('nidn', 'like', $search)
                    ->orWhere('dosens.nik', 'like', $search)
                    ->orWhere('dosens.id', 'like', $search);
            }

            $this->sortFieldOrderDosen($queryDosen);

        }

        return $queryDosen;
    }

    public function buttonDosenFilter($queryDosen)
    {
       if ($this->filterDosen === 'dosen-rps') {
            $queryDosen->whereHas('rps');
        } elseif ($this->filterDosen === 'dosen-non-rps') {
            $queryDosen->whereDoesntHave('rps');
        }
    }

    public function filterByDosen($dosen)
    {
        $this->filterDosen = $dosen;
        $this->resetPage();
    }

    public function sortFieldOrderDosen($queryDosen)
    {
        $queryDosen->select('dosens.*');

        if ($this->sortField === 'nama') {
            $queryDosen->orderBy('name', $this->sortDirection);
        } elseif ($this->sortField === 'nip') {
            $queryDosen->orderBy('nip', $this->sortDirection);
        } elseif ($this->sortField === 'nidn') {
            $queryDosen->orderBy('nidn', $this->sortDirection);
        } else {
            $queryDosen->orderBy('dosens.id', $this->sortDirection);
        }

        return $queryDosen;
    }
}
