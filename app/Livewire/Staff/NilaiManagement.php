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
use App\Livewire\Global\WithCPLSearchFilters;
use App\Livewire\Global\WithCPMKSearchFilters;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithReferensiSearchFilters;
// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
// use App\Livewire\Staff\OBEManagement\WithRPSDelete;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Global\WithSubCPMKSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLDelete;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLFilters;
use App\Livewire\Staff\OBEManagement\CPLManagement\WithCPLModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithCPMKModal;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKDelete;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKFilters;
use App\Livewire\Staff\OBEManagement\CPMKManagement\WithSubCPMKModal;
use App\Livewire\Staff\OBEManagement\ReferensiManagement\WithRefModal;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSDelete;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSModal;
use App\Models\Akademik\CPL;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Prodi;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiManagement extends Component
{
    use HasAkreditas;
    use HasSortir;
    use HasStats;
    use HasToast;
    use RekapCapaian;
    use WithCPLDelete;
    use WithCPLFilters;
    use WithCPLModal;
    use WithCPLSearchFilters;
    use WithCPMKDelete;
    use WithCPMKFilters;
    use WithCPMKModal;
    use WithCPMKSearchFilters;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithMKSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithReferensiSearchFilters;
    use WithRefModal;
    use WithRPSDelete;
    use WithRPSFilters;
    use WithRPSModal;
    use WithRPSSearchFilters;
    use WithSubCPMKDelete;
    use WithSubCPMKFilters;
    use WithSubCPMKModal;
    use WithSubCPMKSearchFilters;
    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = 'mahasiswa';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshProdisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterPr' => ['except' => ''],
        // 'switchTable' => ['except' => 'cpl'],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
    ];

    // public function mount($switchTable = '')
    // {
    //     $this->switchTable = $switchTable;
    // }

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

    public function refreshProdisList()
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

    private function syncSortField($table, $sortField)
    {
        $columns = [
            'rps' => [1 => 'id', 2 => 'kode', 3 => 'akademik', 4 => 'rekap_rps_pr', 5 => 'index_rps_pr', 6 => 'mutu_rps_pr', 7 => 'kode_mk', 8 => 'mk', 9 => 'semester', 10 => 'sks', 11 => 'sks_text', 12 => 'is_wajib', 13 => 'is_draf', 14 => 'revisi'],
            'cpl' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpl_pr', 5 => 'index_cpl_pr', 5 => 'mutu_cpl_pr', 6 => 'count_rps_pr', 7 => 'count_rps'],
            'cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_cpmk_pr', 5 => 'index_cpmk_pr', 6 => 'mutu_cpmk_pr', 7 => 'count_cpl'],
            'sub-cpmk' => [1 => 'id', 2 => 'kode', 3 => 'deskripsi', 4 => 'rekap_scpmk_pr', 5 => 'index_scpmk_pr', 6 => 'mutu_scpmk_pr', 5 => 'metode', 6 => 'materi', 7 => 'metodologi', 8 => 'indikator'],
            'mahasiswa' => [1 => 'id', 2 => 'mahasiswa_id', 3 => 'kode', 4 => 'name', 5 => 'rekap_mhs', 6 => 'index_mhs', 7 => 'mutu_mhs', 8 => 'count_rps', 9 => 'total_sks', 10 => 'angkatan', 11 => 'status'],
        ];

        $aliases = [
            'rekap_rps_pr' => ['rekap_cpl_pr', 'rekap_cpmk_pr', 'rekap_scpmk_pr', 'rekap_mhs'],
            'index_rps_pr' => ['index_cpl_pr', 'index_cpmk_pr', 'index_scpmk_pr', 'index_mhs'],
            'mutu_rps_pr' => ['mutu_cpl_pr', 'mutu_cpmk_pr', 'mutu_scpmk_pr', 'mutu_mhs'],

            'kode' => ['kode', 'name'],
            // 'name' => ['name', 'kode'],
            'deskripsi' => ['deskripsi', 'mk'],
            // 'mk' => ['mk', 'deskripsi'],
            'materi' => ['materi'],
            // 'count_rps' => ['count_rps', 'count_cpl'],
            'count_cpl' => ['count_cpl', 'count_rps', 'total_sks'],
            // 'akademik' => ['akademik', 'total_bobot'],
            // 'total_bobot' => ['total_bobot', 'akademik'],
            'is_draf' => ['is_draf', 'indikator'],
            // 'indikator' => ['indikator', 'is_draf'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->dispatch('table-switched', switchTable: $table);
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $allFilters = [
            'rps' => 'filterRPS',
            'cpl' => 'filterCPL',
            'cpmk' => 'filterCPMK',
            'sub-cpmk' => 'filterSCPMK',
            'mahasiswa' => 'filterUser',
        ];

        foreach ($allFilters as $tableParam => $filterVariable) {
            if ($tableParam !== $this->switchTable) {
                $this->$filterVariable = '';
            }
        }

        $limits = [
            'cpl' => 100,
            'rps' => 200,
            'mahasiswa' => 200,
            'cpmk' => 300,
            'sub-cpmk' => 500,
        ];

        if (isset($limits[$table])) {
            $this->perPage = min((int) $this->perPage, $limits[$table]);
        }

        // $targetPath = '/program-studi-management/kode/'.($table ? '/'.$table : '');
        $suffix = ($table && $table !== 'cpl') ? "/{$table}" : '';

        $targetPath = "/program-studi-management/kode/{$this->kode_pr_url}{$suffix}";

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        $queryUser = $this->inputUserSearch('mahasiswa');

        // $this->inputPrFilter();
        // $this->inputRPSFilter();
        // $this->inputCPLFilter();
        // $this->inputCPMKFilter();

        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();

        try {
            $countMahasiswa = User::query();

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $countMahasiswa->onlyTrashed();
            }

            $users = collect();

            $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, null, 1);
            $users = $queryUser->paginate($this->perPage);

            $stats = [
                'mahasiswa-aktif' => '🟢',
                'mahasiswa-non-aktif' => '🔴',
            ];

            $stats = array_merge($stats, $this->getStatsMahasiswa($countMahasiswa));

            return view('livewire.staff.nilai-management', [
                'users' => $users,
                'user_rps_modal_paginator' => $this->user_rps_modal_paginator,
                'stats' => $stats,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.nilai-management', [
                'users' => User::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
                'mahasiswa_rps_modal_paginator' => collect(),

                'stats' => [
                    'mahasiswa-aktif' => '-',
                    'mahasiswa-non-aktif' => '-',
                ],
            ]);
        }
    }
}
