<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

// use App\Models\Akademik\SubCPMK;
use App\Models\Kelas\KelasSesi;
use Livewire\WithPagination;

trait WithSesiFilters
{
    use WithPagination;

    public $search = '';

    public $filterSesi = '';

    public $searchBobotSesi = '';

    public function inputSesiSearch($idJadwal)
    {
        $querySesi = KelasSesi::where('kj_id', $idJadwal)
            ->with(['jadwal_rel', 'jadwal_rel.kelas_rel', 'override'])->select('kelas_sesi.*');

        if ($this->hasProperty('searchMode') && ($this->searchMode == 'simple' || $this->searchMode == 'smart')) {
            $search = $this->search;
            if (! empty($search)) {
                if ($this->searchMode == 'smart') {
                    $querySesi->searchKelasSesiSmart($search);
                } else {
                    $querySesi->searchKelasSesi($search);
                }
            }
            $this->sortFieldOrderSesi($querySesi);
        }

        return $querySesi;
    }

    public function sortFieldOrderSesi($querySesi)
    {
        $querySesi->select('kelas_sesi.*');

        return match ($this->sortField) {
            'pertemuan_ke' => $querySesi->orderBy('kelas_sesi.pertemuan_ke', $this->sortDirection),
            'hari_pelaksanaan' => $querySesi->orderByRaw("
                CASE DAYNAME(kelas_sesi.tanggal)
                    WHEN 'Monday'    THEN 1
                    WHEN 'Tuesday'   THEN 2
                    WHEN 'Wednesday' THEN 3
                    WHEN 'Thursday'  THEN 4
                    WHEN 'Friday'    THEN 5
                    WHEN 'Saturday'  THEN 6
                    WHEN 'Sunday'    THEN 7
                END " . $this->sortDirection
            ),
            'tanggal_pelaksanaan' => $querySesi->orderByRaw("
                COALESCE(
                    (SELECT tanggal FROM kelas_sesi_overrides WHERE kelas_sesi_overrides.sesi_id = kelas_sesi.id),
                    kelas_sesi.tanggal
                ) " . $this->sortDirection
            ),

            // 'metode' => $querySesi->orderByRaw("
            //     CASE 
            //         -- 1. KASUS 16 SCPMK: Urutan murni berdasarkan metode di sub_cpmks
            //         WHEN (SELECT COUNT(*) FROM sub_cpmks 
            //             JOIN cpmk_pivot_scpmk ON sub_cpmks.id = cpmk_pivot_scpmk.scpmk_id
            //             JOIN rps_pivot_cpmk ON cpmk_pivot_scpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
            //             WHERE rps_pivot_cpmk.rps_id = (SELECT rps_id FROM kelas WHERE id = (SELECT kelas_id FROM kelas_jadwals WHERE id = kelas_sesi.kj_id))) = 16 
            //         THEN (SELECT sub_cpmks.metode FROM sub_cpmks 
            //             JOIN cpmk_pivot_scpmk ON sub_cpmks.id = cpmk_pivot_scpmk.scpmk_id
            //             JOIN rps_pivot_cpmk ON cpmk_pivot_scpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
            //             WHERE cpmk_pivot_scpmk.sort_order = kelas_sesi.pertemuan_ke LIMIT 1)

            //         -- 2. KASUS 15 SCPMK: Cek UTS (P8) / UAS (P16) dan geser
            //         WHEN (SELECT COUNT(*) FROM rps) = 15 THEN
            //             CASE 
            //                 WHEN kelas_sesi.pertemuan_ke = 8 THEN 1 -- UTS (Tetap P8)
            //                 WHEN kelas_sesi.pertemuan_ke = 16 THEN 2 -- UAS (P16)
            //                 -- Jika UAS tidak ada (asumsi tidak ada data P16 di sub_cpmk), P16 jadi metode normal
            //                 WHEN kelas_sesi.pertemuan_ke > 8 THEN kelas_sesi.pertemuan_ke - 1 
            //                 ELSE kelas_sesi.pertemuan_ke
            //             END

            //         -- 3. KASUS 14 SCPMK: UTS P8, UAS P9, sisanya digeser
            //         ELSE 
            //             CASE 
            //                 WHEN kelas_sesi.pertemuan_ke = 8 THEN 1 -- UTS
            //                 WHEN kelas_sesi.pertemuan_ke = 9 THEN 2 -- UAS
            //                 WHEN kelas_sesi.pertemuan_ke > 9 THEN kelas_sesi.pertemuan_ke - 2
            //                 ELSE kelas_sesi.pertemuan_ke
            //             END
            //     END " . $this->sortDirection),

            'total_absensi' => $querySesi->orderByRaw("(
                SELECT COUNT(*) 
                FROM mahasiswa_kehadiran 
                WHERE mahasiswa_kehadiran.sesi_id = kelas_sesi.id 
                AND mahasiswa_kehadiran.status IN ('Hadir', 'Terlambat', 'Dispensasi')
                AND mahasiswa_kehadiran.mahasiswa_id IN (
                    SELECT mahasiswa_id 
                    FROM mahasiswa_kelas 
                    WHERE mahasiswa_kelas.kj_id = kelas_sesi.kj_id
                )
            ) " . $this->sortDirection),

            'total_absensi_all' => $querySesi->orderByRaw("(
                SELECT COUNT(*) 
                FROM mahasiswa_kehadiran 
                WHERE mahasiswa_kehadiran.sesi_id = kelas_sesi.id 
                AND mahasiswa_kehadiran.status IN ('Hadir', 'Terlambat', 'Dispensasi')
            ) " . $this->sortDirection),

            'created_at' => $querySesi->orderBy('kelas_sesi.created_at', $this->sortDirection),
            'updated_at' => $querySesi->orderBy('kelas_sesi.updated_at', $this->sortDirection),
            'id' => $querySesi->orderBy('kelas_sesi.id', $this->sortDirection),
            default => $querySesi->orderBy('kelas_sesi.pertemuan_ke', $this->sortDirection),
        };
    }

    // public function inputJadwalSearch($idKelas = null)
    // {
    //     if (! empty($idKelas)) {
    //         $queryJadwal = KelasJadwal::where('kelas_id', $idKelas)
    //             ->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
    //     } else {
    //         if (Auth::user()->mahasiswa) {
    //             $queryJadwal = KelasJadwal::whereHas('mahasiswas', function ($q) {
    //                 $q->where('mahasiswas.id', Auth::user()->mahasiswa->id);
    //             })->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
    //         } else {
    //             $queryJadwal = KelasJadwal::query()->with(['kelas_rel', 'kelas_rel.rps_rel.mk_rel.prodis', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel', 'kelas_rel.rps_rel.mk_rel.prodis.dp_rel.fk_rel']);
    //         }
    //     }

    //     if ($this->hasProperty('searchMode') && ($this->searchMode == 'simple' || $this->searchMode == 'smart')) {
    //         $search = $this->search;
    //         if (! empty($search)) {
    //             if ($this->searchMode == 'smart') {
    //                 $queryJadwal->searchKelasJadwalSmart($search);
    //             } else {
    //                 $queryJadwal->searchKelasJadwal($search);
    //             }
    //         }
    //         $this->sortFieldOrderJadwal($queryJadwal);
    //     }

    //     return $queryJadwal;
    // }

    public function filterBySesi($kelas)
    {
        $this->filterSesi = $kelas;
        $this->resetPage();
    }
}
