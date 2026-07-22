<?php

namespace App\Livewire\Global\SearchFilters;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
// use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class DepartemenSearchFilter extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters {
        selectDpForFilter as traitSelectDpForFilter;
        resetDpFilter as traitResetDpFilter;
    }
    use WithPagination;

    // public function mount()
    // {
    //     $this->dpNameSearch = Auth::user()->departemen_dp;
    //     $this->selectedDpId = Auth::user()->dp_id;
    // }

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
        return view('livewire.global.livewire-skeletons.search-filter-skeleton');
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
