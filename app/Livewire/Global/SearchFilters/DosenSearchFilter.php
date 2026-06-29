<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class DosenSearchFilter extends Component
{
    use HasToast;
    use WithDosenSearchFilters {
        selectDosenForFilter as traitSelectDosenForFilter;
        resetDosenFilter as traitResetDosenFilter;
    }
    use WithPagination;

    public function selectDosenForFilter($id)
    {
        $this->traitSelectDosenForFilter($id);
        $this->dispatch('selected-dosen-id-updated', selectedDosenId: $this->selectedDosenId);
    }

    public function resetDosenFilter()
    {
        $this->traitResetDosenFilter();
        $this->dispatch('selected-dosen-id-updated', selectedDosenId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-search-filters.skeleton-search-filter');
    }

    public function render()
    {
        try {
            $this->inputDosenFilter();

            return view('livewire.global.livewire-search-filters.dosen-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.dosen-search-filter');
        }
    }
}
