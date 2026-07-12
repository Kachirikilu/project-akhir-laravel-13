<?php

namespace App\Livewire\AllRole;

use App\Livewire\Global\HasSortir;
use App\Livewire\Global\HasStats;
use App\Livewire\Global\HasToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\WithPagination;
use Livewire\Component;

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
        'saveToDraft' => 'saveToDraft',
    ];

    #[On('refresh-data-dashboard')]
    #[On('refresh-table')]
    public function refreshDashboardsList()
    {
        $this->resetPage();
    }

    public function refreshStats()
    {
        $this->clearKelasStatsCache();

        if (Auth::user()->admin || Auth::user()->dosen) {
            $this->clearObeProdiStatsCache();
            $this->clearRpsProdiStatsCache();
            $this->clearMkStatsCache();

            if (Auth::user()->admin) {
                $this->clearDosenStatsCache();
                $this->clearMahasiswaProdiStatsCache();
            } else {
                $this->clearTimDosenStatsCache();
            }
        }
        $this->resetPage();
        $this->toast(text: 'Data Statistik Dashboard berhasil diperbarui!', type: 'info', variant: 'info');
    }

    // public function loadingTable() {}

    // public function updatedPerPage()
    // {
    //     $this->resetPage();
    // }

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

        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($session) {
                $agent = new Agent;
                $agent->setUserAgent($session->user_agent);

                return (object) [
                    'id' => $session->id,
                    'device' => $agent->device() ?: 'Desktop/Unknown',
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'is_desktop' => $agent->isDesktop(),
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => ($session->id === session()->getId()),
                ];
            })->sortByDesc(function ($session) {
                return $session->is_current;
            });

        return view('livewire.all-role.dashboard-management', [
            'stats' => $stats ?? null,
            'sessions' => $sessions,
        ]);
    }
}
