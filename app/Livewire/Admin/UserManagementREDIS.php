<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Models\Auth\User;
use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class UserManagement extends Component
{
    use HasSortir;
    use HasStats;
    use HasToast;

    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithUserDelete;
    use WithUserExcel;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $perPage = 8;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $switchTable = '';

    public $searchMode = 'simple';

    protected $listeners = ['refresh-table' => 'refreshUsersList',
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
    ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
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

    public function resetInputFilter()
    {
        $this->reset(['search', 'searchAngkatan']);
        $this->resetPage();
    }

    public function refreshUsersList()
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

    private function syncSortField2($filter, $sortField)
    {
        $this->sortField = match (true) {
            $filter != '' && $sortField == 'role' => 'name',
            $filter != 'mahasiswa' && $sortField == 'angkatan' => 'status',
            $filter == 'mahasiswa' && in_array($sortField, ['identity2', 'identity3']) => 'identity1',
            $filter != 'dosen' && $sortField == 'identity3' => ($filter == 'admin' ? 'identity2' : 'identity1'),

            default => $sortField
        };

        $idFields = ['admin_id', 'dosen_id', 'mahasiswa_id'];

        if (in_array($this->sortField, $idFields)) {
            $this->sortField = match ($filter) {
                'admin' => 'admin_id',
                'dosen' => 'dosen_id',
                'mahasiswa' => 'mahasiswa_id',
                default => 'id',
            };
        }
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

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = '/user-management'.($table ? '/'.$table : '');
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            $this->inputPrFilter();
            $this->inputDpFilter();
            $this->inputFkFilter();

            $cacheKey = 'user_management_' . md5(json_encode([
                'switchTable'    => $this->switchTable,
                'showDeleted'    => $this->showDeleted,
                'searchMode'     => $this->searchMode,
                'search'         => $this->search,
                'searchAngkatan' => $this->searchAngkatan,
                'perPage'        => $this->perPage,
                'page'           => $this->page ?? 1,
                'sortField'      => $this->sortField,
                'sortDirection'  => $this->sortDirection,
            ]));

            $cachedData = Cache::remember($cacheKey, now()->addMinutes(10), function () {
                $queryUser = $this->inputUserSearch($this->switchTable);
                
                $countUser = User::query();
                if (! empty($this->switchTable)) {
                    $queryUser->whereHas($this->switchTable);
                }
                if ($this->showDeleted && $this->AuthCheck('admin')) {
                    $queryUser->onlyTrashed();
                }

                $baseStats = [
                    'user-prodi' => '🏛️', 'user-opsi' => '⚙️', 'user-aktif' => '🟢', 
                    'user-non-aktif' => '🔴', 'user' => '👥', 'admin' => '🛡️', 
                    'dosen' => '👨‍🏫', 'mahasiswa' => '🧑‍🎓',
                ];
                $stats = array_merge($baseStats, $this->getStatsUser($countUser));

                if ($this->searchMode == 'full') {
                    $paginator = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection);
                } else {
                    $paginator = $queryUser->paginate($this->perPage);
                }

                $itemsArray = collect($paginator->items())->map(function ($user) {
                    $data = $user->toArray();
                    $data['is_trashed'] = $user->trashed();
                    return $data;
                })->toArray();

                return [
                    'items'       => $itemsArray,
                    'total'       => $paginator->total(),
                    'perPage'     => $paginator->perPage(),
                    'currentPage' => $paginator->currentPage(),
                    'stats'       => $stats
                ];
            });

            $hydratedItems = User::hydrate($cachedData['items'] ?? []);

            $formattedItems = $hydratedItems->map(function ($user) {
                if (isset($user->admin) && is_array($user->admin)) {
                    $user->setRelation('admin', (object) $user->admin);
                }
                if (isset($user->dosen) && is_array($user->dosen)) {
                    $user->setRelation('dosen', (object) $user->dosen);
                }
                if (isset($user->mahasiswa) && is_array($user->mahasiswa)) {
                    $user->setRelation('mahasiswa', (object) $user->mahasiswa);
                }
                if (isset($user->pr_rel) && is_array($user->pr_rel)) {
                    $user->setRelation('pr_rel', (object) $user->pr_rel);
                }
                if (isset($user->is_trashed) && $user->is_trashed) {
                    $user->deleted_at = now(); 
                }
                return $user;
            });

            $users = new LengthAwarePaginator(
                $formattedItems,
                $cachedData['total'] ?? 0,
                $cachedData['perPage'] ?? $this->perPage,
                $cachedData['currentPage'] ?? 1,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );

            return view('livewire.admin.user-management', [
                'users' => $users,
                'stats' => $cachedData['stats'] ?? [],
            ]);

        } catch (\Throwable $e) {
            $message = 'Terjadi kesalahan sistem: ' . $e->getMessage();
            session()->flash('error', $message);
            
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.admin.user-management', [
                'users' => \App\Models\Auth\User::whereRaw('1=0')->paginate($this->perPage),
                'totalUserProdi' => '-',
                'stats' => [
                    'user-prodi' => '-', 'user-opsi' => '-', 'user-aktif' => '-', 'user-non-aktif' => '-',
                    'user' => '-', 'admin' => '-', 'dosen' => '-', 'mahasiswa' => '-',
                ],
            ]);
        }
    }
}
