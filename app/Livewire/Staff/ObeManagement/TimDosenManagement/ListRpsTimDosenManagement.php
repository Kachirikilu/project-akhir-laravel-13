<?php

namespace App\Livewire\Staff\ObeManagement\TimDosenManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use Livewire\Attributes\On;
use Livewire\Component;

class ListRpsTimDosenManagement extends Component
{
    use HasToast;
    use WithDosenSearchFilters;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;
    use WithTimDosenModal;

    public $isReady;

    #[On('refresh-data-rps-tim-dosen-rps')]
    public function handleRefreshShowTimDosen()
    {
        $this->isReady = true;
        if ($this->selected_id_tim_dosen) {
            $this->editTimDosen($this->selected_id_tim_dosen, 1);
        }
    }

    #[On('open-list-rps-tim-dosen-modal')]
    public function handleShowTimDosen($id, $isRPS = false)
    {
        $this->isReady = true;
        $this->editTimDosen($id, $isRPS);
    }

    public function loadingRPSsList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.tim-dosen-management.list-rps-tim-dosen-management', ['tim_dosen_rps_modal_paginator' => $this->tim_dosen_rps_modal_paginator]);
    }
}
