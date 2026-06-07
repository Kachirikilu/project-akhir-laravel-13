<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasSortir;
// use App\Models\Akademik\SubCPMK;
use App\Models\Kelas\KelasSesi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\WithPagination;

trait WithSesiFilters
{
    use HasSortir;
    use WithPagination;

    public $search = '';

    public $filterSesi = '';

    public $searchBobotSesi = '';

    // public function inputSesiSearch($idJadwal)
    // {
    //     $querySesi = KelasSesi::where('kj_id', $idJadwal)
    //         ->with(['jadwal_rel', 'jadwal_rel.kelas_rel']);
    //     $search = $this->search;

    //     if (! empty($search)) {
    //         $querySesi->searchKelasSesi($search);
    //     }

    //     $this->sortFieldOrderSesi($querySesi);

    //     return $querySesi;
    // }

    public function inputSesiSearch($idJadwal)
    {
        $querySesi = KelasSesi::where('kj_id', $idJadwal)
            ->with(['jadwal_rel', 'jadwal_rel.kelas_rel']);

        // $this->sortFieldOrderSesi($querySesi);

        return $querySesi;
    }

    // public function searchOutputSesi($querySesi, $idJadwal)
    // {
    //     $accessorFields = ['metode', 'kode_scpmk', 'kode_cpmk', 'bobot', 'tugas', 'w_tugas', 'w_mandiri', 'mhs_absensi', 'bobot_normalisasi'];

    //     $search = trim($this->search);
    //     $pureSearchLower = strtolower($search);
    //     $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

    //     $utsFields = array_map('trim', explode(',', strtolower(env('UTS_FIELDS', 'UTS,EVALUASI AWAL'))));
    //     $uasFields = array_map('trim', explode(',', strtolower(env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK'))));

    //     $targetKeywords = [$pureSearchLower];

    //     if (in_array($pureSearchLower, $utsFields)) {
    //         $targetKeywords = $utsFields;
    //     } elseif (in_array($pureSearchLower, $uasFields)) {
    //         $targetKeywords = $uasFields;
    //     }

    //     $isPercentSearch = str_contains($search, '%');

    //     if (! empty($search) || in_array($this->sortField, $accessorFields)) {

    //         $dbMatchedIds = (! empty($search) && ! $isPercentSearch)
    //             ? (clone $querySesi)->searchKelasSesi($search)->pluck('kelas_sesi.id')->toArray()
    //             : [];

    //         $allSesi = KelasSesi::where('kj_id', $idJadwal)
    //             ->with(['override', 'jadwal_rel.kelas_rel.rps_rel.cpmks.scpmks'])
    //             ->get();

    //         if (! empty($search)) {
    //             $cleanNumber = preg_replace('/[^0-9.]/', '', $pureSearchLower);
    //             $isNumericQuery = is_numeric($cleanNumber) && $cleanNumber !== '';

    //             $allSesi = $allSesi->filter(function ($sesi) use ($pureSearchLower, $searchClean, $dbMatchedIds, $cleanNumber, $isNumericQuery, $targetKeywords) {
    //                 $rawBobot = $sesi->override->bobot
    //                     ?? $sesi->scpmk_atr->bobot
    //                     ?? null;
    //                 $rawBobotNormalisasi = $sesi->bobot_normalisasi ?? null;
    //                 $matchBobot = $isNumericQuery
    //                     && is_numeric($rawBobot)
    //                     && abs((float) $rawBobot - (float) $cleanNumber) < 0.01;
    //                 $matchBobotNormalisasi = $isNumericQuery
    //                     && is_numeric($rawBobotNormalisasi)
    //                     && abs((float) $rawBobotNormalisasi - (float) $cleanNumber) < 0.01;
    //                 $matchTextGrup = false;
    //                 $metodeLower = strtolower($sesi->metode ?? '');
    //                 $tugasLower = strtolower($sesi->tugas ?? '');

    //                 foreach ($targetKeywords as $keyword) {
    //                     if (str_contains($metodeLower, $keyword) || str_contains($tugasLower, $keyword)) {
    //                         $matchTextGrup = true;
    //                         break;
    //                     }
    //                 }
    //                 $matchSubCPMK = false;
    //                 if (! empty($searchClean) && ! is_null($sesi->kode_scpmk)) {
    //                     $scpmkClean = preg_replace('/[^A-Za-z0-9]/', '', $sesi->kode_scpmk);
    //                     $matchSubCPMK = str_contains(strtolower($scpmkClean), strtolower($searchClean));
    //                 }

