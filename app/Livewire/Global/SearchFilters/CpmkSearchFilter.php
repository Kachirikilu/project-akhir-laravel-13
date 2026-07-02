<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPMKSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class CpmkSearchFilter extends Component
{
    use HasToast;
    use WithCPMKSearchFilters {
        selectCPMKForFilter as traitSelectCPMKForFilter;
        resetCPMKFilter as traitResetCPMKFilter;
    }
    use WithPagination;

    public function selectCPMKForFilter($id)
    {
        $this->traitSelectCPMKForFilter($id);
        $this->dispatch('selected-cpmk-id-updated', selectedCPMKId: $this->selectedCPMKId);
    }

    public function resetCPMKFilter()
    {
        $this->traitResetCPMKFilter();
        $this->dispatch('selected-cpmk-id-updated', selectedCPMKId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.search-filter-skeleton');
    }

    public function render()
    {
        try {
            $this->inputCPMKFilter();

            return view('livewire.global.livewire-search-filters.cpmk-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.cpmk-search-filter');
        }
    }
}
