<?php

namespace App\Livewire\Staff\ObeManagement\CpmkManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Models\Akademik\Referensi;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSubCpmkManagement extends Component
{
    use HasToast;
    use WithRPSShow;
    use WithReferensiSearchFilters;
    use WithRPSSearchFilters;
    use WithSubCPMKModal;

    public $parent;
    public $isReady;

    #[On('refresh-data-rps-scpmk')]
    public function handleRefreshSCPMK()
    {
        $this->isReady = true;
        if ($this->selected_id_scpmk) {
            $this->editSCPMK($this->selected_id_scpmk);
        }
    }

    #[On('trigger-scpmk-modal')]
    public function handleTriggerSCPMK() {}

    #[On('open-add-scpmk-modal')]
    public function handleAddSCPMK($parent = nul)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addSCPMK();
    }

    #[On('open-edit-scpmk-modal')]
    public function handleEditSCPMK($id, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->editSCPMK($id);
    }

    #[On('ref-created-scpmk')]
    public function handleRefCreated($id)
    {
        $this->isReady = true;
        $ref = Referensi::find($id);
        if (! $ref) {
            return;
        }
        if (! in_array($ref->id, $this->ref_id_array ?? [])) {
            $this->ref_id_array[] = $ref->id;
            $this->ref_items_array[] = $this->itemsRef($ref);
        }
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.modal-sub-cpmk-management', ['scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator]);
    }
}
