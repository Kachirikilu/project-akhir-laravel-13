<?php

namespace App\Livewire\Staff\OBEManagement\CPLManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCplManagement extends Component
{
    use HasToast;
    use WithCPLModal;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;

    // public $isEditingRPS;
    public $showCPMKModal;

    public $showRPSModal;

    public $isEditingCPMK;

    public $isFlyoutCPMK;

    public $isEditingSCPMK;

    public $isFlyoutSCPMK;

    public $isEditingRef;

    public $isFlyoutRef;

    public $parent;

    #[On('trigger-cpl-modal')]
    public function handleTriggerCPL() {}

    #[On('open-add-cpl-modal')]
    public function handleAddCPL($tingkatan = 1, $parent = null)
    {
        $this->parent = $parent;
        $this->addCPL($tingkatan);
    }

    #[On('open-edit-cpl-modal')]
    public function handleEditCPL($id, $tingkatan = 1, $isRPS = false, $parent = null)
    {
        $this->parent = $parent;
        $this->editCPL($id, $tingkatan, $isRPS);
    }

    public function loadingRPSList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.modal-cpl-management', ['cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator]);
    }
}
