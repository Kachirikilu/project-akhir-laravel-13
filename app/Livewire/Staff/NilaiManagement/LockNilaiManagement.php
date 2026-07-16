<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class LockNilaiManagement extends Component
{
    use HasToast;

    public $showLockNilaiModal;

    public $isReady;

    public $nilai_input = [
        'ganjil_genap' => '',
        'akademik' => '',
        'akademik_1' => '',
        'akademik_2' => '',
        'akademik_t' => '',
        'tanggal_unlock' => '',
        'jenis_kelamin' => '',
        'angkatan' => '',
    ];

    #[On('open-edit-lock-nilai-modal')]
    public function handleEditLockNilai()
    {
        $this->isReady = true;
        $this->editLockNilai();
    }

    public function editLockNilai() {
        $this->nilai_input['akademik'] = '2012/2013';
        $this->nilai_input['akademik_1'] = '2012';
        $this->nilai_input['akademik_2'] = '2013';
        $this->nilai_input['akademik_t'] = '2013';
        $this->nilai_input['jenis_kelamin'] = 'Perempuan';
        $this->nilai_input['angkatan'] = '2013';
    }

    public function render()
    {
        return view('livewire.staff.nilai-management.lock-nilai-management');
    }
}
