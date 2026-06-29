<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class DepartemenSearchFilter extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters {
        selectDpForFilter as traitSelectDpForFilter;
        resetDpFilter as traitResetDpFilter;
    }
    use WithPagination;

    public function selectDpForFilter($id)
    {
        $this->traitSelectDpForFilter($id);
        $this->dispatch('selected-dp-id-updated', selectedDpId: $this->selectedDpId);
    }

    public function resetDpFilter()
    {
        $this->traitResetDpFilter();
        $this->dispatch('selected-dp-id-updated', selectedDpId: null);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-search-filters.skeleton-search-filter');
    }

    public function render()
    {
        try {
            $this->inputDpFilter();

            return view('livewire.global.livewire-search-filters.departemen-search-filter');

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-search-filters.departemen-search-filter');
        }
    }
}
