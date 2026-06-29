<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class SubCpmkSearchFilter extends Component
{
    use HasToast;
    use WithSubCPMKSearchFilters {
        selectSCPMKForFilter as traitSelectSCPMKForFilter;
        resetSCPMKFilter as traitResetSCPMKFilter;
    }
    use WithPagination;

    public function selectSCPMKForFilter($id)
    {
        $this->traitSelectSCPMKForFilter($id);
        $this->dispatch('selected-scpmk-id-updated', selectedSCPMKId: $this->selectedSCPMKId);
    }

    public function resetSCPMKFilter()
    {
        $this->traitResetSCPMKFilter();
        $this->dispatch('selected-scpmk-id-updated', selectedSCPMKId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-search-filters.skeleton-search-filter');
    }

    public function render()
    {
        try {
            $this->inputSCPMKFilter();

            return view('livewire.global.livewire-search-filters.sub-cpmk-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.sub-cpmk-search-filter');
        }
    }
}
