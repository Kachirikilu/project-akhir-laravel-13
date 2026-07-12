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

    public $parent;

    public $isReady;

    #[On('refresh-data-rps-ref')]
    public function handleRefreshReferensi()
    {
        $this->isReady = true;
        if ($this->selected_id_ref) {
            $this->editRef($this->selected_id_ref);
        }
    }

    #[On('trigger-ref-modal')]
    public function handleTriggerRef() {}

    #[On('open-add-ref-modal')]
    public function handleAddRef($parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addRef();
    }

    #[On('open-edit-ref-modal')]
    public function handleEditRef($id, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->editRef($id);
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.modal-ref-management', ['ref_rps_modal_paginator' => $this->ref_rps_modal_paginator]);
    }
}
