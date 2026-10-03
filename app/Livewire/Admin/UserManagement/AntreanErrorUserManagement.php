<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Models\Auth\UserExcelQueue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AntreanErrorUserManagement extends Component
{
    use HasToast;
    use WithPagination;
    use WithUserExcel;

    public $isReady = false;

    public $parent;

    public bool $showErrorUserAntreanModal = false;

    public ?int $selectedImportId = null;

    #[On('open-error-antrean-user-modal')]
    public function handleErrorAntreanUser($id)
    {
        $this->isReady = true;
        $this->showErrorDetail($id);
    }

    public function showErrorDetail(int $id)
    {
        $importRecord = UserExcelQueue::find($id);

        if (! $importRecord) {
            $this->toast(text: 'Data antrean tidak ditemukan!', variant: 'danger');

            return;
        }

        $this->selectedImportId = $id;
        $this->resetPage();
        $this->showErrorUserAntreanModal = true;
    }

    public function render()
    {
        $errorAntreans = null;

        if ($this->selectedImportId) {
            $importRecord = UserExcelQueue::find($this->selectedImportId);

            if ($importRecord && $importRecord->row_errors) {
                $rawErrors = $importRecord->row_errors;

                if (is_string($rawErrors)) {
                    $rawErrors = json_decode($rawErrors, true) ?? [];
                }

                $errorsArray = is_array($rawErrors) ? array_values($rawErrors) : [];
                
                $perPage = 8;
                $currentPage = $this->getPage();
                $offset = ($currentPage - 1) * $perPage;
                $currentPageItems = array_slice($errorsArray, $offset, $perPage);

                $errorAntreans = new LengthAwarePaginator(
                    $currentPageItems,
                    count($errorsArray),
                    $perPage,
                    $currentPage,
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        // 'pageName' => 'errorAntreanPage',
                    ]
                );
            }
        }

        return view('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input-partial.error-antrean-user-management', [
            'errorAntreans' => $errorAntreans,
        ]);
    }

    public function loadingErrorAntreanUsersList() {}
}