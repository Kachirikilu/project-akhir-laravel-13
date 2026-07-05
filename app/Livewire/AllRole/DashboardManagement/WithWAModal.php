<?php

namespace App\Livewire\AllRole\DashboardManagement;

use App\Livewire\Global\HasToast;
use Illuminate\Support\Facades\Auth;

trait WithWAModal
{
    use HasToast;

    public $showWAModal = false;

    public function editWA()
    {
        $formattedPhone = $this->formatNomorHP(Auth::user()->no_hp);
        $this->user_input['no_hp_back'] = $formattedPhone['no_hp_back'];
        $this->user_input['kode_no_hp'] = $formattedPhone['kode_no_hp'];
    }

    public function updatedUserInputNoHpBack($value)
    {
        // 1. Bersihkan Data
        $rawNoHp = ($this->user_input['kode_no_hp'] ?? '') . $value;
        $cleanNoHp = str_replace([' ', '-', '+', '/'], '', $rawNoHp);
        $this->user_input['no_hp'] = $cleanNoHp;

        $this->validate([
            'user_input.no_hp' => 'required|numeric|digits_between:11,15',
        ], $this->validationMessagesWA());

        $user = Auth::user();
        $roleRelation = strtolower($user->role);
        
        if ($user->{$roleRelation}) {
            $user->{$roleRelation}->update([
                'no_hp' => $cleanNoHp,
                'is_wa_active' => 0,
                'wa_limit' => 0,
            ]);
        }

        session()->flash('message', 'Nomor WhatsApp telah diperbarui! Silahkan verifikasi dari WhatsApp Anda untuk aktifkan!');
        $this->dispatch('refresh-data-dashboard');
    }

    public function validationMessagesWA()
    {
        return [
            'user_input.no_hp.digits_between' => 'Nomor WhatsApp harus antara 11 sampai 15 digit!',
            'user_input.no_hp.numeric' => 'Nomor harus berupa angka!',
        ];
    }

    // public function resetInputWA(
    // ) {
    //     $fields = [
    //         'user_input',
    //     ];

    //     $this->reset($fields);
    //     $this->resetErrorBag();
    // }
}