    //                 $matchCPMK = false;
    //                 if (! empty($searchClean) && ! is_null($sesi->kode_cpmk)) {
    //                     $cpmkClean = preg_replace('/[^A-Za-z0-9]/', '', $sesi->kode_cpmk);
    //                     $matchCPMK = str_contains(strtolower($cpmkClean), strtolower($searchClean));
    //                 }

    //                 $matchAbsensi = $isNumericQuery && (int) $sesi->mhs_absensi === (int) $cleanNumber;

    //                 $matchTugas = false;
    //                 if (! empty($searchClean) && ! is_null($sesi->tugas)) {
    //                     $matchTugas = str_contains(strtolower($scpmkClean), strtolower($searchClean));
    //                 }

    //                 $matchWTugas = false;
    //                 $matchWMandiri = false;

    //                 if (preg_match('/(\d+)\s*(?:m|menit)?/i', $pureSearchLower, $wMatches)) {
    //                     $searchMinutes = (int) $wMatches[1];
    //                     $rawWTugas = (int) ($sesi->override->w_tugas ?? $sesi->scpmk_atr->w_tugas ?? $sesi->w_tugas ?? 0);
    //                     $rawWMandiri = (int) ($sesi->override->w_mandiri ?? $sesi->scpmk_atr->w_mandiri ?? $sesi->w_mandiri ?? 0);

    //                     $matchWTugas = $rawWTugas === $searchMinutes;
    //                     $matchWMandiri = $rawWMandiri === $searchMinutes;
    //                 }

    //                 return in_array($sesi->id, $dbMatchedIds)
    //                     || $matchTextGrup
    //                     || $matchSubCPMK
    //                     || $matchCPMK
    //                     || $matchBobot
    //                     || $matchBobotNormalisasi
    //                     || $matchTugas
    //                     || $matchWTugas
    //                     || $matchWMandiri
    //                     || $matchAbsensi;
    //             });
    //         }

    //         $fieldToSort = in_array($this->sortField, $accessorFields) ? $this->sortField : 'pertemuan_ke';
    //         $sortedSesi = $this->sortDirection === 'asc'
    //             ? $allSesi->sortBy(fn ($sesi) => $sesi->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE)
    //             : $allSesi->sortByDesc(fn ($sesi) => $sesi->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE);

    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;

    //         return new LengthAwarePaginator(
    //             $sortedSesi->forPage($currentPage, $this->perPage)->values(),
    //             $sortedSesi->count(),
    //             $this->perPage,
    //             $currentPage,
    //             ['path' => Paginator::resolveCurrentPath()]
    //         );
    //     }

    //     return $querySesi->paginate($this->perPage);
    // }

    public function filterBySesi($kelas)
    {
        $this->filterSesi = $kelas;
        $this->resetPage();
    }

    // public function sortFieldOrderSesi($querySesi)
    // {
    //     $querySesi->select('kelas_sesi.*')
    //         ->withCount('kehadirans')
    //         ->leftJoin('kelas_sesi_overrides', 'kelas_sesi.id', '=', 'kelas_sesi_overrides.sesi_id')
    //         ->leftJoin('kelas_jadwals', 'kelas_sesi.kj_id', '=', 'kelas_jadwals.id');

    //     return match ($this->sortField) {
    //         'pertemuan_ke' => $querySesi->orderBy('kelas_sesi.pertemuan_ke', $this->sortDirection),
    //         'hari_pelaksanaan' => $querySesi->orderByRaw('WEEKDAY(kelas_sesi.tanggal) '.$this->sortDirection),
    //         'tanggal_pelaksanaan' => $querySesi->orderBy('kelas_sesi.tanggal', $this->sortDirection),
    //         'jam_pelaksanaan' => $querySesi->orderByRaw('COALESCE(kelas_sesi_overrides.jam_mulai, kelas_jadwals.jam_mulai) '.$this->sortDirection),
    //         'jumlah_absensi' => $querySesi->orderBy('kehadirans_count', $this->sortDirection),

    //         // 'metode' => $this->applyMetodeSort($querySesi),

    //         'created_at' => $querySesi->orderBy('kelas_sesi.created_at', $this->sortDirection),
    //         'updated_at' => $querySesi->orderBy('kelas_sesi.updated_at', $this->sortDirection),
    //         default => $querySesi->orderBy('kelas_sesi.id', $this->sortDirection),
    //     };
    // }

    // private function applyMetodeSort($querySesi)
    // {
    //     // Hubungkan sesi ke kelas, jadwals, dan rps
    //     $querySesi->leftJoin('kelas', 'kelas_jadwals.kelas_id', '=', 'kelas.id')
    //         ->leftJoin('rps', 'kelas.rps_id', '=', 'rps.id');

