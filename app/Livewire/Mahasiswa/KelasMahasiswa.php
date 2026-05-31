<?php

namespace App\Livewire\Mahasiswa;

use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasModal;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\RPSManagement\WithRPSShow;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class KelasMahasiswa extends Component
{
    use HasToast;
    use WithDepartemenSearchFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;

    use WithJadwalFilters;
    // use WithKelasDelete;
    use WithKelasFilters;
    use WithKelasModal;
    use WithMKSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;

    public $showModal = false;

    public $perPage = 8;

    public $switchTable = '';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshKelassList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterKelas' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
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
        $this->reset(['search', 'filterKelas']);
        $this->resetPage();
    }

    public function refreshKelasList()
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
        $map = [
            'tatap_muka' => 'sks_tm',
            'praktikum' => 'sks_pr',
            'praktek_lapangan' => 'sks_pl',
            'simulasi' => 'sks_sm',
        ];

        if (isset($map[$table]) && str_starts_with($sortField, 'sks_')) {
            $this->sortField = $map[$table];
        }
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);

        $this->resetPage();

        $targetUrl = route('kelas-management', ['switchTable' => $table]);
        if ($table == '' || $table == null) {
            $targetPath = '/kelas-management';
        } else {
            $targetPath = '/kelas-management/'.$table;
        }

        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function render()
    {
        // if (Auth::user()->dosen) {
        //     $this->selectedDosenId = Auth::user()->id;
        // }

        $this->inputPrFilter();
        $this->inputDpFilter();
        $this->inputFkFilter();
        $this->inputRPSFilter();
        $this->inputMKFilter();
        $this->inputDosenFilter();

        try {
            // =========================
            // QUERY UTAMA (TABLE)
            // =========================
            $queryKelas = $this->inputKelasSearch();
            $queryJadwal = $this->inputJadwalSearch();

            // =========================
            // QUERY COUNT (TERPISAH 🔥)
            // =========================
            $countKelas = Kelas::query();
            $countJadwal = KelasJadwal::query();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryKelas->onlyTrashed();
                $countKelas->onlyTrashed();

                $queryJadwal->onlyTrashed();
                $countJadwal->onlyTrashed();
            }

            // =========================
            // MAP TAB
            // =========================
            $mapTipe = [
                'tatap-muka' => 1,
                'praktikum' => 2,
                'praktek-lapangan' => 3,
                'simulasi' => 4,
            ];

            $currentTabTipe = $mapTipe[$this->switchTable] ?? null;

            // =========================
            // STATS GLOBAL (FULL DATA)
            // =========================
            $totalKelas = (clone $countKelas)->count();
            $totalTatapMuka = (clone $countKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.tipe_sks', 1);
            })->count();
            $totalPraktikum = (clone $countKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.tipe_sks', 2);
            })->count();
            $totalPraktek = (clone $countKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.tipe_sks', 3);
            })->count();
            $totalSimulasi = (clone $countKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.tipe_sks', 4);
            })->count();

            // =========================
            // STATS PER TAB
            // =========================
            $tabQuery = clone $countKelas;

            if ($currentTabTipe) {
                $tabQuery->whereHas('rps_rel.mk_rel', function ($q) use ($currentTabTipe) {
                    $q->where('mata_kuliahs.tipe_sks', $currentTabTipe);
                });
            }

            if (Auth::user()->mahasiswa) {
                $totalKelasSaya = (clone $tabQuery)->where(function ($mainQuery) {
                    $mainQuery->whereHas('jadwals.mahasiswas', function ($q) {
                        $q->where('mahasiswas.id', Auth::user()->mahasiswa->id);
                    });
                })->count();
            }

            $totalKelasProdi = (clone $tabQuery)->where(function ($mainQuery) {
                $mainQuery->whereHas('pr_rel', function ($q) {
                    $q->where('prodis.id', Auth::user()->pr_id);
                });
            })->count();

            $totalWajib = (clone $tabQuery)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.is_wajib', true);
            })->count();
            $totalPilihan = (clone $tabQuery)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.is_wajib', false);
            })->count();
            $totalUni = (clone $tabQuery)->whereHas('rps_rel.mk_rel', function ($q) {
                $q->where('mata_kuliahs.level_mk', 4);
            })->count();

            // =========================
            // QUERY FINAL TABLE
            // =========================
            if ($currentTabTipe) {
                $queryKelas->whereHas('rps_rel.mk_rel', function ($q) use ($currentTabTipe) {
                    $q->where('mata_kuliahs.tipe_sks', $currentTabTipe);
                });
            }

            $jadwals = $queryJadwal->paginate($this->perPage);
            
            $jadwals->getCollection()->transform(function ($jadwal) {
                $jadwal->password = '';
                $jadwal->is_my_class = 1;

                return $jadwal;
            });

            $this->buttonKelasFilter($queryKelas);

            return view('livewire.mahasiswa.jadwal-mahasiswa', [
                'kelas' => $queryKelas->paginate($this->perPage),
                'jadwals' => $jadwals,

                'totalWajib' => $totalWajib,
                'totalPilihan' => $totalPilihan,
                'totalUni' => $totalUni,

                'totalKelasSaya' => $totalKelasSaya ?? 0,
                'totalKelasProdi' => $totalKelasProdi,
                'totalKelas' => $totalKelas,
                'totalTatapMuka' => $totalTatapMuka,
                'totalPraktikum' => $totalPraktikum,
                'totalPraktek' => $totalPraktek,
                'totalSimulasi' => $totalSimulasi,
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.mahasiswa.jadwal-mahasiswa', [
                'kelas' => Kelas::whereRaw('1 = 0')->paginate($this->perPage),

                'totalWajib' => '-',
                'totalPilihan' => '-',
                'totalUni' => '-',

                'totalKelasSaya' => '-',
                'totalKelasProdi' => '-',
                'totalKelas' => '-',
                'totalTatapMuka' => '-',
                'totalPraktikum' => '-',
                'totalPraktek' => '-',
                'totalSimulasi' => '-',
            ]);
        }
    }
}
