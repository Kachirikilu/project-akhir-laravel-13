<?php

namespace App\Livewire\Admin;

use App\Livewire\Global\HasSortir;
use App\Livewire\Admin\ProdiManagement\WithDepartemenFilters;
use App\Livewire\Admin\ProdiManagement\WithFakultasFilters;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserExcel;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use HasToast;
    use HasSortir;
    use WithDepartemenFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasFilters;
    use WithFakultasSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithUserDelete;
    use WithUserExcel;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $showModal = false;

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
        $this->reset(['search', 'switchTable']);
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
            'dosen_id' => ['dosen_id', 'admin_id', 'mahasiswa_id'],
            'mahasiswa_id' => ['mahasiswa_id', 'admin_id', 'dosen_id'],
            'nik' => ['nik'],
            'identity1' => ['identity1', 'nip', 'nim'],
            'nip' => ['nip', 'nim', 'identity1'],
            'nim' => ['nim', 'nip', 'identity1'],
            'identity2' => ['identity2', 'nitk', 'nidn', 'nik'],
            'nitk' => ['nitk', 'nidn', 'identity2', 'nik'],
            'nidn' => ['nidn', 'nitk', 'identity2', 'nik'],
            'identity3' => ['identity3', 'nidk', 'nik'],
            'nidk' => ['nidk', 'identity3', 'nik'],
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
        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();

        try {
            $queryUser = $this->inputUserSearch();

            // =========================
            // 1. BASE MURNI (JANGAN DIUBAH)
            // =========================
            $baseUser = User::query();

            $this->buttonUserFilter($queryUser);

            if (! empty($this->switchTable)) {
                $queryUser->whereHas($this->switchTable);
            }

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryUser->onlyTrashed();
            }

            // =========================
            // 3. STATS QUERY (SELALU CLEAN)
            // =========================
            $statsPr = User::query();
            $statsAll = User::query();
            $statsAktif = User::query();
            $statsNonAktif = User::query();

            // =========================
            // SWITCH TABLE APPLY KE STATS (HANYA ROLE FILTER)
            // =========================
            if (! empty($this->switchTable)) {
                $statsPr->whereHas($this->switchTable);
                $statsAll->whereHas($this->switchTable);
                $statsAktif->whereHas($this->switchTable);
                $statsNonAktif->whereHas($this->switchTable);
            }

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $statsPr->onlyTrashed();
                $statsAll->onlyTrashed();
                $statsAktif->onlyTrashed();
                $statsNonAktif->onlyTrashed();
            }

            // =========================
            // STATUS FILTER DIPISAH PER STAT (INI KUNCI 🔥)
            // =========================
            $statsPr->where(function ($q) {
                $q->whereHas('admin.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('dosen.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('mahasiswa.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id));
            });
            $statsAktif->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif'));
            });

            $statsNonAktif->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', '!=', 'Aktif'));
            });

            if ($this->searchMode == 'full') {
                $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                 $users = $queryUser->paginate($this->perPage);
            }
            // =========================
            // RESULT VIEW
            // =========================
            return view('livewire.admin.user-management', [
                'users' => $users,
                //  'users' => User::where('id', 'like', '%' . $this->search . '%')
                // ->orWhere('email', 'like', '%' . $this->search . '%')
                // ->paginate(10),

                'totalUserProdi' => $statsPr->count(),
                'totalAllOpsi' => $statsAll->count(),
                'totalAktif' => $statsAktif->count(),
                'totalNonAktif' => $statsNonAktif->count(),

                'totalUsers' => User::count(),
                'totalAdmins' => User::whereHas('admin')->count(),
                'totalDosens' => User::whereHas('dosen')->count(),
                'totalMahasiswas' => User::whereHas('mahasiswa')->count(),
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.admin.user-management', [
                'users' => User::whereRaw('1=0')->paginate($this->perPage),

                'totalUserProdi' => '-',
                'totalAllOpsi' => '-',
                'totalAktif' => '-',
                'totalNonAktif' => '-',

                'totalUsers' => '-',
                'totalAdmins' => '-',
                'totalDosens' => '-',
                'totalMahasiswas' => '-',
            ]);
        }
    }
}
