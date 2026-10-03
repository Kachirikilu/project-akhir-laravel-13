<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Models\Auth\UserExcelQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AntrianUserManagement extends Component
{
    use HasToast;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithUserExcel;
    use WithUserModal;

    public $isReady;

    public $parent;

    public $fk_id;

    public $dp_id;

    public string $deleteScope = 'own';

    #[On('open-antrian-user-modal')]
    public function handleAntrianUser()
    {
        $this->isReady = true;
    }

    public function render()
    {
        $antrianImports = UserExcelQueue::latest()->paginate(8);

        return view('livewire.admin.user-management.user-modal.antrian-user-management', [
            'antrianImports' => $antrianImports,
        ]);
    }

    public function deleteAntrianList(string $scope = 'own')
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $user = Auth::user();
        $query = UserExcelQueue::query();

        switch ($scope) {
            case 'all':
                if ($user->tingkat == 1) {
                } else {
                    $this->toast(text: 'Anda tidak memiliki akses untuk menghapus seluruh data!', variant: 'danger');

                    return;
                }
                break;

            case 'fk':
                if ($user->tingkat <= 2) {
                    $query->whereHas('user.admin.pr_rel.dp_rel', function ($q) use ($user) {
                        $q->where('fk_id', $user->fk_id);
                    });
                } else {
                    $this->toast(text: 'Anda tidak memiliki akses tingkat Fakultas!', variant: 'danger');

                    return;
                }
                break;

            case 'dp':
                if ($user->tingkat <= 3) {
                    $query->whereHas('user.admin.pr_rel', function ($q) use ($user) {
                        $q->where('dp_id', $user->dp_id);
                    });
                } else {
                    $this->toast(text: 'Anda tidak memiliki akses tingkat Departemen!', variant: 'danger');

                    return;
                }
                break;

            case 'pr':
                if ($user->tingkat <= 4) {
                    $query->whereHas('user.admin', function ($q) use ($user) {
                        $q->where('pr_id', $user->pr_id);
                    });
                }
                break;

            case 'own':
            default:
                $query->where('user_id', $user->id);
                break;
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->toast(text: 'Tidak ada data antrean yang sesuai untuk dihapus!', variant: 'warning');

            return;
        }

        // 1. Hapus berkas fisik di Storage jika ada
        foreach ($records as $item) {
            if ($item->file_path && Storage::disk('local')->exists($item->file_path)) {
                Storage::disk('local')->delete($item->file_path);
            }
        }

        // 2. Hapus record dari database
        $deletedCount = $records->count();
        $query->delete();

        $this->toast(text: "Berhasil menghapus {$deletedCount} data antrean!", variant: 'success');
    }

    public function loadingAntrianUsersList() {}
}
