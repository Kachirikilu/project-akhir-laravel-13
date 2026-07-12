<?php

namespace App\Livewire\Staff\ObeManagement\CpmkManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\CPL;
use App\Models\Akademik\SubCPMK;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCpmkManagement extends Component
{
    use HasToast;
    use WithRPSShow;
    use WithCPLSearchFilters;
    use WithCPMKModal;
    use WithReferensiSearchFilters;
    use WithRPSSearchFilters;
    use WithSubCPMKSearchFilters;

    public $parent;
    public $isReady;

    #[On('refresh-data-rps-cpmk')]
    public function handleRefreshCPMK()
    {
        $this->isReady = true;
        if ($this->selected_id_cpmk) {
            $this->editCPMK($this->selected_id_cpmk);
        }
    }

    #[On('trigger-cpmk-modal')]
    public function handleTriggerCPMK() {}

    #[On('open-add-cpmk-modal')]
    public function handleAddCPMK($parent = nul)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addCPMK();
    }

    #[On('open-edit-cpmk-modal')]
    public function handleEditCPMK($id, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->editCPMK($id);
    }

    #[On('cpl-created-cpmk')]
    public function handleCPLCreated($id)
    {
        $this->isReady = true;
        $cpl = CPL::with(['prodis'])->find($id);
        if (! $cpl) {
            return;
        }
        $this->cpl_id_array[] = $cpl->id;
        $this->cpl_items_array[] = $this->itemsSCPMK($cpl);
    }

    #[On('scpmk-created-cpmk')]
    public function handleSubCPMKCreated($id)
    {
        $this->isReady = true;
        $scpmk = SubCPMK::with(['refs'])->find($id);
        if (! $scpmk) {
            return;
        }
        $this->scpmk_id_array[] = $scpmk->id;
        $this->scpmk_items_array[] = $this->itemsSCPMK($scpmk);
        $mapped = $this->mapSCPMK(collect([$scpmk]));
        $this->pushToSCPMKItems($mapped);
    }

    #[On('scpmk-updated-cpmk')]
    public function handleSubCPMKUpdated($id)
    {
        $this->isReady = true;
        $scpmk = SubCPMK::find($id);
        if ($scpmk) {
            $this->refreshUpdatedSubCPMKInArrays($scpmk);
        }
    }

    #[On('ref-created-cpmk')]
    public function handleRefCreated($id)
    {
        $this->isReady = true;
        $ref = Referensi::find($id);
        if (! $ref) {
            return;
        }
        if (! in_array($ref->id, $this->ref_id_array['cpmk'] ?? [])) {
            $this->ref_id_array['cpmk'][] = $ref->id;
            $this->ref_items_array['cpmk'][] = $this->itemsRef($ref);
        }
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.cpmk-management.modal-cpmk-management', ['cpmk_rps_modal_paginator' => $this->cpmk_rps_modal_paginator]);
    }
}
