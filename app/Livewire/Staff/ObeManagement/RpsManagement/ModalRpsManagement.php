<?php

namespace App\Livewire\Staff\ObeManagement\RpsManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithTimDosenSearchFilters;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\SubCPMK;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\TimDosen;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalRpsManagement extends Component
{
    use HasToast;
    use WithCPLSearchFilters;
    use WithCPMKSearchFilters;
    use WithDosenSearchFilters;
    use WithMKSearchFilters;
    use WithReferensiSearchFilters;
    use WithRPSModal;
    use WithSubCPMKSearchFilters;
    use WithTimDosenSearchFilters;

    public $parent;
    public $isFlyout;

    public $isReady;

    #[On('trigger-rps-modal')]
    public function handleTriggerRPS() {
        // $this->isReady = true;
    }

    #[On('open-add-rps-modal')]
    public function handleAddRPS($parent = null)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->addRPS();
    }

    #[On('open-edit-rps-modal')]
    public function handleEditRPS($id, $parent = null, $isFlyout = false)
    {
        $this->isReady = true;
        $this->parent = $parent;
        $this->isFlyout = $isFlyout;
        $this->editRPS($id);
    }

    #[On('cpmk-created-rps')]
    public function handleCPMKCreated($id)
    {
        $this->isReady = true;
        $cpmk = CPMK::with(['scpmks', 'refs'])->find($id);
        if (! $cpmk) {
            return;
        }
        $this->cpmk_id_array[] = $cpmk->id;
        $this->cpmk_items_array[] = $this->itemsCPMK($cpmk);
        $mapped = $this->mapCPMK(collect([$cpmk]));
        $this->pushToCPMKItems($mapped);
    }

    #[On('scpmk-updated-rps')]
    public function handleSubCPMKUpdated($id)
    {
        $this->isReady = true;
        $scpmk = SubCPMK::find($id);
        if ($scpmk) {
            $this->refreshUpdatedSubCPMKInArrays($scpmk);
        }
    }


    #[On('ref-created-rps')]
    public function handleRefCreated($id)
    {
        $this->isReady = true;
        $ref = Referensi::find($id);
        if (! $ref) {
            return;
        }
        if (! in_array($ref->id, $this->ref_id_array['rps'] ?? [])) {
            $this->ref_id_array['rps'][] = $ref->id;
            $this->ref_items_array['rps'][] = $this->itemsRef($ref);
        }
    }

    #[On('tim-dosen-created-rps')]
    public function handleTimDosenCreated($id)
    {
        $this->isReady = true;
        $tim_dosen = TimDosen::with(['dosens'])->find($id);
        if (! $tim_dosen) {
            return;
        }
        $this->tim_dosen_id_array[] = $tim_dosen->id;
        $this->tim_dosen_items_array[] = $this->itemsTimDosen($tim_dosen);
        $mapped = $this->mapTimDosen(collect([$tim_dosen]));
        $this->pushToTimDosenItems($mapped);
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.modal-skeleton');
    }


    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.modal-rps-management');
    }
}
