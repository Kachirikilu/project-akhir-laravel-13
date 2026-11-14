<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Http\Services\RekapCapaianService;
use App\Livewire\Global\HasAkreditas;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;

use App\Livewire\Global\HasToast;
// use App\Models\Kelas\NilaiMahasiswa;

use App\Livewire\Global\WithNilaiSearchFilters;
use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\WithNilaiMahasiswaFilters;
// use App\Livewire\Admin\UserManagement\WithUserDelete;
// use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\Staff\ObeManagement\CplManagement\WithCPLFilters;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NilaiMahasiswaManagement extends Component
{
    use HasAkreditas;
    use HasToast;
    use RekapCapaianService;

    use WithCPLFilters;
    use WithNilaiMahasiswaExcel;
    // use WithKelasDelete;
    use WithNilaiMahasiswaFilters;
    use WithNilaiSearchFilters;

    // use WithUserDelete;
    // use WithUserModal;

    use WithPagination;
    use WithPagination;

    public $perPage = 8;

    public $showMore = false;

    public $switchTable = 'nilai';

    public $search = '';

    public $searchMode = 'simple';

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
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'showMore' => ['except' => false],
        'filterNilai' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'semester'],
        'sortDirection' => ['except' => 'desc'],
        'showDeleted' => ['except' => false],
    ];

    public function mount($isNilaiMhs = false, $nim = '', $switchTable = 'nilai')
    {
        $this->isNilaiMhs = $isNilaiMhs;
        $this->nim_url = $nim;

        if (empty($switchTable)) {
            if (Auth::user()->admin || Auth::user()->dosen) {
                return $this->redirectRoute('nilai-mahasiswa-management', [
                    'nim' => $nim,
                    'switchTable' => 'nilai',
                ], navigate: true);
            } elseif (Auth::user()->mahasiswa) {
                return $this->redirectRoute('nilai-mahasiswa', [
                    'switchTable' => 'nilai',
                ], navigate: true);
            }
        }

        $this->switchTable = $switchTable;

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

    public function refreshStats()
    {
        $this->resetPage();
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->resetPage();

        $suffix = $table ? "/{$table}" : '';

        if (Auth::user()->admin || Auth::user()->dosen) {
            $targetPath = "/nilai-capaian/nim/{$this->nim_url}{$suffix}";
        } elseif (Auth::user()->mahasiswa) {
            $targetPath = "/nilai-mahasiswa{$suffix}";
        }

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            $angkatan = $this->mahasiswa?->angkatan ? (int) $this->mahasiswa->angkatan : null;

            $queryNilai = collect();
            $queryCPL = collect();

            $queryNilai = $this->inputNilaiSearch($this->mahasiswa_id_url);

            if ($this->switchTable == 'cpl') {
                // $queryUser = $this->inputUserSearch('mahasiswa');
                // $this->addCountRpsMahasiswa($queryUser, 'count_rps');
                // $this->addTotalSksMahasiswa($queryUser, 'total_sks');
                $queryCPL = $this->inputCPLSearch(null, $this->mahasiswa_id_url);
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryNilai->onlyTrashed();

                if ($this->switchTable == 'cpl') {
                    $queryCPL->onlyTrashed();
                }
            }

            $data = [
                'periodes' => collect(),
                'cpl' => collect(),
            ];
            $allNilaiRaw = $queryNilai->get();
            $data['periodes'] = $this->addIPSemester($allNilaiRaw, $angkatan);

            if ($this->switchTable == 'cpl') {
                // $now = now();
                // $sixMonthsAgo = now()->subMonths(6);
                // $currentYear = now()->year;
                // $fiveYearsAgo = now()->subYears(5);

                $this->addCountRpsCpl($queryCPL, null, $this->mahasiswa_id_url, 'count_rps_mhs');
                $this->addCountRpsCpl($queryCPL, null, null, 'count_rps');

                // $this->addRekapOBE($queryCPL, $prId, 'rekap_cpl_pr', 'rekap_cpl_prodi', 'cpl_id', 'cpls');
                $this->addRekapOBE($queryCPL, $this->mahasiswa_id_url, 'rekap_cpl_mhs', 'rekap_cpl_mahasiswa', 'cpl_id', 'cpls', 'mahasiswa_id');
                $this->addIndexOBE($queryCPL, $this->mahasiswa_id_url, 'index_cpl_mhs', 'rekap_cpl_mahasiswa', 'cpl_id', 'cpls', 'mahasiswa_id');
                $this->addAkreditasOBE($queryCPL, $this->mahasiswa_id_url, 'huruf_cpl_mhs', 'rekap_cpl_mahasiswa', 'cpl_id', 'cpls', 'mahasiswa_id');
                // $this->buttonCPLFilter($queryCPL, $now, $sixMonthsAgo, $currentYear, $fiveYearsAgo);

                if ($this->searchMode == 'complex') {
                    $data['cpl'] = $this->searchOutputCPL($queryCPL, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
                } else {
                    $data['cpl'] = $queryCPL->paginate($this->perPage);
                }
            }

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management', array_merge($data, [
                'stats' => $stats ?? null,
            ]));

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            if (method_exists($this, 'toast')) {
                $this->toast(text: $message, variant: 'danger');
            }

            return view('livewire.staff.nilai-management.nilai-mahasiswa-management', [], [
                'periodes' => new LengthAwarePaginator([], 0, $this->perPage ?? 8),
            ]);

            return view('livewire.staff.nilai-management', [
                'periodes' => new LengthAwarePaginator([], 0, $this->perPage ?? 8),
                'cpl' => CPL::whereRaw('1=0')->whereHas('mahasiswa')->paginate($this->perPage),
            ], [
                'stats' => [
                    // 'rps' => '-',
                    // 'rps-saya' => '-',
                    // 'rps-prodi' => '-',
                    // 'rps-prodi-non-aktif' => '-',
                    // 'rps-akademik' => '-',
                    // 'rps-rev-new' => '-',
                    // 'rps-aktif' => '-',
                    // 'rps-draf' => '-',
                    // 'rps-older-5' => '-',

                    // 'mahasiswa-prodi' => '-',
                    // 'mahasiswa-opsi' => '-',
                    // 'mahasiswa-aktif' => '-',
                    // 'mahasiswa-non-aktif' => '-',
                ],
            ]);
        }
    }
}
