<?php

namespace App\Livewire\Staff\KelasManagement\JadwalManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Kelas\KelasJadwal;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

trait WithJadwalFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterJadwal = '';

    public $searchBobotJadwal = '';

    public function inputJadwalSearch($idKelas)
    {
        $queryJadwal = KelasJadwal::where('kelas_id', $idKelas)
            ->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
        $search = $this->search;

        if (! empty($search)) {
            $queryJadwal->searchKelasJadwal($search);
        }

        $this->sortFieldOrderJadwal($queryJadwal);

        return $queryJadwal;
    }

    public function filterByJadwal($kelas)
    {
        $this->filterJadwal = $kelas;
        $this->resetPage();
    }

    public function resetInputFilter()
    {
        $this->reset(['search', 'filterJadwal']);
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
