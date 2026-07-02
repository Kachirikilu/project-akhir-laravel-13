<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithRPSSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class RpsSearchFilter extends Component
{
    use HasToast;
    use WithRPSSearchFilters {
        selectRPSForFilter as traitSelectRPSForFilter;
        resetRPSFilter as traitResetRPSFilter;
    }
    use WithPagination;

    public function selectRPSForFilter($id)
    {
        $this->traitSelectRPSForFilter($id);
        $this->dispatch('selected-rps-id-updated', selectedRPSId: $this->selectedRPSId);
    }

    public function resetRPSFilter()
    {
        $this->traitResetRPSFilter();
        $this->dispatch('selected-rps-id-updated', selectedRPSId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.search-filter-skeleton');
    }

    public function render()
    {
        try {
            $this->inputRPSFilter();

            return view('livewire.global.livewire-search-filters.rps-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.rps-search-filter');
        }
    }
}
