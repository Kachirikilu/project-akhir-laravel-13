<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement;

use App\Models\Kelas\KelasJadwal;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithJadwalFilters
{
    use WithPagination;

    public $search = '';

    public $filterJadwal = '';

    public $searchBobotJadwal = '';

    public function inputJadwalSearch($idKelas = null, $isHariIni = false)
    {
        $user = Auth::user();

        $filterRole = function ($queryJadwal) use ($user) {
            if ($user->dosen) {
                $queryJadwal->whereHas('kelas_rel.rps_rel.tim_dosens.dosens', function ($q) use ($user) {
                    $q->where('dosens.id', $user->dosen->id);
                });
            } elseif ($user->mahasiswa) {
                $queryJadwal->whereHas('mahasiswas', function ($q) use ($user) {
                    $q->where('mahasiswas.id', $user->mahasiswa->id);
                });
            }
        };

        

        if (! empty($idKelas)) {
            $queryJadwal = KelasJadwal::where('kelas_id', $idKelas)
                ->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
        } else {
            if (Auth::user()->dosen) {
                $queryJadwal = KelasJadwal::whereHas('kelas_rel.rps_rel.tim_dosens.dosens', function ($q) {
                    $q->where('dosens.id', Auth::user()->dosen->id);
                })->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
            } elseif (Auth::user()->mahasiswa) {
                $queryJadwal = KelasJadwal::whereHas('mahasiswas', function ($q) {
                    $q->where('mahasiswas.id', Auth::user()->mahasiswa->id);
                })->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
            } else {
                $queryJadwal = KelasJadwal::query()->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
            }
        }

        if ($isHariIni) {
            $queryJadwal->whereHas('sesis', function ($query) {
                $query->whereDate('tanggal', today());
            });
        }
        // dump($queryJadwal->count(), $idKelas);

        // if ($user->dosen) {
        //     $queryJadwal->whereHas('kelas_rel.rps_rel.tim_dosens.dosens', function ($q) use ($user) {
        //         $q->where('dosens.id', $user->dosen->id);
        //     });
        // } elseif ($user->mahasiswa) {
        //     $queryJadwal->whereHas('mahasiswas', function ($q) use ($user) {
        //         $q->where('mahasiswas.id', $user->mahasiswa->id);
        //     });
        // }

        if ($this->hasProperty('searchMode') && ($this->searchMode == 'simple' || $this->searchMode == 'smart')) {
            $search = $this->search;
            if (! empty($search)) {
                if ($this->searchMode == 'smart') {
                    $queryJadwal->searchKelasJadwalSmart($search);
                } else {
                    $queryJadwal->searchKelasJadwal($search);
                }
            }
            $this->sortFieldOrderJadwal($queryJadwal);
        }

        return $queryJadwal;
    }

    public function filterByJadwal($kelas)
    {
        $this->filterJadwal = $kelas;
        $this->resetPage();
    }

    public function sortFieldOrderJadwal($queryJadwal)
    {
        $queryJadwal->select('kelas_jadwals.*')->withCount('mahasiswas');

        if (Auth::user()->admin || Auth::user()->dosen) {
            $pwSearch = 'password';
        } else {
            $pwSearch = 'id';
        }

        return match ($this->sortField) {
            // 'kode' => $this->applyJadwalKodeSort($queryJadwal),
            'kode' => $queryJadwal->orderByRaw('CONCAT(kelas_jadwals.label_kelas, kelas_jadwals.kode_wilayah, kelas_jadwals.tanggal_mulai) '.$this->sortDirection),
            'kelas' => $queryJadwal->orderBy('kelas_jadwals.nama_kelas', $this->sortDirection),
            'label_kelas' => $queryJadwal->orderByRaw('CONCAT(kelas_jadwals.label_kelas, kelas_jadwals.kode_wilayah) '.$this->sortDirection),
            'hari_pelaksanaan' => $queryJadwal->orderBy('kelas_jadwals.hari_pelaksanaan', $this->sortDirection),
            'jam_pelaksanaan' => $queryJadwal->orderBy('kelas_jadwals.jam_mulai', $this->sortDirection),
            'kapasitas' => $queryJadwal
                ->withCount('mahasiswas')
                ->orderBy('mahasiswas_count', $this->sortDirection),
            'password' => $queryJadwal->orderBy('kelas_jadwals.'.$pwSearch, $this->sortDirection),
            'tanggal_pelaksanaan' => $queryJadwal->orderBy('kelas_jadwals.tanggal_mulai', $this->sortDirection),
            'created_at' => $queryJadwal->orderBy('kelas_jadwals.created_at', $this->sortDirection),
            'updated_at' => $queryJadwal->orderBy('kelas_jadwals.updated_at', $this->sortDirection),
            default => $queryJadwal->orderBy('kelas_jadwals.id', $this->sortDirection),
        };
    }
}
