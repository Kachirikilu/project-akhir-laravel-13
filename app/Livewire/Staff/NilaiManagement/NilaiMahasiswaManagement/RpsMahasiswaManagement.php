<?php

namespace App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement;

use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithNilaiSearchFilters;
use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RPSMahasiswaManagement\WithNilaiDelete;
use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RPSMahasiswaManagement\WithNilaiModal;
use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\RPSMahasiswaManagement\WithRPSMahasiswaFilters;
// use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\WithNilaiMahasiswaFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Auth\Mahasiswa;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
// use App\Models\Kelas\NilaiMahasiswa;
use App\Livewire\Staff\NilaiManagement\WithNilaiMahasiswaExcel;
use App\Models\Auth\User;
use App\Http\Services\RekapCapaian;
// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSModal;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class RpsMahasiswaManagement extends Component
{
    use HasToast;

    use RekapCapaian;
    use WithNilaiDelete;
    use WithNilaiModal;
    // use WithNilaiMahasiswaFilters;
    use WithNilaiSearchFilters;
    use WithNilaiMahasiswaExcel;
    // use WithRPSModal;

    use WithPagination;
    // use WithKelasDelete;
    use WithRPSMahasiswaFilters;
    use WithRPSShow;



    public $perPage = 8;

    public $switchTable = '';

    public $isNilaiMhs = false;

    protected $paginationTheme = 'tailwind';

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $showDeleted = false;

    // public User $user;

    public Mahasiswa $mahasiswa;

    public $nim_url;

    public $user_id_url;

    public $mahasiswa_id_url;

    public $ganjil_genap_url;

    public $akademik_url;

    public $akademik_fix_url;

    protected $listeners = ['refresh-table' => 'refreshNilaisList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterNilai' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'showDeleted' =>  ['except' => false],
    ];

    public function mount($isNilaiMhs = false, $nim = '', $ganjil_genap = null, $akademik = null)
    {
        $this->isNilaiMhs = $isNilaiMhs; 
        $this->nim_url = $nim;
        $this->ganjil_genap_url = $ganjil_genap;
        $this->akademik_url = $akademik;
        
        $akademik_fix = str_replace('-', '/', $akademik);
        $this->akademik_fix_url = $akademik_fix;

        $sessionKey = $this->isNilaiMhs ? 'rps_mahasiswa_history.history' : 'rps_nilai.history';
        $compositeKey = "{$nim}-{$ganjil_genap}-{$akademik}";

        $cleanupRpsHistory = function() use ($compositeKey) {
            foreach (['rps_nilai.history', 'rps_mahasiswa_history.history'] as $key) {
                $history = session($key, []);
                if (isset($history[$compositeKey])) {
                    unset($history[$compositeKey]);
                    session([$key => $history]);
                }
            }
        };

        $user = User::whereHas('mahasiswa', function ($q) use ($nim) {
            $q->where('mahasiswas.nim', $nim);
        })->first();
        
        if (! $user) {
            $cleanupRpsHistory();
            abort(404, "Mahasiswa dengan NIM $nim tidak ditemukan!");
        }

        $lowGg = strtolower($ganjil_genap);
        if ($lowGg !== 'ganjil' && $lowGg !== 'genap') {
            $cleanupRpsHistory();
            abort(404, 'URL '.$ganjil_genap.' tidak valid! Masukkan "Ganjil" atau "Genap"');
        }
        
        $nilai = NilaiMahasiswa::where('mahasiswa_id', $user->mahasiswa->id)
            ->where('ganjil_genap', $ganjil_genap)
            ->where('tahun_akademik', $akademik_fix)
            ->first();
            
        if (! $nilai) {
            $cleanupRpsHistory();
            abort(404, "Nilai Mahasiswa NIM $nim tidak ditemukan pada Akademik $ganjil_genap $akademik_fix!");
        }
  
        $this->user = $user;
        $this->mahasiswa = $user->mahasiswa;

        $this->user_id_url = $user->id;
        $this->mahasiswa_id_url = $user->mahasiswa->id;

        $rpsHistory = session($sessionKey, []);
        unset($rpsHistory[$compositeKey]);

        $rpsHistory[$compositeKey] = [
            'mahasiswa_id' => $user->mahasiswa->id,
            'nim' => $nim,
            'ganjil_genap' => $ganjil_genap,
            'tahun_akademik' => $akademik_fix,
            'url' => url()->current(),
        ];

        $rpsHistory = array_slice($rpsHistory, -10, null, true);
        uasort($rpsHistory, function ($a, $b) {
            $nimCompare = strcmp($a['nim'], $b['nim']);
            return ($nimCompare !== 0) ? $nimCompare : strcmp($a['ganjil_genap'], $b['ganjil_genap']);
        });

        session([$sessionKey => $rpsHistory]);
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

    public function refreshNilaisList()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterNilai']);
        $this->resetPage();
    }

    public function refreshNilaiList()
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

    //     $targetUrl = route('nilai-management', ['switchTable' => $table]);
    //     if ($table == '' || $table == null) {
    //         $targetPath = '/nilai-management';
    //     } else {
    //         $targetPath = '/nilai-management/'.$table;
    //     }

    //     $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    // }

    public function render()
    {
        try {
            $queryNilai = $this->inputRPSMahasiswaSearch($this->mahasiswa_id_url);

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();
            }

            // if (! empty(trim($this->search))) {
            //     $searchTerm = '%'.trim($this->search).'%';
            //     $queryNilai->where(function ($q) use ($searchTerm) {
            //         $q->whereHas('rps_rel', function ($sub) use ($searchTerm) {
            //             $sub->where('nama_mata_kuliah', 'like', $searchTerm)
            //                 ->orWhere('kode_mata_kuliah', 'like', $searchTerm);
            //         })
            //             ->orWhere('nilai', 'like', $searchTerm);
            //     });
            // }

            // $queryNilai->orderBy($this->sortField, $this->sortDirection);

            // $perPage = $this->perPage ?? 10;
            // $paginatedRps = $queryNilai->paginate($perPage);

            $nilais = $queryNilai->get();
            

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management', [
                // 'nilais' => $paginatedRps,
                'nilais' => $nilais ,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management', [
                'nilais' => new LengthAwarePaginator([], 0, $this->perPage ?? 10),
            ]);
        }
    }
}
