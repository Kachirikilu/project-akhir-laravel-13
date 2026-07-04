<?php

namespace App\Livewire\AllRole\KelasManagement;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithKelasJadwalSearchFilters;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class JadwalManagement extends Component
{
    use HasGetByKode;
    use HasSortir;
    use HasToast;
    use WithRPSShow;
    use WithNilaiExcel;
    use WithJadwalFilters;
    use WithPagination;
    use WithKelasJadwalSearchFilters;

    public $search = '';

    public $searchMode = 'simple';

    public $isJadwalMhs = false;

    public $kode_kelas_url;

    public Kelas $kelas;

    public $tim_dosen;

    public $rps_id_url;

    public $kode_rps_url;

    public $perPage = 8;

    public $sortField = 'label_kelas';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    public $switchTable = 'jadwal-card';

    protected $listeners = ['refresh-table' => 'refreshKelasList', 'refresh-data-jadwal' => 'refreshJadwalList', 'refresh-data-kelas' => 'refreshJadwalList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'sortField' => ['except' => 'label_kelas'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

    #[On('refresh-data-kelas')]
    #[On('refresh-data-jadwal')]
    #[On('refresh-table')]
    public function refreshJadwalList()
    {
        $this->resetPage();
    }

    public function mount($isJadwalMhs = false, $kode_kelas = null, $switchTable = 'jadwal-card')
    {
        $this->isJadwalMhs = $isJadwalMhs;
        $this->switchTable = $switchTable;

        if (! $this->isJadwalMhs && $kode_kelas !== null) {
            $this->kode_kelas_url = $kode_kelas;

            $kelas = $this->getKelasByKode($kode_kelas);

            if (! $kelas) {
                foreach (['kelas.history', 'kelas_mahasiswa.history'] as $key) {
                    $history = session($key, []);

                    if (isset($history[$kode_kelas])) {
                        unset($history[$kode_kelas]);
                        session([$key => $history]);
                    }
                }

                abort(404, "Kelas dengan Kode $kode_kelas tidak ditemukan!");
            }

            $this->kelas = $kelas;
            $this->rps_id_url = $this->kelas->rps_id;
            $this->kode_rps_url = $this->kelas->kode_rps;
            $this->tim_dosen = $this->getTimDosenByKelas($kelas->rps_id, $kelas->pr_id);

            $sessionKey = $this->isJadwalMhs ? 'kelas_mahasiswa.history' : 'kelas.history';
            $kelasHistory = session($sessionKey, []);
            $currentKode = $kelas->kode;

            $existingKey = array_search($kelas->id, array_column($kelasHistory, 'kelas_id'));
            if ($existingKey !== false) {
                $actualKeys = array_keys($kelasHistory);
                unset($kelasHistory[$actualKeys[$existingKey]]);
            }
            unset($kelasHistory[$currentKode]);
            $kelasHistory[$currentKode] = [
                'kelas_id' => $kelas->id,
                'kode_kelas' => $currentKode,
                'url' => url()->current(),
            ];

            $kelasHistory = array_slice($kelasHistory, -3, null, true);
            uasort($kelasHistory, fn ($a, $b) => strcmp($a['kode_kelas'], $b['kode_kelas']));

            session([$sessionKey => $kelasHistory]);
        }
    }

    public function loadingTable() {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterJadwal']);
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
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->resetPage();

        $base = $this->isJadwalMhs ? 'jadwal-kelas' : 'kelas-management/kelas';
        $suffix = ($table && $table !== 'jadwal-card') ? "/{$table}" : '';

        $targetPath = "/{$base}/{$this->kode_kelas_url}/jadwal{$suffix}";
        $targetPath = preg_replace('#(?<!:)/+#', '/', $targetPath);
        $targetPath = '/'.ltrim(rtrim($targetPath, '/'), '/');

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        try {
            if (! $this->isJadwalMhs) {
                $queryJadwal = $this->inputJadwalSearch($this->kelas->id);
                $countJadwal = KelasJadwal::where('kelas_id', $this->kelas->id);
            } else {
                $queryJadwal = $this->inputJadwalSearch();
                $countJadwal = KelasJadwal::query();
            }

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryJadwal->onlyTrashed();
                $countJadwal->onlyTrashed();
            }

            // $jadwals = $queryJadwal->paginate($this->perPage);
            if ($this->searchMode == 'full') {
                $jadwals = $this->searchOutputJadwal($queryJadwal, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $jadwals = $queryJadwal->paginate($this->perPage);
            }
            if (Auth::user()->mahasiswa) {
                $userId = Auth::id();
                $jadwals->load('mahasiswas:id,user_id');
                $jadwals->getCollection()->transform(function ($jadwal) use ($userId) {
                    if (! $this->isJadwalMhs) {
                        if ($jadwal->password == '' || $jadwal->password == null) {
                            $jadwal->with_pw = false;
                        } else {
                            $jadwal->with_pw = true;
                        }
                        $jadwal->is_my_class = $jadwal->mahasiswas->contains('user_id', $userId);
                    } else {
                        $jadwal->is_my_class = true;
                    }
                    $jadwal->password = '';

                    return $jadwal;
                });
            }

            return view('livewire.all-role.kelas-management.jadwal-management', [
                'jadwals' => $jadwals,
                'kelas' => $this->kelas ?? null,
                'stats' => [
                    'jadwal' => $countJadwal->count(),
                ],
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management.jadwal-management', [
                'jadwals' => KelasJadwal::whereRaw('1 = 0')->paginate($this->perPage),
                'kelas' => $this->kelas ?? null,
                'stats' => [
                    'jadwal' => '',
                ],
            ]);
        }
    }
}
