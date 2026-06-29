<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithUserSearchFilters;

use App\Models\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithPagination;

    use WithUserFilters;
    use WithUserSearchFilters;
    use WithUserDelete;

    public $perPage = 8;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $switchTable = '';

    public $searchMode = 'simple';

    public $selectedPrId;

    public $selectedDpId;

    public $selectedFkId;

    // public $userModal;

    // public $selectedId;

    // public $userIdModal;

    // public $prResults = [];
    // public $pr_id;
    // public $prNameSearch;
    // public $itemsPr;

    protected $listeners = ['refresh-table' => 'refreshUsersList', 'refresh-table' => 'refresh-data-user',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    public $showDeleted = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        // 'switchTable' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterAngkatan' => ['except' => ''],
        'showDeleted' => ['except' => false],

        // 'pr_name' => ['except' => ''],
        // 'roleType' => ['except' => ''],
        // 'isEditing' => ['except' => false],
        // 'showUserModal' => ['except' => false],
        // 'userId' => ['except' => ''],
        // 'email' => ['except' => ''],
        // 'name' => ['except' => ''],
        // 'nip' => ['except' => ''],
        // 'nim' => ['except' => ''],
        // 'angkatan' => ['except' => ''],
        // 'pr_id' => ['except' => ''],
        // 'prNameSearch' => ['except' => ''],
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
    }

    #[On('switch-table-updated')]
    public function updateSwitchTable($switchTable)
    {
        $this->switchTable = $switchTable;
    }

    #[On('selected-pr-id-updated')]
    public function updateSelectedPrId($selectedPrId)
    {
        $this->selectedPrId = $selectedPrId;
    }

    #[On('selected-dp-id-updated')]
    public function updateSelectedDpId($selectedDpId)
    {
        $this->selectedDpId = $selectedDpId;
    }

    #[On('selected-fk-id-updated')]
    public function updateSelectedFkId($selectedFkId)
    {
        $this->selectedFkId = $selectedFkId;
    }

    #[On('refresh-data-user')]
    #[On('refresh-table')]
    public function refreshUsersList()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'searchAngkatan']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }





    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        try {
            $queryUser = $this->inputUserSearch($this->switchTable);

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryUser->onlyTrashed();
            }

            if ($this->searchMode == 'full') {
                $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $users = $queryUser->paginate($this->perPage);
            }

            return view('livewire.admin.user-management', [
                'users' => $users,
                'stats' => [
                    'user-prodi' => '-',
                    'user-opsi' => '-',
                    'user-aktif' => '-',
                    'user-non-aktif' => '-',

                    'user' => '-',
                    'admin' => '-',
                    'dosen' => '-',
                    'mahasiswa' => '-',
                ],
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.user-management', [
                'users' => User::whereRaw('1=0')->paginate($this->perPage),
                'stats' => [
                    'user-prodi' => '-',
                    'user-opsi' => '-',
                    'user-aktif' => '-',
                    'user-non-aktif' => '-',

                    'user' => '-',
                    'admin' => '-',
                    'dosen' => '-',
                    'mahasiswa' => '-',
                ],
            ]);
        }
    }
}
