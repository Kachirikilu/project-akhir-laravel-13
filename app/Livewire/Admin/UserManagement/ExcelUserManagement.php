<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\HasToast;
use Livewire\Attributes\On;
use Livewire\Component;

class ExcelUserManagement extends Component
{
    use HasToast;
    use WithProdiSearchFilters;
    use WithUserExcel;
    use WithUserModal;

    #[On('open-excel-user-modal')]
    public function handleExcelUser()
    {
        $this->addUser('excel');
    }

    public function render()
    {
        return view('livewire.admin.user-management.excel-user-management');
    }
}
