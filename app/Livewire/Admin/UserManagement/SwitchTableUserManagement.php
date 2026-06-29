<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasStats;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SwitchTableUserManagement extends Component
{
    use HasStats;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithProdiSearchFilters;

    protected $paginationTheme = 'tailwind';

    public $switchTable = '';

    public $searchMode = 'simple';

    public $sortField = 'name';

    public $search = '';

    protected $listeners = ['refresh-table' => 'refreshUsersList',
        'refresh-data-user' => 'refreshUsersList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    public function refreshUsersList()
    {
        $userPrId = Auth::user()?->pr_id ?? 0;
        cache()->forget('stats_user_'.$userPrId.'_active');
        cache()->forget('stats_user_'.$userPrId.'_trashed');
    }

    public $showDeleted = false;

    // protected $queryString = [
    //     'search' => ['except' => ''],
    //     'searchMode' => ['except' => 'simple'],
    //     'perPage' => ['except' => 8],
    //     'filter' => ['except' => ''],
    //     'sortField' => ['except' => 'name'],
    //     'sortDirection' => ['except' => 'asc'],
    //     'filterStatus' => ['except' => ''],
    //     'filterAngkatan' => ['except' => ''],
    //     'showDeleted' => ['except' => false],
    // ];

    public function mount($switchTable = '')
    {
        $this->switchTable = $switchTable;
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

    private function getStatsUser($query)
    {
        // 1. Tentukan kunci unik berdasarkan kondisi query
        $cacheKey = 'stats_user_'.Auth::user()->pr_id.'_'.($this->showDeleted ? 'trashed' : 'active');

        // 2. Gunakan remember (TTL 10 menit / 600 detik)
        return cache()->remember($cacheKey, 600, function () use ($query) {
            $stats = [];

            // Statistik Berdasarkan Prodi User
            $stats['user-prodi'] = (clone $query)->where(function ($q) {
                $q->whereHas('admin.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('dosen.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('mahasiswa.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id));
            })->count();

            $stats['user-opsi'] = (clone $query)->count();

            // Statistik Status
            $stats['user-aktif'] = (clone $query)->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif'));
            })->count();

            $stats['user-non-aktif'] = (clone $query)->where(function ($q) {
                $q->whereHas('admin', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($s) => $s->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', '!=', 'Aktif'));
            })->count();

            // Statistik Global (Dikecualikan dari cloning $query agar tetap global)
            $stats['user'] = User::count();
            $stats['admin'] = User::whereHas('admin')->count();
            $stats['dosen'] = User::whereHas('dosen')->count();
            $stats['mahasiswa'] = User::whereHas('mahasiswa')->count();

            return $stats;
        });
    }

    public function render()
    {
        $countUser = User::query();

        if ($this->showDeleted && $this->AuthCheck('admin')) {
            $countUser->onlyTrashed();
        }

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

        $stats = array_merge($stats, $this->getStatsUser($countUser));

        return view('livewire.admin.user-management.stats-user-management', [
            'stats' => $stats,
        ]);
    }
}
