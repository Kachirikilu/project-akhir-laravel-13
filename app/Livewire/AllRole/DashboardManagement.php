<?php

namespace App\Livewire\AllRole;

use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardManagement extends Component
{
    use HasSortir;
    use HasStats;
    use HasToast;
    use WithPagination;


    protected $listeners = [
        'refresh-table' => 'refreshDashboardsList',
        'refresh-data-dashboard' => 'refreshDashboardsList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft'
    ];

    #[On('refresh-data-dashboard')]
    #[On('refresh-table')]
    public function refreshDashboardsList()
    {
        $this->resetPage();
    }

    public function loadingTable() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $stats = collect();

        $prId = Auth::user()->pr_id;

        $statsKelas = $this->getStatsKelas(false);

        if (Auth::user()->admin || Auth::user()->dosen) {
            $statsObe = $this->getStatsObeProdi(false, $prId);
            $statsRps = $this->getStatsRpsProdi(false, $prId);
            $statsMk = $this->getStatsMk(false);
        }
        if (Auth::user()->admin) {
            $statsDsn = $this->getStatsDosen(false);
            $statsMhs = $this->getStatsMahasiswaProdi(false, $prId);
            $stats = array_merge(
                $statsObe,
                $statsRps,
                $statsDsn,
                $statsMhs,
                $statsMk,
                $statsKelas
            );
        } elseif (Auth::user()->dosen) {
            $statsTimDosen = $this->getStatsTimDosen(false);
            $stats = array_merge(
                $statsObe,
                $statsRps,
                $statsTimDosen,
                $statsKelas,
                $statsMk
            );    
        } elseif (Auth::user()->mahasiswa) {
            $stats = array_merge(
                $statsKelas,
            );   
        }

        return view('livewire.all-role.dashboard', [
            'stats' => $stats ?? null,
        ]);
    }
}
