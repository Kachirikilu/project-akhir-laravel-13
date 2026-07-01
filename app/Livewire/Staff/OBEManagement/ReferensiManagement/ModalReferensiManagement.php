<?php

namespace App\Livewire\Staff\OBEManagement\ReferensiManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalReferensiManagement extends Component
{
    use WithRPSSearchFilters;
    use WithReferensiSearchFilters;
    use HasToast;
    use WithRefModal;

    public $showRPSModal;
    public $isEditingCPMK;
    public $isFlyoutCPMK;
    public $isEditingSCPMK;
    public $isFlyoutSCPMK;
    public $isEditingCPL;
    public $isFlyoutCPL;

    public $parent;

    #[On('trigger-ref-modal')]
    public function handleTriggerRef()
    {
    }

    #[On('open-add-ref-modal')]
    public function handleAddRef($parent = null)
    {
        $this->parent = $parent;
        $this->addRef();
    }

    #[On('open-edit-ref-modal')]
    public function handleEditRef($id, $parent = null)
    {
        $this->parent = $parent;
        $this->editRef($id);
    }

    public function loadingRPSList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.modal-ref-management', ['ref_rps_modal_paginator' => $this->ref_rps_modal_paginator]);
    }
}
