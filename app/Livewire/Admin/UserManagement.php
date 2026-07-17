<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithUserSearchFilters;
use App\Models\Auth\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithUserExcel;
    use WithPagination;
    use WithUserFilters;
    use WithUserSearchFilters;

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

    protected $listeners = [
        'refresh-table' => 'refreshUsersList',
        'refresh-data-user' => 'refreshUsersList',
        'refresh-stats-user' => 'refreshStatsUsersList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

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

    #[On('refresh-stats-user')]
    public function refreshStatsUsersList()
    {
        $this->clearObeStatsCache();
        $this->clearDosenStatsCache();
        $this->clearUserStatsCache();
        $this->clearObeProdiStatsCache();
        $this->clearMahasiswaProdiStatsCache();
    }

    public function refreshStats() {
        $this->refreshStatsUsersList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik User berhasil diperbarui!', type: 'info', variant: 'info');
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

    public function updatedSwitchTable()
    {
        $this->dispatch('switch-table-updated', switchTable: $this->switchTable);
    }

    private function syncSortField($table, $sortField)
    {
        $columns = [
            '' => [1 => 'id', 2 => 'role', 3 => 'name', 4 => 'email', 5 => 'identity1', 6 => 'identity2', 7 => 'nidk', 8 => 'nik', 9 => 'status', 10 => 'program_studi', 11 => 'created_at', 12 => 'updated_at'],
            'admin' => [1 => 'id', 2 => 'admin_id', 3 => 'name', 4 => 'email', 5 => 'nip', 6 => 'nitk', 7 => 'nik', 8 => 'kampus', 9 => 'status', 10 => 'created_at', 11 => 'updated_at'],
            'dosen' => [1 => 'id', 2 => 'dosen_id', 3 => 'name', 4 => 'email', 5 => 'nip', 6 => 'nidn', 7 => 'nidk', 8 => 'nik', 9 => 'status', 10 => 'program_studi', 11 => 'created_at', 12 => 'updated_at'],
            'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'name', 4 => 'email', 5 => 'nim', 6 => 'nik', 7 => 'angkatan', 8 => 'kampus', 9 => 'status', 10 => 'program_studi', 11 => 'created_at', 12 => 'updated_at'],
        ];
        $aliases = [
            'name' => ['name'],
            'email' => ['email'],
            'program_studi' => ['program_studi'],
            'kampus' => ['kampus'],
            'status' => ['status'],
            'admin_id' => ['admin_id', 'dosen_id', 'mahasiswa_id'],
            'nik' => ['nik'],
            'identity1' => ['identity1', 'nip', 'nim'],
            'identity2' => ['identity2', 'nitk', 'nidn', 'nik'],
            'identity3' => ['identity3', 'nidk', 'nik'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        // $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = '/user-management'.($table ? '/'.$table : '');
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    // public function sortBy($field)
    // {
    //     if ($this->sortField === $field) {
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //     } else {
    //         $this->sortField = $field;
    //         $this->sortDirection = 'asc';
    //     }
    //     $this->resetPage();
    // }

    // public function placeholder()
    // {
    //     return view('livewire.global.livewire-skeletons.table-placeholder');
    // }

    public function render()
    {
        try {
            $queryUser = $this->inputUserSearch($this->switchTable);

            $stats = [
                'user-prodi' => '🏛️',
                'user-opsi' => '⚙️',
                'user-aktif' => '🟢',
                'user-non-aktif' => '🔴',
                'user' => '👥',
                'admin' => '🛡️',
                'dosen' => '👨‍🏫',
                'mahasiswa' => '🧑‍🎓',
            ];
            
            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryUser->onlyTrashed();
            }
            $stats = array_merge($stats, $this->getStatsUser($this->showDeleted));

            if ($this->searchMode == 'complex') {
                $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $users = $queryUser->paginate($this->perPage);
            }

            return view('livewire.admin.user-management', [
                'users' => $users,
                'stats' => $stats,
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
