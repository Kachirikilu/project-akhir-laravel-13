<?php

namespace App\Livewire\Admin\ProdiManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarProdiManagement extends Component
{
    public $id;
    public $dp_id;
    public $fk_id;
    public $kode;
    public $kode_short;
    public $kode_dp;
    public $kode_fk;
    public $strata;
    public $prodi;
    public $departemen;
    public $fakultas;
    public $departemen_dp;
    public $fakultas_fk;
    public $switchTable;
    public $isTrashed;

    public function mount($id, $dp_id, $fk_id, $kode, $kode_short, $kode_dp, 
                          $kode_fk, $strata, $prodi, $departemen, $fakultas, $departemen_dp, $fakultas_fk, $switchTable, $isTrashed) 
    {
        $this->id = $id;
        $this->dp_id = $dp_id;
        $this->fk_id = $fk_id;
        $this->kode = $kode;
        $this->kode_short = $kode_short;
        $this->kode_dp = $kode_dp;
        $this->kode_fk = $kode_fk;
        $this->strata = $strata;
        $this->prodi = $prodi;
        $this->departemen = $departemen;
        $this->fakultas = $fakultas;
        $this->departemen_dp = $departemen_dp;
        $this->fakultas_fk = $fakultas_fk;
        $this->switchTable = $switchTable;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }


    public function render()
    {
        return view('livewire.admin.prodi-management.toolbar-prodi-management');
    }
}
