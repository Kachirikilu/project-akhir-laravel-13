<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement\SesiManagement;

use App\Models\Auth\User;
use App\Models\Auth\Mahasiswa;
use Livewire\WithPagination;

trait WithMahasiswaFilters
{
    use WithPagination;

    public $filterMahasiswa = '';

    public function inputDosenSearch()
    {
        $queryMahasiswa = Mahasiswa::query()->with(['user', 'pr_rel', 'jadwals']);

        if ($this->switchTable === 'mahasiswa') {

            $search = '%'.trim($this->search).'%';

            if (! empty($this->search)) {
                $queryMahasiswa->where('mahasiswas.name', 'like', $search)
                    ->orWhere('nim', 'like', $search)
                    ->orWhere('mahasiswas.nik', 'like', $search)
                    ->orWhere('mahasiswas.id', 'like', $search);
            }

            $this->sortFieldOrderMahasiswa($queryMahasiswa);
        }

        return $queryMahasiswa;
    }

    // public function buttonDosenFilter($queryMahasiswa)
    // {
    //    if ($this->filterDosen === 'dosen-rps') {
    //         $queryMahasiswa->whereHas('rps');
    //     } elseif ($this->filterDosen === 'dosen-non-rps') {
    //         $queryMahasiswa->whereDoesntHave('rps');
    //     }
    // }

    public function filterByMahasiswa($mahasiswa)
    {
        $this->filterMahasiswa = $mahasiswa;
        $this->resetPage();
    }

    public function sortFieldOrderMahasiswa($queryMahasiswa)
    {
        $queryMahasiswa->select('mahasiswas.*');

        if ($this->sortField === 'nama') {
            $queryMahasiswa->orderBy('name', $this->sortDirection);
        } elseif ($this->sortField === 'nim') {
            $queryMahasiswa->orderBy('nim', $this->sortDirection);
        } else {
            $queryMahasiswa->orderBy('mahasiswas.id', $this->sortDirection);
        }

        return $queryMahasiswa;
    }
}
