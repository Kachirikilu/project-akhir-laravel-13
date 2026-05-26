<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithUserFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $searchAngkatan = '';

    public function updatingSearchAngkatan()
    {
        $this->resetPage();
    }

    public function resetInputAngkatan()
    {
        $this->reset('searchAngkatan');
        $this->resetPage();
    }

    // public function inputUserSearch($role = null, $id_jadwal = null)
    // {
    //     if (!$role) {
    //         $queryUser = User::query()
    //         ->with([
    //             'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
    //             'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
    //             'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
    //             'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
    //         ]);
    //     } elseif ($role == 'admin') {
    //         $queryUser = User::query()
    //         ->with([
    //             'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
    //         ]);
    //     } elseif ($role == 'dosen') {
    //         $queryUser = User::query()
    //         ->with([
    //             'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
    //             'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
    //         ]);
    //     } elseif ($role == 'mahasiswa') {
    //         $queryUser = User::query()
    //         ->with([
    //             'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
    //         ]);

    //         if ($id_jadwal) {
    //             $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($id_jadwal) {
    //                 $q->where('kj_id', $id_jadwal);
    //             });
    //         }
    //     }

    //     $search = $this->search;

    //     if (! empty($search)) {
    //         $queryUser->searchUser($search);
    //     }

    //     if (! empty($this->searchAngkatan) && $this->switchTable == 'mahasiswa') {
    //         $queryUser->searchUser($this->searchAngkatan, true);
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

    //     if (! empty($this->selectedRPSId) && $this->switchTable === 'dosen') {
    //         $queryUser->whereHas('dosen.rps', function ($q) {
    //             $q->where('rps.id', $this->selectedRPSId);
    //         });
    //     }

    //     $this->sortFieldOrderUser($queryUser);

    //     return $queryUser;
    // }

    public function inputUserSearch($role = null, $id_jadwal = null)
    {
        if (! $role) {
            $queryUser = User::query()->with([
                'admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel',
                'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
                'mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel',
            ]);
        } elseif ($role == 'admin') {
            $queryUser = User::query()->with(['admin', 'admin.pr_rel', 'admin.pr_rel.dp_rel', 'admin.pr_rel.dp_rel.fk_rel']);
        } elseif ($role == 'dosen') {
            $queryUser = User::query()->with([
                'dosen', 'dosen.pr_rel', 'dosen.pr_rel.dp_rel', 'dosen.pr_rel.dp_rel.fk_rel',
                'dosen.rps', 'dosen.scpmks', 'dosen.sesiMengajars.jadwal.kelas_rel',
            ]);
        } elseif ($role == 'mahasiswa') {
            $queryUser = User::query()->with(['mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel']);

            if ($id_jadwal) {
                $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($id_jadwal) {
                    $q->where('kj_id', $id_jadwal);
                });
            }
        }

        $search = trim($this->search);

        if (! empty($search)) {
            if (! str_contains($search, '%')) {
                $queryUser->where(function ($q) use ($search) {
                    $q->searchUser($search);
                });
            }
        }

        if (! empty($this->searchAngkatan) && $this->switchTable == 'mahasiswa') {
            $queryUser->searchUser($this->searchAngkatan, true);
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

        if (! empty($this->selectedRPSId) && $this->switchTable === 'dosen') {
            $queryUser->whereHas('dosen.rps', function ($q) {
                $q->where('rps.id', $this->selectedRPSId);
            });
        }

        $this->sortFieldOrderUser($queryUser);

        return $queryUser;
    }

    public function buttonUserFilter($queryUser)
    {
        $queryUser->when(in_array($this->switchTable, ['admin', 'dosen', 'mahasiswa']), function ($q) {
            $q->whereHas($this->switchTable);
        });

        if ($this->switchTable === 'dosen') {
            if (! empty($this->filterDosen)) {
                if ($this->filterDosen == 'dosen-rps') {
                    $queryUser->whereHas('dosen.rps');
                } elseif ($this->filterDosen == 'dosen-non-rps') {
                    $queryUser->whereDoesntHave('dosen.rps');
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
        } elseif ($this->filterStatus === '') {
            $queryUser->where(function ($q) {
                $q->whereHas('admin.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('dosen.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id))
                    ->orWhereHas('mahasiswa.pr_rel', fn ($sub) => $sub->where('prodis.id', Auth::user()->pr_id));
            });
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

    public function sortFieldOrderUser($queryUser)
    {
        $profileFields = [
            'role', 'admin_id', 'dosen_id', 'mahasiswa_id',
            'name', 'identity1', 'identity2', 'identity3', 'nik',
            'prodi', 'status', 'angkatan', 'kode',
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

        if ($this->sortField === 'prodi') {
            return $this->applyProdiSort($queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
                ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
                ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
                'COALESCE(ap.strata, dp.strata, mp.strata)',
                'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)');
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
            'kode' => 'COALESCE(admins.name, dosens.name, mahasiswas.name)',
            'identity1' => 'COALESCE(admins.nip, dosens.nip, mahasiswas.nim)',
            'identity2' => 'COALESCE(admins.nitk, dosens.nidn)',
            'identity3' => 'dosens.nidk',
            'nip' => 'COALESCE(admins.nip, dosens.nip)',
            'nitk' => 'admins.nitk',
            'nidn' => 'dosens.nidn',
            'nidk' => 'dosens.nidk',
            'nim' => 'mahasiswas.nim',
            'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',
            'status' => 'COALESCE(admins.status, dosens.status, mahasiswas.status)',
            'angkatan' => 'mahasiswas.angkatan',
            'created_at' => 'users.created_at',
            'updated_at' => 'users.updated_at',
            default => 'users.id'
        };

        return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    }
}
