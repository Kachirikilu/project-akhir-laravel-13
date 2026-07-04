<?php

namespace App\Livewire\Staff;

use App\Http\Services\RekapCapaian;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Global\HasAkreditas;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\NilaiManagement\WithNilaiMahasiswaExcel;
// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSModal;
use App\Models\Akademik\CPL;
use App\Models\Auth\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class NilaiManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;
    // use WithDepartemenSearchFilters;
    // use WithFakultasSearchFilters;
    use WithNilaiMahasiswaExcel;
    use WithPagination;
    use WithProdiSearchFilters;
    // use WithRPSModal;
    // use WithRPSSearchFilters;
    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $perPage = 8;

    public $switchTable = 'mahasiswa';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'desc';

    public $showDeleted = false;

    public $selectedPrId;

    public $selectedDpId;

    public $selectedFkId;

    protected $listeners = [
        'refresh-table' => 'refreshUsersList',
        'refresh-data-rps-mahasiswa' => 'refreshNilaisList',
        'refresh-stats-user' => 'refreshStatsUserList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
        'refresh-stats-user'  => 'refreshStatsUserList',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        // 'switchTable' => ['except' => 'cpl'],
        'filterStatus' => ['except' => ''],

        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'desc'],
        'showDeleted' => ['except' => false],
    ];


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

    #[On('refresh-data-rps-mahasiswa')]
    #[On('refresh-table')]
    public function refreshNilaisList()
    {
        $this->resetPage();
    }
    #[On('refresh-stats-user')]
    public function refreshStatsUserList()
    {
        $this->clearUserStatsCache();
        $this->clearMahasiswaStatsCache();
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function loadingRPSList() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        // $this->reset(['search', 'filterPr']);
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

    // public function placeholder()
    // {
    //     return view('livewire.global.livewire-skeletons.table-placeholder');
    // }

    public function render()
    {

        try {
            // $this->inputPrFilter();
            // $this->inputDpFilter();
            // $this->inputFkFilter();
            
            $queryUser = $this->inputUserSearch('mahasiswa');

            $countMahasiswa = User::whereHas('mahasiswa');

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryUser->onlyTrashed();
                $countMahasiswa->onlyTrashed();
            }

            $users = collect();

            if ($this->searchMode == 'full') {
                $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, null, 1);
            } else {
                $users = $queryUser->paginate($this->perPage);
            }

            $stats = [
                'mahasiswa-prodi' => '🏛️',
                'mahasiswa-opsi' => '⚙️',
                'mahasiswa-aktif' => '🟢',
                'mahasiswa-non-aktif' => '🔴',
            ];

            $stats = array_merge($stats, $this->getStatsMahasiswa($this->showDeleted));

            return view('livewire.staff.nilai-management', [
                'users' => $users,
                'stats' => $stats,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.nilai-management', [
                'users' => User::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
                'stats' => [
                    'mahasiswa-prodi' => '-',
                    'mahasiswa-opsi' => '-',
                    'mahasiswa-aktif' => '-',
                    'mahasiswa-non-aktif' => '-',
                ],
            ]);
        }
    }
}
