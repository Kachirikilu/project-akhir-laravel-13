<?php

namespace App\Livewire\Staff\OBEManagement\CPMKManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Models\Akademik\Referensi;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSubCpmkManagement extends Component
{
    use HasToast;
    use WithReferensiSearchFilters;
    use WithRPSSearchFilters;
    use WithSubCPMKModal;

    public $showRefModal;

    public $isEditingRef;

    public $isEditingCPL;

    public $showRPSModal;

    public $isFlyoutCPMK;

    public $isFlyoutCPL;

    public $isFlyoutRef;

    public $parent;

    #[On('trigger-scpmk-modal')]
    public function handleTriggerSCPMK() {}

    #[On('open-add-scpmk-modal')]
    public function handleAddSCPMK($parent = nul)
    {
        $this->parent = $parent;
        $this->addSCPMK();
    }

    #[On('open-edit-scpmk-modal')]
    public function handleEditSCPMK($id, $parent = null)
    {
        $this->parent = $parent;
        $this->editSCPMK($id);
    }

    #[On('ref-created-scpmk')]
    public function handleRefCreated($id)
    {
        $ref = Referensi::find($id);
        if (! $ref) {
            return;
        }
        if (! in_array($ref->id, $this->ref_id_array['scpmk'] ?? [])) {
            $this->ref_id_array['scpmk'][] = $ref->id;
            $this->ref_items_array['scpmk'][] = $this->itemsRef($ref);
        }
    }

    public function loadingRPSList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.scpmk-management.modal-sub-cpmk-management', ['scpmk_rps_modal_paginator' => $this->scpmk_rps_modal_paginator]);
    }
}
