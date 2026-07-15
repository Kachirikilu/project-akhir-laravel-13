<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Livewire\Admin\UserManagement\WithUserFilters;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithCpmkGrafikShow;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithNilaiExcel;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithSesiFilters;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasNilaiAbsensi;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithKelasSesiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Models\Auth\User;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\Kelas\KelasSesi;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SesiManagement extends Component
{
    use HasGetByKode;
    use HasNilaiAbsensi;
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithCpmkGrafikShow;
    use WithJadwalModal;
    use WithKelasSesiSearchFilters;
    use WithNilaiExcel;
    use WithPagination;
    use WithRPSShow;
    use WithSesiFilters;
    use WithUserFilters;
    use WithUserSearchFilters;

    public $search = '';

    public $searchMode = 'simple';

    public $isJadwalOnly = false;

    public $kode_kelas_url;

    public $kode_jadwal_short_url;

    public Kelas $kelas;

    public KelasJadwal $jadwal;

    public $tim_dosen;

    public $jadwal_id_url;

    public $kode_jadwal_url;

    public $rps_id_url;

    public $kode_rps_url;

    public $perPage = 8;

    public $sortField = 'pertemuan_ke';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public $showDeleted = false;

    public $switchTable = 'card';

    public $mapping_pertemuan;

    public $refreshTrigger = 0;

    protected $listeners = [
        'refresh-table' => 'refreshSesiList',
        'refresh-table' => 'refreshCapaiansList',
        'refresh-data-sesi' => 'refreshSesiList',
        'refresh-data-sesi' => 'refreshSesiList',
        'refresh-data-jadwal' => 'refreshSesiList',
        'refresh-data-jadwal' => 'refreshCapaiansList',
        'refresh-data-rps-mahasiswa' => 'refreshCapaiansList',
        'refresh-stats-kelas' => 'refreshKelasList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'searchMode' => ['except' => 'simple'],
        'perPage' => ['except' => 8],
        'sortField' => ['except' => 'pertemuan_ke'],
        // 'switchTable' => ['except' => 'card'],
        'sortDirection' => ['except' => 'asc'],
        'showDeleted' => ['except' => false],
    ];

    #[On('refresh-data-sesi')]
    #[On('refresh-data-jadwal')]
    #[On('refresh-table')]
    public function refreshSesiList()
    {
        $this->resetPage();
    }

    #[On('refresh-data-rps-mahasiswa')]
    #[On('refresh-data-sesi')]
    #[On('refresh-data-jadwal')]
    #[On('refresh-table')]
    public function refreshCapaiansList()
    {
        $this->resetPage();
        $this->refreshTrigger = $this->refreshTrigger === 0 ? 1 : 0;
    }

    public function updatedShowDeletedd()
    {
        $this->refreshTrigger = $this->refreshTrigger === 0 ? 1 : 0;
    }

    #[On('refresh-stats-kelas')]
    public function refreshStatsKelasList()
    {
        $this->clearKelasStatsCache();
    }

    public function refreshStats()
    {
        $this->refreshStatsKelasList();
        $this->resetPage();
        $this->toast(text: 'Data Statistik Kelas berhasil diperbarui!', type: 'info', variant: 'info');
    }

    public function mount(
        $isJadwalOnly = false,
        $kode_kelas = null,
        $kode_jadwal_short = null,
        $switchTable = 'hari-ini'
    ) {
        $this->updatedShowDeleted();

        if (empty($switchTable) || $switchTable == null || $switchTable == 'null') {
            $switchTable = '';
        }
        $this->kode_kelas_url = $kode_kelas;
        $this->isJadwalOnly = $isJadwalOnly;

        $kelas = $this->getKelasByKode($kode_kelas);

        if (! $kelas) {
            foreach (['kelas.history', 'kelas_mahasiswa.history'] as $key) {
                $history = session($key, []);

                if (isset($history[$kode_kelas])) {
                    unset($history[$history]);
                    session([$key => $history]);
                }
            }

            abort(404, "Kelas dengan Kode $kode_kelas tidak ditemukan!");
        }

        $this->kode_jadwal_short_url = $kode_jadwal_short;
        $this->kelas = $kelas;

        $jadwal = $this->getJadwalByKode($kode_kelas.'-'.$kode_jadwal_short);

        if (! $jadwal || $jadwal->kelas_id !== $this->kelas->id) {
            $compositeKey = "{$kode_kelas}-{$kode_jadwal_short}";
            foreach (['jadwal.history', 'jadwal_mahasiswa.history'] as $key) {
                $history = session($key, []);

                if (isset($history[$compositeKey])) {
                    unset($history[$compositeKey]);
                    session([$key => $history]);
                }
            }
            abort(404, "Jadwal Kelas dengan Kode {$kode_kelas}-{$kode_jadwal_short} tidak ditemukan!");
        }

        $this->jadwal = $jadwal;
        $this->jadwal_id_url = $this->jadwal->id;
        $this->kode_jadwal_url = $this->jadwal->kode;
        $this->rps_id_url = $this->jadwal->rps_id;
        $this->kode_rps_url = $this->jadwal->kode_rps;
        $this->switchTable = $switchTable;
        $this->tim_dosen = $this->getTimDosenByKelas($kelas->rps_id, $kelas->pr_id);

        // =====================================
        // MANAJEMEN RIWAYAT/HISTORY SESSION
        // =====================================
        $sessionKey = $this->isJadwalOnly ? 'jadwal_mahasiswa.history' : 'jadwal.history';
        $sesiHistory = session($sessionKey, []);

        $currentKode = $kelas->kode;
        $compositeKey = $jadwal->kode;

        $existingKey = array_search($jadwal->id, array_column($sesiHistory, 'jadwal_id'));
        if ($existingKey !== false) {
            $actualKeys = array_keys($sesiHistory);
            unset($sesiHistory[$actualKeys[$existingKey]]);
        }

        unset($sesiHistory[$compositeKey]);

        $sesiHistory[$compositeKey] = [
            'kelas_id' => $kelas->id,
            'jadwal_id' => $jadwal->id,
            'kode_kelas' => $currentKode,
            'kode_jadwal_short' => $jadwal->kode_jadwal,
            'switchTable' => $switchTable,
            'url' => url()->current(),
        ];

        $sesiHistory = array_slice($sesiHistory, -12, null, true);

        uasort($sesiHistory, function ($a, $b) {
            $kodeCompare = strcmp($a['kode_kelas'], $b['kode_kelas']);

            return ($kodeCompare !== 0) ? $kodeCompare : strcmp($a['kode_jadwal_short'], $b['kode_jadwal_short']);
        });

        session([$sessionKey => $sesiHistory]);
    }

    public function loadingTable() {}

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterSesi']);
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

    private function syncSortField($table, $sortField)
    {
        if (Auth::user()->admin || Auth::user()->dosen) {
            $mahasiswa = [1 => 'mahasiswa_id', 2 => 'pertemuan_ke', 3 => 'name', 4 => 'mhs_poin_absensi', 5 => 'mhs_masuk', 6 => 'mhs_dispensasi', 7 => 'mhs_terlambat', 8 => 'mhs_izin', 9 => 'mhs_sakit', 10 => 'mhs_tidak_masuk', 11 => 'mhs_nilai_akhir', 12 => 'mhs_nilai_index', 13 => 'mhs_nilai_mutu', 14 => 'angkatan', 15 => 'status', 16 => 'program_studi'];
        } else {
            $mahasiswa = [1 => 'mahasiswa_id', 2 => 'pertemuan_ke', 3 => 'name', 4 => 'angkatan', 5 => 'status', 6 => 'program_studi'];
        }
        $columns = [
            'card' => [1 => 'pertemuan_ke', 2 => 'total_absensi', 3 => 'tanggal_pelaksanaan', 4 => 'metode', 5 => 'kode_scpmk', 6 => 'bobot'],
            'table' => [1 => 'id', 2 => 'metode', 3 => 'pertemuan_ke', 4 => 'hari_pelaksanaan', 5 => 'jam_pelaksanaan', 6 => 'total_absensi', 7 => 'tanggal_pelaksanaan', 8 => 'kode_scpmk', 9 => 'bobot', 10 => 'tugas', 11 => 'w_tugas', 12 => 'w_mandiri'],
            'mahasiswa' => $mahasiswa,
            'cpmk' => [1 => 'mahasiswa_id', 2 => 'pertemuan_ke', 3 => 'name', 4 => 'mhs_nilai_akhir', 5 => 'mhs_nilai_index', 6 => 'mhs_nilai_mutu', 7 => 'angkatan', 8 => 'status', 9 => 'program_studi'],
        ];
        $aliases = [
            'id' => ['id', 'mahasiswa_id'],
            'mahasiswa_id' => ['mahasiswa_id', 'id'],
            'pertemuan_ke' => ['pertemuan_ke', 'name'],
            'mhs_nilai_akhir' => ['mhs_nilai_akhir'],
            'mhs_nilai_index' => ['mhs_nilai_index'],
            'mhs_nilai_huruf' => ['mhs_nilai_huruf'],
            'angkatan' => ['angkatan'],
            'status' => ['status'],
            'program_studi' => ['program_studi'],
        ];

        $this->sortField($table, $sortField, $columns, $aliases);
    }

    public function switchingTable($table)
    {
        $this->switchTable = $table;
        $this->syncSortField($table, $this->sortField);
        $this->resetPage();

        $allFilters = ['filterSesi', 'filterMahasiswa'];

        foreach ($allFilters as $filter) {
            if ($filter !== 'filter'.strtoupper($this->switchTable)) {
                $this->$filter = '';
            }
        }

        $currentTable = $table ?? $this->table ?? 'table';

        if ($this->switchTable == 'mahasiswa' || $this->switchTable == 'cpmk') {
            if ($this->perPage == 2) {
                $this->perPage = 3;
            } elseif ($this->perPage == 4) {
                $this->perPage = 5;
            } elseif ($this->perPage == 16) {
                $this->perPage = 15;
            }
        } elseif ($this->switchTable == 'card' || $this->switchTable == 'table') {
            if ($this->perPage == 3) {
                $this->perPage = 2;
            } elseif ($this->perPage == 5) {
                $this->perPage = 4;
            } elseif ($this->perPage == 10) {
                $this->perPage = 8;
            } elseif ($this->perPage >= 15) {
                $this->perPage = 16;
            }
        }

        if (Auth::user()->dosen && ($this->switchTable == 'mahasiswa' || $this->switchTable == 'cpmk')) {
            $this->showDeleted = false;
        }

        $base = $this->isJadwalOnly ? 'jadwal-kelas' : 'kelas-management/kelas';
        $suffix = ($table && $table !== 'hari-ini') ? "/{$table}" : '';

        $targetPath = "/{$base}/{$this->kode_kelas_url}/jadwal/{$this->kode_jadwal_short_url}/sesi{$suffix}";
        $this->dispatch('table-switched', switchTable: $table, targetUrl: $targetPath);
    }

    public function updatedSwitchTable()
    {
        $this->updatedShowDeleted();
    }

    public function updatedShowDeleted()
    {
        if ($this->switchTable == '' || $this->switchTable == 'hari-ini' || $this->switchTable == 'card' || $this->switchTable == 'table' || (Auth::user()->dosen && ($this->switchTable == 'mahasiswa' || $this->switchTable == 'cpmk')) || Auth::user()->mahasiswa) {
            $this->showDeleted = false;
        }
    }

    public function render()
    {
        try {
            $jadwalId = $this->jadwal_id_url;

            $querySesi = collect();
            $queryUser = collect();

            $querySesi = $this->inputSesiSearch($jadwalId);

            switch ($this->switchTable) {
                // case '':
                // case 'hari-ini':
                // case 'card':
                // case 'table':
                //     break;
                case 'mahasiswa':
                case 'cpmk':
                    $queryUser = $this->inputUserSearch('mahasiswa', $jadwalId, null, 1);
                    break;
            }

            if (Auth::user()->mahasiswa) {
                $mahasiswaId = Auth::user()->mahasiswa->id;
                $isInJadwal = KelasJadwal::where('id', $jadwalId)
                    ->whereHas('mahasiswas', function ($q) use ($mahasiswaId) {
                        $q->where('mahasiswas.id', $mahasiswaId);
                    })
                    ->exists();

                if (! $isInJadwal) {
                    $message = 'Anda tidak terdaftar di Kelas ini!';
                    $this->toast(text: $message, variant: 'danger');

                    $history = session('jadwal.history', []);
                    $compositeKey = $this->kode_kelas.'_'.$this->kode_jadwal_short_url;
                    unset($history[$compositeKey]);
                    session(['jadwal.history' => $history]);
                    $this->redirect(route('jadwal-management', $this->kode_kelas));
                }
            }

            /**
             * =========================
             * AMBIL SESI + CEK EXPIRED
             * =========================
             */
            if (Auth::user()->mahasiswa) {
                $mahasiswaId = Auth::user()->mahasiswa->id ?? null;

                $sesiList = KelasSesi::with([
                    'jadwal_rel',
                    'override',
                    'kehadirans' => function ($query) use ($mahasiswaId) {
                        $query->where('mahasiswa_id', $mahasiswaId);
                    },
                ])
                    ->where('kj_id', $jadwalId)
                    ->get();
            } else {
                $sesiList = KelasSesi::with(['jadwal_rel', 'override'])
                    ->where('kj_id', $jadwalId)
                    ->get();

            }

            $now = now();

            $expiredSesiIds = $sesiList->filter(function ($sesi) {
                $jamAkhir = $sesi->jam_berakhir;
                if (! $jamAkhir) {
                    return false;
                }

                return Carbon::parse($sesi->tanggal.' '.$jamAkhir)
                    ->lt(now());
            })->pluck('id')->all();

            $expiredCount = (int) count($expiredSesiIds ?: []);

            if (Auth::user()->admin || Auth::user()->dosen) {

                switch ($this->switchTable) {
                    case '':
                    case 'hari-ini':
                    case 'card':
                    case 'table':
                        $this->addAbsenSesi($querySesi, $jadwalId, 'mhs_absensi');
                        break;
                    case 'mahasiswa':
                        $this->addMahasiswaAttendanceStats($queryUser, $jadwalId);
                        $this->addMahasiswaTidakMasuk($queryUser, $jadwalId, $expiredCount, 'mhs_tidak_masuk');
                        break;
                    case 'cpmk':
                        // $this->addNilaiRPSSubquery($queryUser, $this->rps_id_url, 'mhs_nilai_array', 'nilai_array');
                        // $this->addNilaiRPSSubquery($queryUser, $this->rps_id_url, 'mhs_bobot_array', 'bobot_array');
                        $this->addNilaiJadwalSubquery($queryUser, $jadwalId, 'mhs_nilai_array', 'nilai_array');
                        $this->addNilaiJadwalSubquery($queryUser, $jadwalId, 'mhs_bobot_array', 'bobot_array');
                        break;
                }
            }

            if ($this->switchTable == 'mahasiswa' || $this->switchTable == 'cpmk') {
                $this->addMahasiswaNilaiAkhir($queryUser, $jadwalId, 'mhs_nilai_akhir');
                $this->addMahasiswaNilaiIndex($queryUser, $jadwalId, 'mhs_nilai_index');
                $this->addMahasiswaNilaiMutu($queryUser, $jadwalId, 'mhs_nilai_mutu');
            }

            /**
             * =========================
             * COUNTING
             * =========================
             */
            // $countSesi = KelasSesi::where('kj_id', $jadwalId);
            $countSesi = 16;

            $countMahasiswa = User::whereHas('mahasiswa.jadwals', function ($q) use ($jadwalId) {
                $q->where('kj_id', $jadwalId);
            });

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                switch ($this->switchTable) {
                    // case '':
                    // case 'hari-ini':
                    // case 'card':
                    // case 'table':
                    //     $querySesi->onlyTrashed();
                    //     // $countSesi->onlyTrashed();
                    //     $countSesi = 0;
                    //     break;
                    case 'mahasiswa':
                    case 'cpmk':
                        $queryUser->onlyTrashed();
                        $countMahasiswa->onlyTrashed();
                        break;
                }
            }

            /**
             * =========================
             * DEFAULT DATA
             * =========================
             */
            $sesis = collect();
            $users = collect();
            $groupsCpmk = collect();
            // $totalBobotPerCpmk = collect();

            $absensi = [
                'mhs_poin_absensi' => 0,
                'mhs_poin_absensi_percent' => 0,
                'mhs_absensi' => 0,
                'mhs_masuk' => 0,
                'mhs_hadir' => 0,
                'mhs_terlambat' => 0,
                'mhs_izin' => 0,
                'mhs_sakit' => 0,
                'mhs_dispensasi' => 0,
                'mhs_absen' => 0,
                'mhs_tidak_masuk' => 0,
            ];

            /**
             * =========================
             * OUTPUT SWITCH
             * =========================
             */
            switch ($this->switchTable) {
                case 'hari-ini':
                    $sesis = (clone $querySesi)->whereDate('tanggal', today())->get();
                    if ($sesis->count() === 0) {
                        $sesis = $querySesi->get();
                        $haveSesiDay = false;
                    } else {
                        $haveSesiDay = true;
                    }
                    break;
                case 'card':
                    $sesis = $querySesi->get();
                    break;
                case 'table':
                    if ($this->searchMode == 'complex') {
                        $sesis = $this->searchOutputSesi($querySesi, $this->search, $this->perPage, $this->sortField, $this->sortDirection, $jadwalId);
                    } else {
                        $sesis = $querySesi->paginate($this->perPage);
                    }
                    break;
                case 'mahasiswa':
                    if ($this->searchMode == 'complex') {
                        $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, $jadwalId);
                    } else {
                        $users = $queryUser->paginate($this->perPage);
                    }
                    break;
                case 'cpmk':
                    $sampleNilai = NilaiMahasiswa::where('rps_id', $this->rps_id_url)->first();

                    if (! $sampleNilai) {
                        $sampleNilai = new NilaiMahasiswa;
                        $sampleNilai->rps_id = $this->rps_id_url;
                    }
                    $mappingData = $sampleNilai->mapping_pertemuan ?? [];

                    if ($this->searchMode == 'complex') {
                        $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection, $jadwalId);
                    } else {
                        $users = $queryUser->paginate($this->perPage);
                    }
                    if (! empty($mappingData)) {
                        $collectionMapping = collect($mappingData);

                        $totalGlobalBobotMentah = $collectionMapping->sum('bobot');
                        $totalGlobalBobotMentah = $totalGlobalBobotMentah > 0 ? $totalGlobalBobotMentah : 1;

                        $normalizedMapping = $collectionMapping->map(function ($item) use ($totalGlobalBobotMentah) {
                            $bobotMentah = $item['bobot'] ?? 0;
                            $item['bobot'] = ($bobotMentah / $totalGlobalBobotMentah) * 100;

                            return $item;
                        })->toArray();

                        $this->mapping_pertemuan = $normalizedMapping;
                        $groupsCpmk = collect($normalizedMapping)->groupBy('kode_cpmk');
                    } else {
                        $groupsCpmk = collect();
                    }

                    break;
            }

            /**
             * =========================
             * SUMMARY
             * =========================
             */
            $totalSesiKelas = 16;
            //  $countSesi->count() ?: 0;

            $summaryQuery = User::query()
                ->whereHas('mahasiswa.jadwals', function ($q) use ($jadwalId) {
                    $q->where('kj_id', $jadwalId);
                })
                ->select('users.*');

            if ($this->showDeleted && $this->AuthCheck('staff')) {
                $summaryQuery->onlyTrashed();
            }

            $statuses = [
                'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Izin','Sakit','Dispensasi')",
                'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')",
                'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",
                'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",
                'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",
                'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",
                'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",
                'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",
                'mhs_poin_absensi' => "CASE 
                        WHEN mahasiswa_kehadiran.status IN ('Hadir','Dispensasi') THEN 2
                        WHEN mahasiswa_kehadiran.status IN ('Terlambat','Izin','Sakit') THEN 1
                        ELSE 0
                    END",
            ];

            if (Auth::user()->admin || Auth::user()->dosen || Auth::user()->mahasiswa) {
                foreach ($statuses as $alias => $condition) {

                    $summaryQuery->selectSub(function ($query) use ($jadwalId, $alias, $condition) {

                        if ($alias === 'mhs_poin_absensi') {
                            $rawSql = "COALESCE(SUM($condition),0)";
                        } else {
                            $rawSql = "COALESCE(SUM(CASE WHEN $condition THEN 1 ELSE 0 END),0)";
                        }

                        $query->selectRaw($rawSql)
                            ->from('mahasiswa_kehadiran')
                            ->join('kelas_sesi', 'mahasiswa_kehadiran.sesi_id', '=', 'kelas_sesi.id')
                            ->join('mahasiswas', 'mahasiswa_kehadiran.mahasiswa_id', '=', 'mahasiswas.id')
                            ->whereColumn('mahasiswas.user_id', 'users.id')
                            ->where('kelas_sesi.kj_id', $jadwalId);

                    }, $alias);
                }
            }

            /**
             * =========================
             * FINAL CALCULATION
             * =========================
             */
            if (Auth::user()->admin || Auth::user()->dosen) {

                $summary = $summaryQuery->get();

                $expiredCountX = count($expiredSesiIds);

                foreach ($summary as $row) {
                    $hadir = $row->mhs_hadir + $row->mhs_terlambat + $row->mhs_dispensasi;
                    $row->mhs_tidak_masuk = max(0, $expiredCountX - $hadir);
                }

                $absensi['mhs_poin_absensi'] = $summary->sum('mhs_poin_absensi');

                $maxPoint = $countMahasiswa->count() * $totalSesiKelas * 2;

                $absensi['mhs_poin_absensi_percent'] = $maxPoint > 0
                    ? round(($absensi['mhs_poin_absensi'] / $maxPoint) * 100, 2)
                    : 0;

                $absensi['mhs_masuk'] = $summary->sum('mhs_masuk');
                $absensi['mhs_hadir'] = $summary->sum('mhs_hadir');
                $absensi['mhs_terlambat'] = $summary->sum('mhs_terlambat');
                $absensi['mhs_izin'] = $summary->sum('mhs_izin');
                $absensi['mhs_sakit'] = $summary->sum('mhs_sakit');
                $absensi['mhs_dispensasi'] = $summary->sum('mhs_dispensasi');
                $absensi['mhs_absen'] = $summary->sum('mhs_absen');
                $absensi['mhs_tidak_masuk'] = $summary->sum('mhs_tidak_masuk');

            } elseif (Auth::user()->mahasiswa) {

                $myUser = $summaryQuery->where('users.id', Auth::id())->first();

                $expiredCountX = count($expiredSesiIds);
                $hadir = (
                    ($myUser?->mhs_hadir ?? 0) +
                    ($myUser?->mhs_terlambat ?? 0) +
                    ($myUser?->mhs_izin ?? 0) +
                    ($myUser?->mhs_sakit ?? 0) +
                    ($myUser?->mhs_dispensasi ?? 0)
                );

                $tidakMasuk = max(0, $expiredCountX - $hadir);

                $absensi['mahasiswa'] = [
                    'mhs_poin_absensi' => $myUser?->mhs_poin_absensi ?? 0,

                    'mhs_poin_absensi_percent' => ($totalSesiKelas * 2) > 0
                        ? round(
                            (($myUser?->mhs_poin_absensi ?? 0) / ($totalSesiKelas * 2)) * 100,
                            2
                        )
                        : 0,

                    'mhs_absensi' => $myUser?->mhs_absensi ?? 0,
                    'mhs_masuk' => $myUser?->mhs_masuk ?? 0,
                    'mhs_hadir' => $myUser?->mhs_hadir ?? 0,
                    'mhs_terlambat' => $myUser?->mhs_terlambat ?? 0,
                    'mhs_izin' => $myUser?->mhs_izin ?? 0,
                    'mhs_sakit' => $myUser?->mhs_sakit ?? 0,
                    'mhs_dispensasi' => $myUser?->mhs_dispensasi ?? 0,
                    'mhs_absen' => $myUser?->mhs_absen ?? 0,
                    'mhs_tidak_masuk' => $tidakMasuk ?? 0,
                ];
            }

            return view('livewire.all-role.kelas-management.jadwal-management.sesi-management', [
                'sesis' => $sesis,
                'users' => $users,
                'groupsCpmk' => $groupsCpmk ?? collect(),
                // 'mapping_pertemuan' => $mapping_pertemuan ?? null,
                'absensi' => $absensi,
                'kelas' => $this->kelas,

                'haveSesiDay' => $haveSesiDay ?? false,

                'stats' => [
                    'sesi-hari-ini' => (clone $querySesi)->whereDate('tanggal', today())->count(),
                    'sesi' => $totalSesiKelas,
                    'mahasiswa' => $countMahasiswa->count(),
                ],
            ]);

        } catch (\Throwable $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.all-role.kelas-management.jadwal-management.sesi-management', [
                'sesis' => KelasSesi::whereRaw('1 = 0')->paginate($this->perPage),
                'users' => User::whereRaw('1 = 0')->paginate($this->perPage),
                'groupsCpmk' => collect(),
                // 'mapping_pertemuan' => null,
                'absensi' => [
                    'mhs_poin_absensi' => '-',
                    'mhs_poin_absensi_percent' => '-',
                    'mhs_absensi' => '-',
                    'mhs_masuk' => '-',
                    'mhs_hadir' => '-',
                    'mhs_terlambat' => '-',
                    'mhs_izin' => '-',
                    'mhs_sakit' => '-',
                    'mhs_dispensasi' => '-',
                    'mhs_absen' => '-',
                    'mhs_tidak_masuk' => '-',
                ],
                'kelas' => $this->kelas ?? collect(),

                'haveSesiDay' => false,

                'stats' => [
                    'sesi-hari-ini' => '',
                    'sesi' => '-',
                    'mahasiswa' => '-',
                ],
            ]);
        }
    }
}
