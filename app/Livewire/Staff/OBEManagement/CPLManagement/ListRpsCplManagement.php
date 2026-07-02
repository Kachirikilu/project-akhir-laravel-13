<?php

namespace App\Livewire\Staff\OBEManagement\CPLManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use Livewire\Attributes\On;
use Livewire\Component;

class ListRpsCplManagement extends Component
{
    use HasToast;
    use WithCPLModal;
    use WithRPSShow;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;

    public $tingkatan;

    #[On('refresh-data-rps-cpl-rps')]
    public function handleRefreshShowCPL()
    {
        if ($this->selected_id_cpl) {
            $this->editCPL($this->selected_id_cpl, $this->tingkatan, 1);
        }
    }

    #[On('open-list-rps-cpl-modal')]
    public function handleShowCPL($id, $tingkatan = 1, $isRPS = false)
    {
        $this->tingkatan = $tingkatan;
        $this->editCPL($id, $tingkatan, $isRPS);
    }

    public function loadingRPSList() {}

    public function render()
    {
        return view('livewire.staff.obe-management.cpl-management.list-rps-cpl-management', ['cpl_rps_modal_paginator' => $this->cpl_rps_modal_paginator]);
    }
}
