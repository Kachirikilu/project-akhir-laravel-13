<?php

namespace App\Livewire\Staff\ObeManagement\CplManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCplManagement extends Component
{
    use HasToast;
    use WithCPLModal;
    use WithRPSShow;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;

    public $parent;

    public $tingkatan;

    public $isReady;

    #[On('refresh-data-rps-cpl')]
    public function handleRefreshCPL()
    {
        $this->isReady = true;
        if ($this->selected_id_cpl) {
            $this->editCPL($this->selected_id_cpl, $this->tingkatan);
        }
    }

    #[On('trigger-cpl-modal')]
    public function handleTriggerCPL() {}

    #[On('open-add-cpl-modal')]
    public function handleAddCPL($tingkatan = 1, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addCPL($tingkatan);
    }

    #[On('open-edit-cpl-modal')]
    public function handleEditCPL($id, $tingkatan = 1, $isRPS = false, $parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->tingkatan = $tingkatan;
        $this->editCPL($id, $tingkatan, $isRPS);
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.modal-cpl-management', ['cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator]);
    }
}
