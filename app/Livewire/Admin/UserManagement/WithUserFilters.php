<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithUserFilters
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $searchAngkatan = '';

    public $filterAngkatan = '';

    public $totalSeluruhAngkatan = '';

    public $totalAngkatan = [];

    public $angkatanFilter = [];

    public function updatingSearchAngkatan()
    {
        $this->resetPage();
    }

    public function resetInputAngkatan()
    {
        $this->reset('searchAngkatan');
        $this->resetPage();
    }

    // public function inputUserSearch($role = null, $jadwalId = null, $prId = null, $noFilter = false)
    // {
    //     if (! $role) {
    //         $queryUser = User::query()->with([
    //             'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
    //             'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
    //             'dosen.tim_dosens.rps',
    //             'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
    //         ]);
    //     } elseif ($role == 'admin') {
    //         $queryUser = User::query()->with(['admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel']);
    //         $queryUser = User::whereHas('admin');
    //     } elseif ($role == 'dosen') {
    //         $queryUser = User::query()->with([
    //             'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
    //             'dosen.tim_dosens.rps',
    //         ]);
    //         $queryUser = User::whereHas('dosen');
    //     } elseif ($role == 'mahasiswa') {
    //         $queryUser = User::query()->with(['mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel']);
    //         $queryUser = User::whereHas('mahasiswa');

    //         if ($jadwalId) {
    //             $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($jadwalId) {
    //                 $q->where('kj_id', $jadwalId);
    //             });
    //         }
    //     }

    //     if (! empty($prId)) {
    //         $queryUser->inLocationUser('prodi', $prId);
    //     }

    //     if ($this->filterStatus !== '') {
    //         if ($this->selectedPrId) {
    //             $queryUser->inLocationUser('prodi', $this->selectedPrId);
    //         }
    //         if ($this->selectedDpId) {
    //             $queryUser->inLocationUser('departemen', $this->selectedDpId);
    //         }
    //         if ($this->selectedFkId) {
    //             $queryUser->inLocationUser('fakultas', $this->selectedFkId);
    //         }
    //     }

    //     if (! empty($this->selectedRPSId) && $role === 'dosen') {
    //         $queryUser->whereHas('dosen.tim_dosens.rps', function ($q) {
    //             $q->where('rps.id', $this->selectedRPSId);
    //         });
    //     }

    //     if (!$noFilter) {
    //         if (! empty($prId)) {
    //             $this->buttonUserFilter($queryUser, 1);
    //         } else {
    //             $this->buttonUserFilter($queryUser);
    //         }
    //     }

    //     if ($this->hasProperty('searchMode') && $this->searchMode == 'simple' && $this->filterAngkatan == '') {
    //         $search = trim($this->search);
    //         if (! empty($search)) {
    //             if (! str_contains($search, '%')) {
    //                 $queryUser->where(function ($q) use ($search) {
    //                     $q->searchUser($search);
    //                 });
    //             }
    //         }
    //         if (! empty($this->searchAngkatan) && $role == 'mahasiswa') {
    //             $queryUser->searchUser($this->searchAngkatan, true);
    //         }
    //         $this->sortFieldOrderUser($queryUser);
    //     }

    //     // filterAngkatan

    //     if (! empty($this->filterAngkatan) && $role === 'mahasiswa') {
    //         $queryUser->whereHas('mahasiswa', function ($q) {
    //             $q->where('mahasiswas.angkatan', $this->filterAngkatan);
    //         });
    //     }

    //     return $queryUser;
    // }
    public function inputUserSearch($role = null, $jadwalId = null, $prId = null, $noFilter = false)
    {
        if (! $role) {
            $queryUser = User::query()->with([
                'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
                'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                'dosen.tim_dosens.rps',
                'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
            ]);
        } elseif ($role == 'admin') {
            $queryUser = User::query()
                ->with(['admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel'])
                ->whereHas('admin');
        } elseif ($role == 'dosen') {
            $queryUser = User::query()
                ->with([
                    'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                    'dosen.tim_dosens.rps',
                ])
                ->whereHas('dosen');
        } elseif ($role == 'mahasiswa') {
            $queryUser = User::query()
                ->with(['mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel'])
                ->whereHas('mahasiswa');
            if ($jadwalId) {
                $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($jadwalId) {
                    $q->where('kj_id', $jadwalId);
                });
            }
        }

        if (! empty($prId)) {
            $queryUser->inLocationUser('prodi', $prId);
        }

        if ($this->filterStatus !== '') {
            if ($this->selectedPrId) {
                $queryUser->inLocationUser('prodi', $this->selectedPrId);
            }
            if ($this->selectedDpId) {
                $queryUser->inLocationUser('departemen', $this->selectedDpId);
            }
            if ($this->selectedFkId) {
                $queryUser->inLocationUser('fakultas', $this->selectedFkId);
            }
        }

        if (! empty($this->selectedRPSId) && $role === 'dosen') {
            $queryUser->whereHas('dosen.tim_dosens.rps', function ($q) {
                $q->where('rps.id', $this->selectedRPSId);
            });
        }

        if (!$noFilter) {
            if (! empty($prId)) {
                $this->buttonUserFilter($queryUser, 1);
            } else {
                $this->buttonUserFilter($queryUser);
            }
        }

        if ($this->hasProperty('searchMode') && $this->searchMode == 'simple' && $this->filterAngkatan == '') {
            $search = trim($this->search);
            if (! empty($search)) {
                if (! str_contains($search, '%')) {
                    $queryUser->where(function ($q) use ($search) {
                        $q->searchUser($search);
                    });
                }
            }
            if (! empty($this->searchAngkatan) && $role == 'mahasiswa') {
                $queryUser->searchUser($this->searchAngkatan, true);
            }
            $this->sortFieldOrderUser($queryUser);
        }

        if (! empty($this->filterAngkatan) && $role === 'mahasiswa') {
            $queryUser->whereHas('mahasiswa', function ($q) {
                $q->where('mahasiswas.angkatan', $this->filterAngkatan);
            });
        }

        return $queryUser;
    }
    
    protected function generateAngkatanFilter(int $jumlah = 5): array
    {
        $now = Carbon::now();

        $tahunTerbaru =
            $now->month >= 6
                ? $now->year
                : $now->year - 1;

        return collect(range(0, $jumlah - 1))
            ->map(fn ($i) => $tahunTerbaru - $i)
            ->values()
            ->toArray();
    }

    protected function loadTotalAngkatan($query): void
    {
        $this->angkatanFilter =
            $this->generateAngkatanFilter();

        $this->totalSeluruhAngkatan =
            (clone $query)->count();

        $this->totalAngkatan = [];

        foreach ($this->angkatanFilter as $angkatan) {

            $this->totalAngkatan[$angkatan] =
                (clone $query)
                    ->whereHas('mahasiswa', function ($q) use ($angkatan) {
                        $q->where('angkatan', $angkatan);
                    })
                    ->count();
        }
    }

    public function buttonUserFilter($queryUser, $havePr = null)
    {
        // $queryUser->when(in_array($this->switchTable, ['admin', 'dosen', 'mahasiswa']), function ($q) {
        //     $q->whereHas($this->switchTable);
        // });

        if ($this->switchTable === 'dosen') {
            if (! empty($this->filterDosen)) {
                if ($this->filterDosen == 'dosen-rps') {
                    $queryUser->whereHas('dosen.tim_dosens.rps');
                } elseif ($this->filterDosen == 'dosen-non-rps') {
                    $queryUser->whereDoesntHave('dosen.tim_dosens.rps');
                }
            }
        }

        // Filter by status
        if ($this->filterStatus === 'dosen-prodi') {
            $queryUser->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', Auth::user()->pr_id));
        } elseif ($this->filterStatus === 'dosen-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('dosen', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'dosen-non-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('dosen', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'user-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'user-non-aktif') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('dosen', fn ($sub) => $sub->where('status', '!=', 'Aktif'))
                    ->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'mahasiswa-aktif') {
            $queryUser->where(function ($q) {
                $q->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', 'Aktif'));
            });
        } elseif ($this->filterStatus === 'mahasiswa-non-aktif') {
            $queryUser->where(function ($q) {
                $q->orWhereHas('mahasiswa', fn ($sub) => $sub->where('status', '!=', 'Aktif'));
            });
        } elseif ($this->filterStatus === '' && ! $havePr) {
            $queryUser->where(function ($q) {
                $q->whereHas('admin.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('dosen.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('mahasiswa.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id));
            });
        }
        if ($this->switchTable === 'mahasiswa') {
            $queryForAngkatanCount = clone $queryUser;
            $this->loadTotalAngkatan($queryForAngkatanCount);
        }

        return $queryUser;
    }

    public function filterByUser($role)
    {
        $this->switchTable = $role;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function filterByAngkatan($angkatan)
    {
        $this->filterAngkatan = $angkatan;
        $this->reset(['search', 'searchAngkatan']);
        $this->resetPage();
    }

    private function applySubqueriesToUsers($queryUser, $idJadwal, $expiredCount)
    {
        $statuses = [
            'mhs_absensi' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Izin','Sakit','Dispensasi')",
            'mhs_masuk' => "mahasiswa_kehadiran.status IN ('Hadir','Terlambat','Dispensasi')",
            'mhs_hadir' => "mahasiswa_kehadiran.status = 'Hadir'",
            'mhs_terlambat' => "mahasiswa_kehadiran.status = 'Terlambat'",
            'mhs_izin' => "mahasiswa_kehadiran.status = 'Izin'",
            'mhs_sakit' => "mahasiswa_kehadiran.status = 'Sakit'",
            'mhs_dispensasi' => "mahasiswa_kehadiran.status = 'Dispensasi'",
            'mhs_absen' => "(mahasiswa_kehadiran.status = 'Absen' OR mahasiswa_kehadiran.status IS NULL)",
            'mhs_poin_absensi' => "CASE WHEN mahasiswa_kehadiran.status IN ('Hadir','Dispensasi') THEN 2 WHEN mahasiswa_kehadiran.status IN ('Terlambat','Izin','Sakit') THEN 1 ELSE 0 END",
        ];

        $queryUser->selectRaw("GREATEST(0, ? - ((SELECT COALESCE(SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Terlambat' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Dispensasi' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?))) as mhs_tidak_masuk", [$expiredCount, $idJadwal, $idJadwal, $idJadwal, $idJadwal, $idJadwal]);
    }

    public function sortFieldOrderUser($queryUser)
    {
        $profileFields = [
            'role', 'admin_id', 'dosen_id', 'mahasiswa_id',
            'name', 'identity1', 'identity2', 'identity3', 'nik', 'kampus',
            'count_rps', 'total_sks',
            'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_mutu',
            'rekap_mhs', 'ipk_mhs', 'mutu_mhs',
            'program_studi', 'status', 'angkatan', 'kode', 'pertemuan_ke',
            'nip', 'nitk', 'nidn', 'nidk', 'nim',
        ];

        if (in_array($this->sortField, $profileFields)) {
            return $this->applyUserCombinedSort($queryUser);
        }

        $field = ($this->sortField === 'id') ? 'users.id' : $this->sortField;

        return $queryUser->orderBy($field, $this->sortDirection);
    }

    private function applyUserCombinedSort($queryUser)
    {
        $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
            ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
            ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
            ->select('users.*');

        if ($this->sortField === 'program_studi') {
            return $this->applyProdiSort(
                $queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
                    ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
                    ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
                'COALESCE(ap.strata, dp.strata, mp.strata)',
                'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)'
            );
        }
        if ($this->sortField === 'program_studi') {
            return $this->applyProdiSort(
                $queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
                    ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
                    ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
                'COALESCE(ap.strata, dp.strata, mp.strata)',
                'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)'
            );
        }

        if (
            $this->sortField === 'rekap_mhs' ||
            $this->sortField === 'ipk_mhs' ||
            $this->sortField === 'mutu_mhs' ||
            $this->sortField === 'count_rps' ||
            $this->sortField === 'total_sks'
        ) {
            $queryUser
                ->leftJoin(
                    'rekap_nilai_mahasiswa as rnm',
                    'mahasiswas.id',
                    '=',
                    'rnm.mahasiswa_id'
                );
            return match ($this->sortField) {
                'rekap_mhs', 'ipk_mhs', 'mutu_mhs'  => $queryUser->orderBy(
                    'rnm.nilai',
                    $this->sortDirection
                ),
                'count_rps'  => $queryUser->orderBy(
                    'rnm.count_rps',
                    $this->sortDirection
                ),
                'total_sks'  => $queryUser->orderBy(
                    'rnm.total_sks',
                    $this->sortDirection
                ),
                default => $queryUser,
            };
        }

        $aliasSort = match ($this->sortField) {
            'mhs_nilai_akhir',
            'mhs_nilai_index',
            'mhs_nilai_mutu' => 'mhs_nilai_akhir',

            // 'rekap_mhs', 'ipk_mhs', 'mutu_mhs' => 'rekap_mhs',

            'count_rps',
            'total_sks' => $this->sortField,

            default => null,
        };

        if ($aliasSort) {
            return $queryUser->orderBy(
                $aliasSort,
                $this->sortDirection
            );
        }

        $orderByRaw = match ($this->sortField) {
            'admin_id' => 'admins.id',
            'dosen_id' => 'dosens.id',
            'mahasiswa_id' => 'mahasiswas.id',

            'role' => 'CASE
                        WHEN admins.id IS NOT NULL THEN 1
                        WHEN dosens.id IS NOT NULL THEN 2
                        WHEN mahasiswas.id IS NOT NULL THEN 3
                        ELSE 4
                    END',

            'name' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
            'kode' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',

            'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
            'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
            'identity3' => 'dosens.nidk',

            'nip' => 'COALESCE(admins.nip, dosens.nip)',
            'nitk' => 'admins.nitk',
            'nidn' => 'dosens.nidn',
            'nidk' => 'dosens.nidk',
            'nim' => 'mahasiswas.nim',

            'pertemuan_ke' => 'mahasiswas.nim',

            'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',

            'kampus' => 'COALESCE(
                            admins.kode_wilayah,
                            mahasiswas.kode_wilayah
                        )',

            'status' => 'COALESCE(
                            admins.status,
                            dosens.status,
                            mahasiswas.status
                        )',

            'angkatan' => 'mahasiswas.angkatan',

            'created_at' => 'users.created_at',
            'updated_at' => 'users.updated_at',

            default => 'users.id',
        };

        return $queryUser->orderByRaw(
            "$orderByRaw {$this->sortDirection}"
        );
    }
    // private function applyUserCombinedSort($queryUser)
    // {
    //     $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
    //         ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
    //         ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
    //         ->select('users.*');

    //     if ($this->sortField === 'program_studi') {
    //         return $this->applyProdiSort($queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
    //             ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
    //             ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
    //             'COALESCE(ap.strata, dp.strata, mp.strata)',
    //             'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)');
    //     }

    //     $orderByRaw = match ($this->sortField) {
    //         'admin_id' => 'admins.id',
    //         'dosen_id' => 'dosens.id',
    //         'mahasiswa_id' => 'mahasiswas.id',
    //         'role' => 'CASE
    //                         WHEN admins.id IS NOT NULL THEN 1
    //                         WHEN dosens.id IS NOT NULL THEN 2
    //                         WHEN mahasiswas.id IS NOT NULL THEN 3
    //                         ELSE 4
    //                     END',
    //         'name' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
    //         'kode' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
    //         'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
    //         'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
    //         'identity3' => 'dosens.nidk',
    //         'nip' => 'COALESCE(admins.nip, dosens.nip)',
    //         'nitk' => 'admins.nitk',
    //         'nidn' => 'dosens.nidn',
    //         'nidk' => 'dosens.nidk',
    //         'nim' => 'mahasiswas.nim',
    //         'count_rps' => '(SELECT COUNT(DISTINCT rpd.rps_id)
    //                         FROM rps_pivot_dosen rpd
    //                         WHERE rpd.dosen_id = dosens.id)',
    //         'total_sks' => '(SELECT COALESCE(SUM(mk.sks_kuliah), 0)
    //             FROM rps_pivot_dosen rpd
    //             JOIN rps r ON r.id = rpd.rps_id
    //             JOIN mata_kuliahs mk ON mk.id = r.mk_id
    //             WHERE rpd.dosen_id = dosens.id)',
    //         'pertemuan_ke' => 'mahasiswas.nim',
    //         'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',
    //         'kampus' => 'COALESCE(admins.kode_wilayah, mahasiswas.kode_wilayah)',
    //         'status' => 'COALESCE(admins.status, dosens.status, mahasiswas.status)',
    //         'angkatan' => 'mahasiswas.angkatan',
    //         'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_mutu' => 'mahasiswas.mhs_nilai_akhir',
    //         'created_at' => 'users.created_at',
    //         'updated_at' => 'users.updated_at',
    //         default => 'users.id'
    //     };

    //     return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    // }
}
