<?php

namespace App\Livewire\AllRole;

use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
use App\Livewire\AllRole\KelasManagement\WithKelasModal;
use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithKelasSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Global\WithMKSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithRPSSearchFilters;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
use App\Models\Kelas\Kelas;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class KelasManagement extends Component
{
    use HasSortir;
    use HasToast;
    use WithDepartemenSearchFilters;
    use WithDosenSearchFilters;
    use WithFakultasSearchFilters;

    // use WithKelasDelete;
    use WithKelasFilters;
    use WithKelasModal;
    use WithKelasDelete;
    use WithKelasSearchFilters;
    use WithMKSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    use WithRPSSearchFilters;
    use WithRPSShow;



    public $perPage = 8;

    public $switchTable = '';

    public $switchTable2 = 'kelas-card';

    public $search = '';

    public $searchMode = 'simple';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'kode';

    public $sortDirection = 'asc';

    public $showDeleted = false;

    protected $listeners = ['refresh-table' => 'refreshKelasList',
        'loadDraft' => 'loadDraft', 'saveToDraft' => 'saveToDraft'];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'filterKelas' => ['except' => ''],
        'filterKelasgg' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'sortField' => ['except' => 'kode'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' =>  ['except' => false],
    ];

    public function mount($switchTable = '', $switchTable2 = 'kelas-card')
    {
        $this->switchTable = $switchTable;
        $this->switchTable2 = $switchTable2;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterKelas', 'filterKelasgg']);
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
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
        $columns = [
            'kelas-card' => [1 => 'kode', 2 => 'kode_rps', 3 => 'kelas', 4 => 'mk', 5 => 'semester'],
            'kelas-table' => [1 => 'id', 2 => 'kode', 3 => 'kode_rps', 4 => 'kelas', 5 => 'program_studi', 6 => 'hari_pelaksanaan', 7 => 'jam_pelaksanaan', 8 => 'kapasitas', 9 => 'tanggal_pelaksanaan', 10 => 'kode_mk', 11 => 'mk', 12 => 'semester', 13 => 'sks', 14 => 'pembelajaran', 15 => 'wajib', 16 => 'created_at', 17 => 'updated_at'],
        ];
        $aliases = [
            'kode' => ['kode'],
            'kode_rps' => ['kode_rps', 'kode_rps'],
            'kelas' => ['kelas', 'hari_pelaksanaan', 'jam_pelaksanaan', 'kapasitas', 'tanggal_pelaksanaan'],
            'mk' => ['mk', 'semester', 'sks', 'pembelajaran', 'wajib'],
            'semester' => ['semester'],
            'created_at' => ['created_at'],
            'updated_at' => ['updated_at'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable2($table)
    {
        $this->switchTable2 = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        if ($table == 'kelas-card') {
            $table = '';
        }

        $targetPath = $this->buildTargetPath($this->switchTable, $table);

        $this->dispatch('table-switched', switchTable2: $this->switchTable2, switchTable: $this->switchTable, targetUrl: $targetPath);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;

        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $targetPath = $this->buildTargetPath($table, $this->switchTable2);

        $this->dispatch('table-switched', switchTable2: $this->switchTable2, switchTable: $this->switchTable, targetUrl: $targetPath);
    }


    private function buildTargetPath($table, $table2)
    {
        $path = '/kelas-management';

        if ($table2) {
            $path .= '/'.$table2;

            if ($table) {
                $path .= '/'.$table;
            }
        } elseif ($table) {
            $path .= '/all/'.$table;
        }

        return $path;
    }

    public function render()
    {
        try {
            $this->inputPrFilter();
            $this->inputDpFilter();
            $this->inputFkFilter();
            $this->inputRPSFilter();
            $this->inputMKFilter();
            $this->inputDosenFilter();
            // =========================
            // QUERY UTAMA (TABLE)
            // =========================
            $queryKelas = $this->inputKelasSearch();

            // =========================
            // QUERY COUNT (TERPISAH 🔥)
            // =========================
            $countKelas = Kelas::query();

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $queryKelas->onlyTrashed();
                $countKelas->onlyTrashed();
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

            if (Auth::user()->dosen) {
                $totalKelasSaya = (clone $tabQuery)->where(function ($mk) {
                    $mk->whereHas('rps_rel.dosens', function ($q) {
                        $q->where('dosens.id', Auth::user()->dosen->id);
                    });
                        // ->orWhereHas('jadwals.sesis.dosens', function ($q) {
                        //     $q->where('dosens.id', Auth::user()->dosen->id);
                        // });
                })->count();
            } elseif (Auth::user()->mahasiswa) {
                $totalKelasSaya = (clone $tabQuery)->where(function ($mk) {
                    $mk->whereHas('jadwals.mahasiswas', function ($q) {
                        $q->where('mahasiswas.id', Auth::user()->mahasiswa->id);
                    });
                })->count();
            }

            $totalKelasProdi = (clone $tabQuery)->where(function ($mk) {
                $mk->whereHas('pr_rel', function ($q) {
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

            // $totalGanjilKelas = (clone $tabQuery)->whereHas('rps_rel.mk_rel', function ($q) {
            //     $q->whereRaw('mata_kuliahs.semester % 2 = 1');
            // })->count();
            // $totalGenapKelas = (clone $tabQuery)->whereHas('rps_rel.mk_rel', function ($q) {
            //     $q->whereRaw('mata_kuliahs.semester % 2 = 0');
            // })->count();

            // =========================
            // QUERY FINAL TABLE
            // =========================
            if ($currentTabTipe) {
                $queryKelas->whereHas('rps_rel.mk_rel', function ($q) use ($currentTabTipe) {
                    $q->where('mata_kuliahs.tipe_sks', $currentTabTipe);
                });
            }

            $this->buttonKelasFilter($queryKelas);

            if ($this->searchMode == 'full') {
                $kelas = $this->searchOutputKelas($queryKelas, $this->search, $this->perPage, $this->sortField, $this->sortDirection);
            } else {
                $kelas = $queryKelas->paginate($this->perPage);
            }

            return view('livewire.all-role.kelas-management', [
                'kelas' => $kelas,

                'stats' => [
                    'kelas-saya' => $totalKelasSaya ?? 0,
                    'kelas-prodi' => $totalKelasProdi,

                    'kelas' => $totalKelas,
                    'kelas-tp' => $totalTatapMuka,
                    'kelas-pr' => $totalPraktikum,
                    'kelas-pl' => $totalPraktek,
                    'kelas-sm' => $totalSimulasi,

                    'kelas-wajib' => $totalWajib,
                    'kelas-pilihan' => $totalPilihan,
                    'kelas-uni' => $totalUni,
                ],
            ]);

        } catch (QueryException $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management', [
                'kelas' => Kelas::whereRaw('1 = 0')->paginate($this->perPage),

                'totalGanjilGanjil' => '-',
                'stats' => [
                    'kelas-saya' => '-',
                    'kelas-prodi' => '-',

                    'kelas' => '-',
                    'kelas-tp' => '-',
                    'kelas-pr' => '-',
                    'kelas-pl' => '-',
                    'kelas-sm' => '-',

                    'kelas-wajib' => '-',
                    'kelas-pilihan' => '-',
                    'kelas-uni' => '-',
                ],
            ]);
        }
    }
}