    //     $utsPattern = implode('|', SubCPMK::$UTS_FIELDS);
    //     $uasPattern = implode('|', SubCPMK::$UAS_FIELDS);

    //     // 1. Subquery ngecek apakah RPS ini punya penanda UTS internal
    //     $subHasUts = "(SELECT COUNT(*)
    //                     FROM sub_cpmks
    //                     JOIN cpmk_pivot_scpmk ON sub_cpmks.id = cpmk_pivot_scpmk.scpmk_id
    //                     JOIN rps_pivot_cpmk ON cpmk_pivot_scpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
    //                     WHERE rps_pivot_cpmk.rps_id = rps.id
    //                     AND (sub_cpmks.deskripsi REGEXP '{$utsPattern}' OR sub_cpmks.metode REGEXP '{$utsPattern}')) > 0";

    //     // 2. Subquery ngecek apakah RPS ini punya penanda UAS internal
    //     $subHasUas = "(SELECT COUNT(*)
    //                     FROM sub_cpmks
    //                     JOIN cpmk_pivot_scpmk ON sub_cpmks.id = cpmk_pivot_scpmk.scpmk_id
    //                     JOIN rps_pivot_cpmk ON cpmk_pivot_scpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
    //                     WHERE rps_pivot_cpmk.rps_id = rps.id
    //                     AND (sub_cpmks.deskripsi REGEXP '{$uasPattern}' OR sub_cpmks.metode REGEXP '{$uasPattern}')) > 0";

    //     // 3. Menentukan target index (offset data) berdasarkan aturan pertemuan
    //     $targetIndexSql = "CASE
    //         WHEN {$subHasUts} AND {$subHasUas} THEN (kelas_sesi.pertemuan_ke - 1)
    //         WHEN {$subHasUts} AND NOT {$subHasUas} THEN (kelas_sesi.pertemuan_ke - 1)
    //         WHEN NOT {$subHasUts} AND {$subHasUas} THEN CASE WHEN kelas_sesi.pertemuan_ke < 8 THEN (kelas_sesi.pertemuan_ke - 1) ELSE (kelas_sesi.pertemuan_ke - 2) END
    //         ELSE CASE WHEN kelas_sesi.pertemuan_ke < 8 THEN (kelas_sesi.pertemuan_ke - 1) ELSE (kelas_sesi.pertemuan_ke - 2) END
    //     END";

    //     // 4. STRATEGI BARU: Subquery yang langsung mencari metode berdasarkan ranking yang konsisten
    //     $subCpmkMetodeSql = "(
    //         SELECT sub.metode
    //         FROM (
    //             SELECT sub_cpmks.metode, rps_pivot_cpmk.rps_id,
    //                 ROW_NUMBER() OVER (
    //                     PARTITION BY rps_pivot_cpmk.rps_id
    //                     ORDER BY cpmk_pivot_scpmk.sort_order ASC, sub_cpmks.id ASC
    //                 ) - 1 as idx
    //             FROM sub_cpmks
    //             JOIN cpmk_pivot_scpmk ON sub_cpmks.id = cpmk_pivot_scpmk.scpmk_id
    //             JOIN rps_pivot_cpmk ON cpmk_pivot_scpmk.cpmk_id = rps_pivot_cpmk.cpmk_id
    //         ) sub
    //         WHERE sub.rps_id = kelas.rps_id
    //         AND sub.idx = {$targetIndexSql}
    //         LIMIT 1
    //     )";

    //     // 5. Aturan Fallback Gabungan untuk menentukan Teks Akhir yang akan diurutkan
    //     // Kita ganti '-' menjadi 'ZZZ' saat DESC atau ' ' saat ASC jika Anda ingin karakter kosong tidak mengacaukan abjad
    //     $resolvedMetodeRaw = "COALESCE(
    //         kelas_sesi_overrides.metode,
    //         CASE
    //             WHEN NOT {$subHasUts} AND kelas_sesi.pertemuan_ke = 8 THEN 'UTS'
    //             WHEN NOT {$subHasUas} AND kelas_sesi.pertemuan_ke = 16 THEN 'UAS'
    //             ELSE NULL
    //         END,
    //         {$subCpmkMetodeSql},
    //         ''
    //     )";

    //     // Urutkan berdasarkan teks metodenya secara alfabetis (A-Z / Z-A)
    //     // Ditambah urutan pertemuan_ke agar jika metodenya sama (misal sama-sama 'Kuliah'), dia urut berdasarkan waktu
    //     return $querySesi->orderByRaw("TRIM({$resolvedMetodeRaw}) {$this->sortDirection}, kelas_sesi.pertemuan_ke ASC");
    // }
}
