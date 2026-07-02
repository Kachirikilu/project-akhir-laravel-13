<?php

namespace App\Livewire\AllRole;

// use App\Livewire\AllRole\KelasManagement\WithKelasFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasModal;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasToast;
// use App\Livewire\Global\WithDepartemenSearchFilters;
// use App\Livewire\Global\WithDosenSearchFilters;
// use App\Livewire\Global\WithFakultasSearchFilters;
// use App\Livewire\Global\WithKelasSearchFilters;
// use App\Livewire\AllRole\KelasManagement\WithKelasDelete;
// use App\Livewire\Global\WithMKSearchFilters;
// use App\Livewire\Global\WithProdiSearchFilters;
// use App\Livewire\Global\WithRPSSearchFilters;
// use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSShow;
// use App\Models\Kelas\Kelas;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Prodi;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


use Livewire\WithPagination;

class DashboardManagement extends Component
{
    use HasSortir;
    use HasToast;
    // use WithDepartemenSearchFilters;
    // use WithDosenSearchFilters;
    // use WithFakultasSearchFilters;

    // use WithKelasDelete;
    // use WithKelasFilters;
    // use WithKelasModal;
    // use WithKelasDelete;
    // use WithKelasSearchFilters;
    // use WithMKSearchFilters;
    // use WithPagination;
    // use WithProdiSearchFilters;
    // use WithRPSSearchFilters;
    // use WithRPSShow;

    public $user_id;

    public User $user;

    public $role;

    public $pr_id;

    public Prodi $prodi;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $userId = Auth::user()->id;
        $role = Auth::user()->role;


        $roleLow = strtolower($role);

        if (! empty($userId)) {
            $user = User::find($userId);
        } else {
            abort(404, 'ID User tidak ditemukan!');
        }

        if (! $user) {
            abort(404, "User dengan ID $userId tidak ditemukan!");
        }

        if ($roleLow == 'admin') {
            $modelRole = $user->admin;
            if (! $modelRole) {
                abort(404, "Role Admin pada User dengan ID $userId tidak ditemukan!");
            }
            $pr_id = $modelRole->pr_id;
        } elseif ($roleLow == 'dosen') {
            $modelRole = $user->dosen;
            if (! $modelRole) {
                abort(404, "Role Dosen pada User dengan ID $userId tidak ditemukan!");
            }
            $pr_id = $modelRole->pr_id;
        } elseif ($roleLow == 'mahasiswa') {
            $modelRole = $user->mahasiswa;
            if (! $modelRole) {
                abort(404, "Role Mahasiswa pada User dengan ID $userId tidak ditemukan!");
            }
            $pr_id = $modelRole->pr_id;
        } else {
            abort(404, 'Role User tidak ditemukan!');
        }

        if (! $pr_id) {
            abort(404, 'ID Program Studi tidak ditemukan!');
        }

        $prodi = Prodi::find($pr_id);
        if (! $prodi) {
            abort(404, 'Program Studi tidak ditemukan!');
        }

        $this->role = $modelRole;
        $this->user_id = $userId;
        $this->user = $user;
        $this->pr_id = $pr_id;
        $this->prodi = $prodi;
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.all-role.dashboard');
    }
}
