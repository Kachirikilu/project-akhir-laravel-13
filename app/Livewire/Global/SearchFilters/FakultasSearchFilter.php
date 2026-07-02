<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithFakultasSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class FakultasSearchFilter extends Component
{
    use HasToast;
    use WithFakultasSearchFilters {
        selectFkForFilter as traitSelectFkForFilter;
        resetFkFilter as traitResetFkFilter;
    }
    use WithPagination;

    public function selectFkForFilter($id)
    {
        $this->traitSelectFkForFilter($id);
        $this->dispatch('selected-fk-id-updated', selectedFkId: $this->selectedFkId);
    }

    public function resetFkFilter()
    {
        $this->traitResetFkFilter();
        $this->dispatch('selected-fk-id-updated', selectedFkId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.search-filter-skeleton');
    }

    public function render()
    {
        try {
            $this->inputFkFilter();

            return view('livewire.global.livewire-search-filters.fakultas-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.fakultas-search-filter');
        }
    }
}
