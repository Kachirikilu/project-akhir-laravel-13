<?php

namespace App\Livewire\Staff\NilaiManagement;

use App\Http\Services\RekapCapaian;
use App\Livewire\Admin\UserManagement\WithUserDelete;
use App\Livewire\Admin\UserManagement\WithUserFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
// use App\Livewire\Staff\NilaiManagement\NilaiMahasiswaManagement\WithNilaiMahasiswaFilters;
// use App\Models\Kelas\NilaiMahasiswa;
// use App\Livewire\Staff\NilaiManagement\WithNilaiMahasiswaExcel;
use App\Livewire\Admin\UserManagement\WithUserModal;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement\WithCpmkGrafikShow;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasNilaiAbsensi;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDepartemenSearchFilters;
use App\Livewire\Global\WithFakultasSearchFilters;
use App\Livewire\Global\WithProdiSearchFilters;
use App\Livewire\Global\WithUserSearchFilters;
// use App\Livewire\Global\WithUserSearchFilters;
// use App\Livewire\Global\WithNilaiSearchFilters;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use App\Models\Penilaian\NilaiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RpsCapaianMahasiswaManagement extends Component
{
    // use WithKelasDelete;
    // use WithNilaiMahasiswaFilters;
    // use WithUserSearchFilters;
    use HasGetByKode;
    use HasNilaiAbsensi;
    use HasToast;
    use RekapCapaian;
    use WithCpmkGrafikShow;
    use WithDepartemenSearchFilters;
    use WithFakultasSearchFilters;
    use WithRPSShow;

    // use WithNilaiSearchFilters;
    use WithPagination;
    use WithProdiSearchFilters;
    // use WithNilaiMahasiswaExcel;

    use WithUserDelete;
    use WithUserFilters;
    use WithUserModal;
    use WithUserSearchFilters;

    public $perPage = 8;

    public $switchTable = 'mahasiswa';

    protected $paginationTheme = 'tailwind';

    public $sortField = 'pertemuan_ke';

    public $sortDirection = 'desc';

    public $showDeleted = false;

    public $search = '';

    public $searchMode = 'simple';

    // public User $user;

    public $rps;

    public $kode_rps_url;

    public $rps_id_url;

    public $selectedPrId;

    public $selectedDpId;

    public $selectedFkId;

    public $refreshTrigger = 0;

    protected $listeners = [
        'refresh-table' => 'refreshCapaianList',
        'refresh-data-rps-mahasiswa' => 'refreshCapaianList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 8],
        'filterCapaian' => ['except' => ''],
        // 'switchTable' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterAngkatan' => ['except' => ''],
        'sortField' => ['except' => 'pertemuan_ke'],
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
    public function refreshCapaianList()
    {
        $this->resetPage();
        $this->refreshTrigger = $this->refreshTrigger === 0 ? 1 : 0;
    }

    public function mount($kode_rps = '')
    {
        $this->kode_rps_url = $kode_rps;

        $rps = $this->getRPSByKode($kode_rps);

        if (! $rps) {
            foreach (['rps.history', 'rps_capaian_mahasiswa.history'] as $key) {
                $history = session($key, []);
                if (isset($history[$kode_rps])) {
                    unset($history[$kode_rps]);
                    session([$key => $history]);
                }
            }
            abort(404, "RPS dengan kode $kode_rps tidak ditemukan!");
        }

        $this->rps = $rps;
        $rpsId = $rps->id;

        $this->rps_id_url = $rpsId;

        $sessionKey = 'rps_capaian_mahasiswa.history';
        $nilaiHistory = session($sessionKey, []);

        $existingKey = array_search($rpsId, array_column($nilaiHistory, 'rps_id'));
        if ($existingKey !== false) {
            $actualKeys = array_keys($nilaiHistory);
            unset($nilaiHistory[$actualKeys[$existingKey]]);
        }

        unset($nilaiHistory[$kode_rps]);
        $nilaiHistory[$kode_rps] = [
            'rps_id' => $rpsId,
            'kode_rps' => $kode_rps,
            'url' => url()->current(),
        ];

        $nilaiHistory = array_slice($nilaiHistory, -5, null, true);
        uasort($nilaiHistory, fn ($a, $b) => strcmp($a['kode_rps'], $b['kode_rps']));

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
        $this->reset(['search']);
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

    public function render()
    {
        try {
            $rpsId = $this->rps_id_url;

            $queryUser = $this->inputUserSearch('mahasiswa', null, null, null, $rpsId);

            if (Auth::user()->mahasiswa) {
                $mahasiswaId = Auth::user()->mahasiswa->id;
                $isInJadwal = KelasJadwal::where('id', $rpsId)
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

            $this->addNilaiRPSSubquery($queryUser, $rpsId, 'mhs_nilai_array', 'nilai_array');
            $this->addNilaiRPSSubquery($queryUser, $rpsId, 'mhs_bobot_array', 'bobot_array');

            $this->addMahasiswaNilaiAkhir($queryUser, $rpsId, 'mhs_nilai_akhir');
            $this->addMahasiswaNilaiIndex($queryUser, $rpsId, 'mhs_nilai_index');
            $this->addMahasiswaNilaiMutu($queryUser, $rpsId, 'mhs_nilai_mutu');

            if ($this->showDeleted && $this->AuthCheck('admin')) {
                $queryUser->onlyTrashed();
                // $countMahasiswa->onlyTrashed();
            }

            /**
             * =========================
             * DEFAULT DATA
             * =========================
             */
            $users = collect();
            $groupsCpmk = collect();
            // $totalBobotPerCpmk = collect();

            $sampleNilai = NilaiMahasiswa::where('rps_id', $this->rps_id_url)->first();

            if (! $sampleNilai) {
                $sampleNilai = new NilaiMahasiswa;
                $sampleNilai->rps_id = $this->rps_id_url;
            }
            $mappingData = $sampleNilai->mapping_pertemuan ?? [];

            if ($this->searchMode == 'complex') {
                $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, $this->perPage, $this->sortField, $this->sortDirection);
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

            return view('livewire.staff.nilai-management.rps-capaian-mahasiswa-management', [
                'users' => $users,
                'groupsCpmk' => $groupsCpmk ?? collect(),
            ]);

        } catch (\Throwable $e) {
            $message = 'Terjadi kesalahan database: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toast(text: $message, variant: 'danger');

            return view('livewire.staff.nilai-management.rps-capaian-mahasiswa-management', [
                'users' => User::whereRaw('1 = 0')->paginate($this->perPage),
                'groupsCpmk' => collect(),
            ]);
        }
    }
}
