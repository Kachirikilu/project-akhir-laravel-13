<?php

namespace App\Livewire\Staff\OBEManagement\ReferensiManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ToolbarReferensiManagement extends Component
{
    public $id;
    public $kode;
    public $kode_ref;
    public $citation;
    public $judul;
    public $penulis;
    public $penerbit;
    public $tahun;
    public $isTrashed;

    public function mount($id, $kode, $kode_ref, $citation, $judul, 
                          $penulis, $penerbit, $tahun, $link, $isTrashed) 
    {
        $this->id = $id;
        $this->kode = $kode;
        $this->kode_ref = $kode_ref;
        $this->citation = $citation;
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->penerbit = $penerbit;
        $this->tahun = $tahun;
        $this->link = $link;
        $this->isTrashed = $isTrashed;
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-toolbars.skeleton-toolbar');
    }

    public function render()
    {
        return view('livewire.staff.obe-management.ref-management.toolbar-referensi-management');
    }
}
