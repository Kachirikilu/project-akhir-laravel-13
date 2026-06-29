<?php

namespace App\Livewire\Global\InputSearch;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;

class ProdiSearchInput extends Component
{
    use HasToast;
    use WithPagination;
    use WithProdiSearchFilters;
    // {
    //     selectPrForFilter as traitSelectPrForFilter;
    //     resetPrFilter as traitResetPrFilter;
    // }

    // public function selectPrForFilter($id)
    // {
    //     $this->traitSelectPrForFilter($id);
    //     $this->dispatch('selected-pr-id-updated', selectedPrId: $this->selectedPrId);
    // }

    // public function resetPrFilter()
    // {
    //     $this->traitResetPrFilter();
    //     $this->dispatch('selected-pr-id-updated', selectedPrId: null);
    // }

    public function render()
    {
        try {
            $this->inputPrFilter();

            return view('livewire.global.livewire-input-search.prodi-input-search');
        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.global.livewire-input-search.prodi-input-search');
        }
    }
}
