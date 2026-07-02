<?php

namespace App\Livewire\Staff\OBEManagement\TimDosenManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Models\Auth\Dosen;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalTimDosenManagement extends Component
{
    use HasToast;
    use WithDosenSearchFilters;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;
    use WithTimDosenModal;

    // public $showUserModal;
    // public $isEditingUser;

    // public $showRPSModal;
    // public $isEditingCPMK;
    // public $isFlyoutCPMK;
    // public $isEditingSCPMK;
    // public $isFlyoutSCPMK;
    // public $isEditingCPL;
    // public $isFlyoutCPL;
    // public $isEditingRef;
    // public $isFlyoutRef;

    public $parent;

    #[On('refresh-data-rps-tim-dosen')]
    public function handleRefreshTimDosen()
    {
        if ($this->selected_id_tim_dosen) {
            $this->editTimDosen($this->selected_id_tim_dosen);
        }
    }

    #[On('trigger-tim-dosen-modal')]
    public function handleTriggerTimDosen() {}

    #[On('open-add-tim-dosen-modal')]
    public function handleAddTimDosen($parent = null)
    {
        $this->parent = $parent;
        $this->addTimDosen();
    }

    #[On('open-edit-tim-dosen-modal')]
    public function handleEditTimDosen($id, $parent = null)
    {
        $this->parent = $parent;
        $this->editTimDosen($id);
    }

    #[On('dosen-created-tim-dosen')]
    public function handleDosenCreated($id)
    {
        $dosen = Dosen::find($id);
        if (! $dosen) {
            return;
        }
        if (! isset($this->dosen_id_array) || ! is_array($this->dosen_id_array)) {
            $this->dosen_id_array = [];
        }
        if (! isset($this->dosen_items_array) || ! is_array($this->dosen_items_array)) {
            $this->dosen_items_array = [];
        }
        if (! in_array($dosen->id, $this->dosen_id_array)) {
            $this->dosen_id_array[] = $dosen->id;
            $this->dosen_items_array[] = $this->itemsDosen($dosen);
        }
        $isKetua = collect($this->dosen_items_array)
            ->contains(fn ($item) => isset($item['is_ketua']) && $item['is_ketua'] === true);

        if (! $isKetua && count($this->dosen_items_array) > 0) {
            $lastIndex = array_key_last($this->dosen_items_array);
            $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
            $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
        }
    }

    public function loadingRPSList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.modal-tim-dosen-management', ['tim_dosen_rps_modal_paginator' => $this->tim_dosen_rps_modal_paginator]);
    }
}
