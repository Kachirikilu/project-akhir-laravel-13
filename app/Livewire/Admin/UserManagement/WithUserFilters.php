<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasSortir;
use App\Models\Auth\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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

    // public function inputUserSearch($role = null, $jadwal_id = null)
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

    //         if ($jadwal_id) {
    //             $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($jadwal_id) {
    //                 $q->where('kj_id', $jadwal_id);
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

    public function inputUserSearch($role = null, $jadwal_id = null)
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

            if ($jadwal_id) {
                $queryUser = $queryUser->whereHas('mahasiswa.jadwals', function ($q) use ($jadwal_id) {
                    $q->where('kj_id', $jadwal_id);
                });
            }
        }

        // $search = trim($this->search);

        // if (! empty($search) && ! $jadwal_id) {
        //     if (! str_contains($search, '%')) {
        //         $queryUser->where(function ($q) use ($search) {
        //             $q->searchUser($search);
        //         });
        //     }
        // }

        // if (! empty($this->searchAngkatan) && $this->switchTable == 'mahasiswa') {
        //     $queryUser->searchUser($this->searchAngkatan, true);
        // }

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

        // $this->sortFieldOrderUser($queryUser);

        return $queryUser;
    }

    public function searchOutputMahasiswa($queryUser, $idJadwal)
    {
        $accessorFields = [
            'mhs_absensi', 'mhs_masuk', 'mhs_terlambat', 'mhs_izin',
            'mhs_sakit', 'mhs_dispensasi', 'mhs_poin_absensi',
            'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_huruf', 'mhs_nilai_huruf', 'mhs_tidak_masuk',
        ];

        $search = trim($this->search);
        $searchLower = strtolower($search);

        // ====================================================
        // 1. EKSTRAKSI ANGKA, TEKS KATEGORI, DAN HURUF MUTU
        // ====================================================
        preg_match_all('/[0-9.,]+/', $search, $matchesNumbers);
        $searchNumbers = $matchesNumbers[0] ?? [];

        preg_match('/(^|\s)([a-eA-E](?:\+|\-)?)(?=\s|$)/i', $search, $matchesGrade);
        $searchGrade = isset($matchesGrade[2])
            ? strtolower(trim($matchesGrade[2]))
            : null;

        $searchCleanText = trim(preg_replace('/[^A-Za-z ]/', '', $searchLower));
        if ($searchGrade) {
            $searchCleanText = trim(str_replace($searchGrade, '', $searchCleanText));
        }

        // ==========================
        // AMBIL DATA PEMBAGI PERSEN
        // ==========================
        $totalSesiKelas = isset($this->countSesi) ? (clone $this->countSesi)->count() : 16;
        $totalSesiKelas = $totalSesiKelas ?: 1;

        // Helper pencocok angka parsial
        $anyNumberMatches = function ($value) use ($searchNumbers) {
            $value = $value ?? 0;

            if (empty($searchNumbers)) {
                return false;
            }

            $valRaw = strtolower(trim((string) $value));
            $valFloat = strtolower(trim(number_format((float) $value, 2, '.', '')));
            $valInt = strtolower(trim(number_format((float) $value, 0, '.', '')));

            foreach ($searchNumbers as $num) {
                $cleanNum = str_replace(',', '.', $num);
                if (
                    str_starts_with($valRaw, $cleanNum) ||
                    str_starts_with($valFloat, $cleanNum) ||
                    str_starts_with($valInt, $cleanNum) ||
                    $valRaw === $cleanNum
                ) {
                    return true;
                }
            }

            return false;
        };

        if (! empty($search) || in_array($this->sortField, $accessorFields)) {

            // 1. Ambil ID Database yang Lolos Pencarian Teks Standar (Nama, NIM, Angkatan)
            $dbMatchedIds = (! empty($search) && ! str_contains($search, '%'))
                ? (clone $queryUser)->where(function ($q) use ($search) {
                    $q->searchUser($search);
                })->pluck('users.id')->toArray()
                : [];

            // 2. Ambil Seluruh Data Master dengan Subquery Absensi Lengkap
            $allUsers = (clone $queryUser)->get();

            // 3. Lakukan Filter Koleksi di Memori PHP
            if (! empty($search)) {
                $allUsers = $allUsers->filter(function ($user) use ($searchLower, $searchCleanText, $searchNumbers, $searchGrade, $anyNumberMatches, $dbMatchedIds, $totalSesiKelas) {

                    $userHuruf = strtolower(
                        trim(
                            (string) ($user->mhs_nilai_huruf ?? 'E')
                        )
                    );

                    // A. KUNCI UTAMA: Jika dosen mengetik/mencari Huruf Mutu Spesifik (Cth: "A", "b+", "huruf B")
                    if ($searchGrade !== null) {
                        if (str_contains($searchGrade, '+') || str_contains($searchGrade, '-')) {
                            if ($userHuruf === $searchGrade) {
                                return true;
                            }
                        } else {
                            if (str_starts_with($userHuruf, $searchGrade)) {
                                return true;
                            }

                        }
                        if (in_array($user->id, $dbMatchedIds)) {
                            return true;
                        }

                        return false;
                    }

                    // B. Bypass jika lolos pencarian teks standar Nama/NIM (ketika tidak sedang mengunci huruf mutu)
                    if (in_array($user->id, $dbMatchedIds)) {
                        return true;
                    }

                    // C. Deteksi Teks Indikator Kategori Akademik & Kehadiran
                    $hasTextMasuk = (str_contains($searchLower, 'hadir') || str_contains($searchLower, 'masuk')) && ! str_contains($searchLower, 'tidak');
                    $hasTextTidakMasuk = str_contains($searchLower, 'tidak hadir') || str_contains($searchLower, 'tidak masuk');
                    $hasTextDispensasi = str_contains($searchLower, 'dispensasi') || str_contains($searchLower, 'dispensi');
                    $hasTextTerlambat = str_contains($searchLower, 'terlambat');
                    $hasTextIzin = str_contains($searchLower, 'izin');
                    $hasTextSakit = str_contains($searchLower, 'sakit');
                    $hasTextIndex = str_contains($searchLower, 'index') || str_contains($searchLower, 'indeks');
                    $hasTextHuruf = str_contains($searchLower, 'huruf') || str_contains($searchLower, 'mutu') || str_contains($searchLower, 'predikat');

                    $hasAnyCategoryText = $hasTextMasuk || $hasTextTidakMasuk || $hasTextDispensasi || $hasTextTerlambat || $hasTextIzin || $hasTextSakit || $hasTextIndex || $hasTextHuruf;

                    // D. Eksekusi Filter Berdasarkan Kategori Teks Pendamping
                    if ($hasAnyCategoryText) {
                        if ($hasTextMasuk) {
                            return empty($searchNumbers) ? ($user->mhs_masuk > 0) : $anyNumberMatches($user->mhs_masuk);
                        }
                        if ($hasTextTidakMasuk) {
                            return empty($searchNumbers) ? ($user->mhs_tidak_masuk > 0) : $anyNumberMatches($user->mhs_tidak_masuk);
                        }
                        if ($hasTextDispensasi) {
                            return empty($searchNumbers) ? ($user->mhs_dispensasi > 0) : $anyNumberMatches($user->mhs_dispensasi);
                        }
                        if ($hasTextTerlambat) {
                            return empty($searchNumbers) ? ($user->mhs_terlambat > 0) : $anyNumberMatches($user->mhs_terlambat);
                        }
                        if ($hasTextIzin) {
                            return empty($searchNumbers) ? ($user->mhs_izin > 0) : $anyNumberMatches($user->mhs_izin);
                        }
                        if ($hasTextSakit) {
                            return empty($searchNumbers) ? ($user->mhs_sakit > 0) : $anyNumberMatches($user->mhs_sakit);
                        }
                        if ($hasTextIndex) {
                            return empty($searchNumbers) ? true : $anyNumberMatches($user->mhs_nilai_index);
                        }
                        if ($hasTextHuruf) {
                            return ! empty($userHuruf);
                        }
                    }

                    // E. Jika Dosen Hanya Mengetik Angka Murni atau Nilai Persentase Absen
                    if (empty($searchCleanText) || $searchCleanText === 'persen') {
                        if ($anyNumberMatches($user->mhs_nilai_akhir)) {
                            return true;
                        }

                        $poinMhs = round((($user->mhs_poin_absensi ?? 0) / (2 * $totalSesiKelas)) * 100, 2);
                        if ($anyNumberMatches($poinMhs)) {
                            return true;
                        }

                        return $anyNumberMatches($user->mhs_nilai_index)
                            || $anyNumberMatches($user->mhs_masuk)
                            || $anyNumberMatches($user->mhs_dispensasi)
                            || $anyNumberMatches($user->mhs_terlambat)
                            || $anyNumberMatches($user->mhs_izin)
                            || $anyNumberMatches($user->mhs_sakit)
                            || $anyNumberMatches($user->mhs_tidak_masuk)
                            || $anyNumberMatches($user->mhs_absensi);
                    }

                    return false;
                });
            }

            // ==========================
            // SORTING & PAGINATION
            // ==========================
            $fieldToSort = in_array($this->sortField, $accessorFields) ? $this->sortField : 'id';

            $sortedUsers = $this->sortDirection === 'asc'
                ? $allUsers->sortBy(fn ($user) => $user->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE)
                : $allUsers->sortByDesc(fn ($user) => $user->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE);

            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $sortedUsers->forPage($currentPage, $this->perPage)->values(),
                $sortedUsers->count(),
                $this->perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return $queryUser->paginate($this->perPage);
    }

    // public function searchOutputMahasiswa($queryUser, $idJadwal)
    // {
    //     $accessorFields = [
    //         'mhs_absensi',
    //         'mhs_masuk',
    //         'mhs_hadir',
    //         'mhs_terlambat',
    //         'mhs_izin',
    //         'mhs_sakit',
    //         'mhs_dispensasi',
    //         'mhs_absen',
    //         'mhs_poin_absensi',
    //         'mhs_nilai_akhir',
    //         'mhs_nilai_index',
    //         'mhs_nilai_huruf',
    //     ];

    //     $search = trim($this->search);
    //     $searchClean = preg_replace('/[^A-Za-z0-9.,]/', '', $search);

    //     // ==========================
    //     // HELPER SEARCH NUMERIC
    //     // ==========================
    //     $normalizeSearch = function ($value) {
    //         return strtolower(trim(str_replace(',', '.', $value)));
    //     };

    //     $matchesNumeric = function ($value, $search) use ($normalizeSearch) {
    //         if ($value === null || $search === '') {
    //             return false;
    //         }

    //         $valueString = $normalizeSearch(number_format((float) $value, 2, '.', ''));
    //         $searchString = $normalizeSearch($search);
    //         $searchString = str_replace(['%', 'persen'], '', $searchString);
    //         $searchString = trim($searchString);

    //         return str_starts_with($valueString, $searchString);
    //     };

    //     if (! empty($search) || in_array($this->sortField, $accessorFields)) {

    //         // 1. Ambil ID dari database yang lolos pencarian teks standar (Nama/NIM)
    //         // Kita clone $queryUser SEBELUM diutak-atik agar aman.
    //         $dbMatchedIds = (! empty($search) && ! str_contains($search, '%'))
    //             ? (clone $queryUser)->pluck('users.id')->toArray()
    //             : [];

    //         // 2. Ambil seluruh data master user untuk jadwal terkait (Sama seperti pola $allSesi)
    //         // Kita panggil method pencarian dasar Anda tanpa kriteria filter angka dari switch utama
    //         $allUsers = $this->inputUserSearch('mahasiswa', $idJadwal)->get();

    //         // 3. Lakukan filter koleksi di memori PHP
    //         if (! empty($search)) {
    //             $allUsers = $allUsers->filter(function ($user) use ($search, $searchClean, $matchesNumeric, $dbMatchedIds) {

    //                 // Cek apakah data ini lolos pencarian teks database (Nama/NIM)
    //                 $matchTextBiasa = in_array($user->id, $dbMatchedIds);

    //                 // Cek kolom kustom (Subquery / Accessor)
    //                 $matchNilaiAkhir = $matchesNumeric($user->mhs_nilai_akhir, $search);
    //                 $matchPoinAbsensi = $matchesNumeric($user->mhs_poin_absensi, $search);

    //                 $matchNilaiHuruf = ! empty($searchClean) && str_contains(
    //                     strtolower((string) $user->mhs_nilai_huruf),
    //                     strtolower($searchClean)
    //                 );

    //                 // Cek seluruh filter angka kehadiran kustom
    //                 $matchAbsensi = $matchesNumeric($user->mhs_absensi, $search)
    //                     || $matchesNumeric($user->mhs_masuk, $search)
    //                     || $matchesNumeric($user->mhs_hadir, $search)
    //                     || $matchesNumeric($user->mhs_terlambat, $search)
    //                     || $matchesNumeric($user->mhs_izin, $search)
    //                     || $matchesNumeric($user->mhs_sakit, $search)
    //                     || $matchesNumeric($user->mhs_dispensasi, $search)
    //                     || $matchesNumeric($user->mhs_absen, $search);

    //                 return $matchTextBiasa
    //                     || $matchNilaiAkhir
    //                     || $matchPoinAbsensi
    //                     || $matchNilaiHuruf
    //                     || $matchAbsensi;
    //             });
    //         }

    //         // ==========================
    //         // SORTING
    //         // ==========================
    //         $fieldToSort = in_array($this->sortField, $accessorFields) ? $this->sortField : 'id';

    //         $sortedUsers = $this->sortDirection === 'asc'
    //             ? $allUsers->sortBy(fn ($user) => $user->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE)
    //             : $allUsers->sortByDesc(fn ($user) => $user->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE);

    //         // ==========================
    //         // PAGINATION
    //         // ==========================
    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;

    //         return new LengthAwarePaginator(
    //             $sortedUsers->forPage($currentPage, $this->perPage)->values(),
    //             $sortedUsers->count(),
    //             $this->perPage,
    //             $currentPage,
    //             ['path' => Paginator::resolveCurrentPath()]
    //         );
    //     }

    //     return $queryUser->paginate($this->perPage);
    // }

    // public function searchOutputMahasiswa($queryUser, $idJadwal, $expiredCount)
    // {
    //     $accessorFields = [
    //         'mhs_absensi', 'mhs_masuk', 'mhs_hadir', 'mhs_terlambat',
    //         'mhs_izin', 'mhs_sakit', 'mhs_dispensasi', 'mhs_absen',
    //         'mhs_tidak_masuk', 'mhs_poin_absensi', 'mhs_nilai_akhir',
    //         'mhs_nilai_index', 'mhs_nilai_huruf',
    //     ];

    //     $search = trim($this->search);
    //     $cleanNumber = preg_replace('/[^0-9.]/', '', $search);
    //     $isNumericQuery = is_numeric($cleanNumber) && $cleanNumber !== '';
    //     $isPercentSearch = str_contains($search, '%');

    //     // 1. KONDISI A: Jika pencarian mengandung '%', atau murni angka kecil (Kriteria jumlah absen/nilai)
    //     // Dan pastikan panjang angkanya masuk akal untuk jumlah absen/nilai (misal di bawah 4 digit)
    //     if (! empty($search) && ($isPercentSearch || ($isNumericQuery && strlen($cleanNumber) <= 3)) || in_array($this->sortField, $accessorFields)) {

    //         // Ambil data master mentah mahasiswa yang ada di jadwal ini tanpa filter teks keyword/having dulu
    //         // Kita panggil manual bypass agar queryBuilder-nya tidak rusak oleh where 'like' teks nim/nama
    //         $allUsers = User::query()
    //             ->with(['mahasiswa', 'mahasiswa.pr_rel', 'mahasiswa.pr_rel.dp_rel', 'mahasiswa.pr_rel.dp_rel.fk_rel'])
    //             ->whereHas('mahasiswa.jadwals', function ($q) use ($idJadwal) {
    //                 $q->where('kj_id', $idJadwal);
    //             });

    //         if ($this->showDeleted && $this->AuthCheck('staff')) {
    //             $allUsers->onlyTrashed();
    //         }

    //         // Terapkan semua SelectSub Nilai & Absensi ke instance $allUsers ini
    //         $this->applySubqueriesToUsers($allUsers, $idJadwal, $expiredCount);

    //         $usersCollection = $allUsers->get();

    //         // Filter koleksi menggunakan PHP memory jika mencari kriteria angka/persentase absen
    //         if (! empty($search)) {
    //             $usersCollection = $usersCollection->filter(function ($user) use ($cleanNumber, $isPercentSearch, $idJadwal) {
    //                 if ($isPercentSearch) {
    //                     $totalSesi = KelasSesi::where('kj_id', $idJadwal)->count() ?: 1;
    //                     $percent = (($user->mhs_poin_absensi) / (2 * $totalSesi)) * 100;

    //                     return str_contains(round($percent, 2), $cleanNumber);
    //                 }

    //                 return (int) $user->mhs_absensi === (int) $cleanNumber
    //                     || (int) $user->mhs_masuk === (int) $cleanNumber
    //                     || (int) $user->mhs_hadir === (int) $cleanNumber
    //                     || (int) $user->mhs_terlambat === (int) $cleanNumber
    //                     || (int) $user->mhs_izin === (int) $cleanNumber
    //                     || (int) $user->mhs_sakit === (int) $cleanNumber
    //                     || (int) $user->mhs_dispensasi === (int) $cleanNumber
    //                     || (int) $user->mhs_absen === (int) $cleanNumber
    //                     || (int) $user->mhs_tidak_masuk === (int) $cleanNumber
    //                     || (int) $user->mhs_poin_absensi === (int) $cleanNumber
    //                     || str_contains((string) $user->mhs_nilai_akhir, $cleanNumber);
    //             });
    //         }

    //         // Jalankan Sorting Collection
    //         $fieldToSort = in_array($this->sortField, $accessorFields) ? $this->sortField : 'id';
    //         $sortedUsers = $this->sortDirection === 'asc'
    //             ? $usersCollection->sortBy(fn ($u) => $u->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE)
    //             : $usersCollection->sortByDesc(fn ($u) => $u->{$fieldToSort}, SORT_NATURAL | SORT_FLAG_CASE);

    //         $currentPage = Paginator::resolveCurrentPage() ?: 1;

    //         return new LengthAwarePaginator(
    //             $sortedUsers->forPage($currentPage, $this->perPage)->values(),
    //             $sortedUsers->count(),
    //             $this->perPage,
    //             $currentPage,
    //             ['path' => Paginator::resolveCurrentPath()]
    //         );
    //     }

    //     // 2. KONDISI B: Pencarian Teks biasa (Nama, NIM, Angkatan) -> Eksekusi via database murni (Sangat Cepat & Aman)
    //     return $queryUser->paginate($this->perPage);
    // }

    // Helper untuk menyuntikkan selectSub agar kode searchOutputMahasiswa tetap rapi dan terbaca
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

        // $queryUser->selectSub(function ($query) use ($idJadwal) {
        //     $query->from('nilai_mahasiswa')->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
        //         ->whereColumn('mahasiswas.user_id', 'users.id')->where('nilai_mahasiswa.kj_id', $idJadwal)
        //         ->selectRaw('COALESCE(nilai_mahasiswa.nilai, 0)')->limit(1);
        // }, 'mhs_nilai_akhir');

        // $queryUser->selectSub(function ($query) use ($idJadwal) {
        //     $query->from('nilai_mahasiswa')->join('mahasiswas', 'nilai_mahasiswa.mahasiswa_id', '=', 'mahasiswas.id')
        //         ->whereColumn('mahasiswas.user_id', 'users.id')->where('nilai_mahasiswa.kj_id', $idJadwal)
        //         ->selectRaw("CASE WHEN nilai_mahasiswa.nilai >= 86 THEN 'A' WHEN nilai_mahasiswa.nilai >= 71 THEN 'B' WHEN nilai_mahasiswa.nilai >= 56 THEN 'C' WHEN nilai_mahasiswa.nilai >= 41 THEN 'D' ELSE 'E' END")->limit(1);
        // }, 'mhs_nilai_huruf');

        // foreach ($statuses as $alias => $condition) {
        //     $queryUser->selectSub(function ($query) use ($idJadwal, $alias, $condition) {
        //         $rawSql = ($alias === 'mhs_poin_absensi') ? "COALESCE(SUM($condition), 0)" : "COALESCE(SUM(CASE WHEN $condition THEN 1 ELSE 0 END), 0)";
        //         $query->selectRaw($rawSql)->from('mahasiswa_kehadiran')->join('kelas_sesi', 'mahasiswa_kehadiran.sesi_id', '=', 'kelas_sesi.id')->join('mahasiswas', 'mahasiswa_kehadiran.mahasiswa_id', '=', 'mahasiswas.id')->whereColumn('mahasiswas.user_id', 'users.id')->where('kelas_sesi.kj_id', $idJadwal);
        //     }, $alias);
        // }

        $queryUser->selectRaw("GREATEST(0, ? - ((SELECT COALESCE(SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Terlambat' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Izin' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?) + (SELECT COALESCE(SUM(CASE WHEN status='Dispensasi' THEN 1 ELSE 0 END),0) FROM mahasiswa_kehadiran JOIN kelas_sesi ON sesi_id=kelas_sesi.id JOIN mahasiswas ON mahasiswa_id=mahasiswas.id WHERE user_id=users.id AND kj_id=?))) as mhs_tidak_masuk", [$expiredCount, $idJadwal, $idJadwal, $idJadwal, $idJadwal, $idJadwal]);
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

    // public function sortFieldOrderUser($queryUser)
    // {
    //     $profileFields = [
    //         'role', 'admin_id', 'dosen_id', 'mahasiswa_id',
    //         'name', 'identity1', 'identity2', 'identity3', 'nik',
    //         'prodi', 'status', 'angkatan', 'kode', 'pertemuan_ke',
    //         'nip', 'nitk', 'nidn', 'nidk', 'nim',
    //     ];

    //     if (in_array($this->sortField, $profileFields)) {
    //         return $this->applyUserCombinedSort($queryUser);
    //     }

    //     $field = ($this->sortField === 'id') ? 'users.id' : $this->sortField;

    //     return $queryUser->orderBy($field, $this->sortDirection);
    // }

    // private function applyUserCombinedSort($queryUser)
    // {
    //     $queryUser->leftJoin('admins', 'users.id', '=', 'admins.user_id')
    //         ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
    //         ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
    //         ->select('users.*');

    //     if ($this->sortField === 'prodi') {
    //         return $this->applyProdiSort($queryUser->leftJoin('prodis as ap', 'admins.pr_id', '=', 'ap.id')
    //             ->leftJoin('prodis as dp', 'dosens.pr_id', '=', 'dp.id')
    //             ->leftJoin('prodis as mp', 'mahasiswas.pr_id', '=', 'mp.id'),
    //             'COALESCE(ap.strata, dp.strata, mp.strata)',
    //             'COALESCE(ap.nama_pr, dp.nama_pr, mp.nama_pr)');
    //     }

    //     // if (Auth::user()->mahasiswa) {
    //     //     $sort = $this->sortField;
    //     //     if ($sort == 'mhs_poin_absensi' || $sort == 'mhs_absensi' || $sort == 'mhs_masuk' || $sort == 'mhs_hadir' || $sort == 'mhs_terlambat' || $sort == 'mhs_izin' || $sort == 'mhs_sakit' || $sort == 'mhs_dispensasi' || $sort == 'mhs_tidak_masuk' || $sort == 'mhs_absen' || $sort == 'mhs_poin_absensi') {
    //     //         $this->sortField == 'id';
    //     //     }
    //     // }

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
    //         'pertemuan_ke' => 'mahasiswas.nim',
    //         'nik' => 'COALESCE(admins.nik, dosens.nik, mahasiswas.nik)',
    //         'status' => 'COALESCE(admins.status, dosens.status, mahasiswas.status)',
    //         'angkatan' => 'mahasiswas.angkatan',
    //         'created_at' => 'users.created_at',
    //         'updated_at' => 'users.updated_at',
    //         default => 'users.id'
    //     };

    //     return $queryUser->orderByRaw("$orderByRaw {$this->sortDirection}");
    // }
}
