<?php

use App\Livewire\Admin\UserManagement\WithUserExcel;

it('normalizes queue import mode from the same source as the form', function () {
    $component = new class
    {
        use WithUserExcel;

        public function __construct()
        {
            $this->user_input = [
                'role' => 'Mahasiswa',
                'update_or_create' => 'nik',
            ];
            $this->update_or_create_mode = true;
        }
    };

    $settings = $component->resolveExcelQueueImportSettings();

    expect($settings)->toMatchArray([
        'role' => 'Mahasiswa',
        'update_or_create_mode' => true,
        'update_or_create' => 'nik',
    ]);
});
