<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarRpsManagement extends Component
{
    use WithRPSShow;
    
    public $id;
    public $kode;
    public $rps;
    public $draf;
    public $level_mk;
    public $deskripsi_rps;
    public $mk_id;
    public $kode_mk;
    public $mk;
    public $akademik;
    public $count_scpmk;
    public $bobot_uts;
    public $bobot_uas;
    public $total_bobot;
    public $kode_semester;
    public $isTrashed;

    public function mount($id, $kode, $rps, $draf, $level_mk, 
                          $deskripsi_rps, $mk_id, $kode_mk, $mk, $akademik, 
                          $count_scpmk, $bobot_uts, $bobot_uas, $total_bobot, $kode_semester, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->rps = $rps;
        $this->draf = $draf;
        $this->level_mk = $level_mk;
        $this->deskripsi_rps = $deskripsi_rps;
        $this->mk_id = $mk_id;
        $this->kode_mk = $kode_mk;
        $this->mk = $mk;
        $this->akademik = $akademik;
        $this->count_scpmk = $count_scpmk;
        $this->bobot_uts = $bobot_uts;
        $this->bobot_uas = $bobot_uas;
        $this->total_bobot = $total_bobot;
        $this->kode_semester = $kode_semester;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.rps-management.toolbar-rps-management');
    }
}
