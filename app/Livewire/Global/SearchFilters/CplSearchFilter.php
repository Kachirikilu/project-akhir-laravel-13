<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class CplSearchFilter extends Component
{
    use HasToast;
    use WithCPLSearchFilters {
        selectCPLForFilter as traitSelectCPLForFilter;
        resetCPLFilter as traitResetCPLFilter;
    }
    use WithPagination;

    public function selectCPLForFilter($id)
    {
        $this->traitSelectCPLForFilter($id);
        $this->dispatch('selected-cpl-id-updated', selectedCPLId: $this->selectedCPLId);
    }

    public function resetCPLFilter()
    {
        $this->traitResetCPLFilter();
        $this->dispatch('selected-cpl-id-updated', selectedCPLId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.search-filter-skeleton');
    }

    public function render()
    {
        try {
            $this->inputCPLFilter();

            return view('livewire.global.livewire-search-filters.cpl-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.cpl-search-filter');
        }
    }
}
