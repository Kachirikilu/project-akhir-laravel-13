<?php

namespace App\Livewire\Staff\ObeManagement\ReferensiManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalReferensiManagement extends Component
{
    use WithRPSSearchFilters;
    use WithRPSShow;
    use WithReferensiSearchFilters;
    use HasToast;
    use WithRefModal;

    // public $showRPSModal;
    // public $isEditingCPMK;
    // public $isFlyoutCPMK;
    // public $isEditingSCPMK;
    // public $isFlyoutSCPMK;
    // public $isEditingCPL;
    // public $isFlyoutCPL;

    public $parent;

    #[On('refresh-data-rps-ref')]
    public function handleRefreshReferensi()
    {
        if ($this->selected_id_ref) {
            $this->editRef($this->selected_id_ref);
        }
    }

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

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.modal-ref-management', ['ref_rps_modal_paginator' => $this->ref_rps_modal_paginator]);
    }
}
