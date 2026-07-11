<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithNilaiSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\WithNilaiMahasiswaFilters;
// use App\Models\Kelas\NilaiMahasiswa;
use App\Livewire\Staff\NilaiManagement\WithNilaiMahasiswaExcel;

// use App\Livewire\Admin\UserManagement\WithUserDelete;
// use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Http\Services\RekapCapaian;

use App\Models\Auth\User;
use App\Models\Auth\Mahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiMahasiswaManagement extends Component
{
    use HasToast;
    use RekapCapaian;
    // use WithKelasDelete;
    use WithNilaiMahasiswaFilters;
    use WithNilaiSearchFilters;
    use WithNilaiMahasiswaExcel;

    // use WithUserDelete;
    // use WithUserModal;

    use WithPagination;



    public $perPage = 8;

    public $switchTable = '';

    public $isNilaiMhs = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'semester';

    public $sortDirection = 'desc';

    public $showDeleted = false;

    // public User $user;

    public Mahasiswa $mahasiswa;

    public $nim_url;

    public $user_id_url;

    public $mahasiswa_id_url;

    protected $listeners = ['refresh-table' => 'refreshPeriodesList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterNilai' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'semester'],
        'sortDirection' => ['except' => 'desc'],
        'showDeleted' =>  ['except' => false],
    ];

    public function mount($isNilaiMhs = false, $nim = '')
    {
        $this->isNilaiMhs = $isNilaiMhs; 
        $this->nim_url = $nim;

        $user = User::whereHas('mahasiswa', function ($q) use ($nim) {
            $q->where('mahasiswas.nim', $nim);
        })->first();

        if (! $user) {
            foreach (['nilai.history', 'nilai_mahasiswa.history'] as $key) {
                $history = session($key, []);
                if (isset($history[$nim])) {
                    unset($history[$nim]);
                    session([$key => $history]);
                }
            }
            abort(404, "Mahasiswa dengan NIM $nim tidak ditemukan!");
        }

        $this->mahasiswa = $user->mahasiswa;
        $mahasiswaId = $this->mahasiswa->id;

        $this->user_id_url = $user->id;
        $this->mahasiswa_id_url = $user->mahasiswa->id;

        $sessionKey = $this->isNilaiMhs ? 'nilai_mahasiswa.history' : 'nilai.history';
        $nilaiHistory = session($sessionKey, []);

        $existingKey = array_search($mahasiswaId, array_column($nilaiHistory, 'mahasiswa_id'));
        if ($existingKey !== false) {
            $actualKeys = array_keys($nilaiHistory);
            unset($nilaiHistory[$actualKeys[$existingKey]]);
        }

        unset($nilaiHistory[$nim]);
        $nilaiHistory[$nim] = [
            'mahasiswa_id' => $mahasiswaId,
            'nim' => $nim,
            'nama_mahasiswa' => $user->name, 
            'url' => url()->current(),
        ];

        $nilaiHistory = array_slice($nilaiHistory, -5, null, true);
        uasort($nilaiHistory, fn ($a, $b) => strcmp($a['nim'], $b['nim']));

        session([$sessionKey => $nilaiHistory]);
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
        $this->reset(['search', 'filterNilai']);
        $this->resetPage();
    }

    public function refreshPeriodesList()
    {
        $this->resetPage();
    }
    public function refreshStats() {
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

    // private function syncSortField($table, $sortField)
    // {
    //     $map = [
    //         'tatap_muka' => 'sks_tm',
    //         'praktikum' => 'sks_pr',
    //         'praktek_lapangan' => 'sks_pl',
    //         'simulasi' => 'sks_sm',
    //     ];

    //     if (isset($map[$table]) && str_starts_with($sortField, 'sks_')) {
    //         $this->sortField = $map[$table];
    //     }
    // }

    // public function switchingTable($table)
    // {
    //     $this->switchTable = $table;
    //     $this->syncSortField($table, $this->sortField);

    //     $this->resetPage();

    //     $targetUrl = route('nilai-mahasiswa-management', ['switchTable' => $table]);
    //     if ($table == '' || $table == null) {
    //         $targetPath = '/nilai-mahasiswa-management';
    //     } else {
    //         $targetPath = '/nilai-mahasiswa-management/'.$table;
    //     }

    //     $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    // }

    public function render()
    {
        try {
            $angkatan = $this->mahasiswa?->angkatan ? (int) $this->mahasiswa->angkatan : null;

            $queryNilai = $this->inputNilaiSemesterSearch($this->mahasiswa_id_url);

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();
            }

            $allNilaiRaw = $queryNilai->get();
            $calculatedPeriode = $this->addIPSemester($allNilaiRaw, $angkatan);

            // $perPage = $this->perPage ?? 8;
            // $paginatedPeriode = $this->searchOutputNilai(
            //     $calculatedPeriode,
            //     // $this->search,
            //     null,
            //     $perPage,
            //     null,
            //     // $this->sortField,
            //     $this->sortDirection
            // );

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management', [
                'periodes' => $calculatedPeriode,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management', [
                'periodes' => new LengthAwarePaginator([], 0, $this->perPage ?? 8),
            ]);
        }
    }
}
