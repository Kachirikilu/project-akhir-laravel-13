<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithMKSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class MkSearchFilter extends Component
{
    use HasToast;
    use WithMKSearchFilters {
        selectMKForFilter as traitSelectMKForFilter;
        resetMKFilter as traitResetMKFilter;
    }
    use WithPagination;

    public function selectMKForFilter($id)
    {
        $this->traitSelectMKForFilter($id);
        $this->dispatch('selected-mk-id-updated', selectedMKId: $this->selectedMKId);
    }

    public function resetMKFilter()
    {
        $this->traitResetMKFilter();
        $this->dispatch('selected-mk-id-updated', selectedMKId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-search-filters.skeleton-search-filter');
    }

    public function render()
    {
        try {
            $this->inputMKFilter();

            return view('livewire.global.livewire-search-filters.mk-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.mk-search-filter');
        }
    }
}
