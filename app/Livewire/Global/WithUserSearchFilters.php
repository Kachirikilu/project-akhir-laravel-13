<?php

namespace App\Livewire\Global;

use App\Models\Auth\User;
use App\Models\Kelas\KelasJadwal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

trait WithUserSearchFilters
{
    use LogicSearch;
    use WithPagination;

    public $userSearchQuery = '';

    public $userSearchResults = [];

    public $modeUser = '';

    public $user_id;

    public $user_name = '';

    public $user_items = [];

    public $userNameSearch = '';

    public $userResults = [];

    public $selectedUserId = null;

    // Properti Array untuk Multiple Selection jika dibutuhkan
    public $user_id_array = [];

    public $user_items_array = [];

    // Properti User Pengajar
    // public $is_ketua_user = ''; // ID User yang sebagai ketua
    public $peran_user = [];

    public $pertemuan_user = [];

    private function mapUser($collection)
    {
        return $collection->map(fn ($u) => [
            'id' => $u->id,
            'kode' => $u->nik,
            'name' => $u->name,
            'role' => $u->role ?? null,
            'prodi' => $u->pr_rel?->prodi,
            'fakultas' => $u->pr_rel?->fakultasFk,
            'status' => $u->status,
        ])->toArray();
    }

    private function mapUserSearch($collection)
    {
        return $collection->map(fn ($u) => [
            'id' => $u->id,
            'kode' => $u->nik,
            'name' => $u->name,
            'role' => $u->role ?? null,
            'prodi' => $u->pr_rel?->prodi,
            'departemen' => $u->pr_rel?->departemenDp,
            'fakultas' => $u->pr_rel?->fakultasFk,
            'kode_pr' => $u->pr_rel?->kode,
            'status' => $u->status,
        ])->toArray();
    }

    private function userQuery()
    {
        return User::query()->with('user');
    }

    private function itemsUser($u)
    {
        if (! $u) {
            return null;
        }

        return [
            'id' => $u->id,
            'kode' => $u->nik,
            'slot1' => $u->name,
            'slot2' => $u->role ?? null,
            'slot3' => $u->status,
            'slot4' => $u->prodi,
        ];
    }

    public function inputUserFilter()
    {
        $search = trim($this->userSearchQuery);

        // Jika ada input search
        if ((strlen($search) > 1 || is_numeric($search)) && ($search !== $this->user_name)) {
            $this->userSearchResults = $this->mapUserSearch(
                $this->userQuery()->searchUser($search)->limit(12)->get()
                // $this->searchOutputUser($this->userQuery(), $search, null, 12)
            );
        } elseif (empty($search) || $this->user_name) {
            $this->userSearchResults = $this->getUserbyUser('search');
        } else {
            $this->userSearchResults = [];
        }
    }

    public function resetUserFilter()
    {
        $this->reset(['selectedUserId', 'userSearchQuery', 'user_name', 'user_items']);
        $this->resetPage();
    }

    public function selectUserForFilter($id)
    {
        $uata = $this->userQuery()->find($id);

        if ($uata) {
            $this->selectedUserId = $id;
            $this->user_name = $uata->name;
            $this->userSearchQuery = $uata->name;
            $this->user_items = $this->itemsUser($uata);
            $this->userSearchResults = [];
            $this->resetPage();
        }
    }

    public function updatedUserNameSearch($value)
    {
        $this->user_id = null;
        $this->user_items = null;
        $this->resetErrorBag(['user_id', 'userNameSearch']);

        $query = $this->userQuery();

        if (trim(strlen($value)) > 0) {
            $results = $query->searchUser($value)->limit(12)->get();
            // $results = $this->searchOutputUser($query, $value, null, 12);
            $this->userResults = $this->mapUser($results);

            $normalizedValue = str_replace(['-', ' '], '', strtolower($value));
            $exactMatch = $results->first(function ($u) use ($value, $normalizedValue) {
                $normalizedUserNIK = str_replace(['-', ' '], '', strtolower($u->nik));
                $normalizedUserID1 = str_replace(['-', ' '], '', strtolower($u->identity1));
                $normalizedUserID2 = str_replace(['-', ' '], '', strtolower($u->identity2));
                $normalizedUserID3 = str_replace(['-', ' '], '', strtolower($u->identity3));

                return strtolower($u->name) === strtolower($value)
                    || strtolower($u->user->email) === strtolower($value)
                    || $normalizedUserNIK === $normalizedValue
                    || $normalizedUserID1 === $normalizedValue
                    || $normalizedUserID2 === $normalizedValue
                    || $normalizedUserID3 === $normalizedValue;
            });

            if ($exactMatch) {
                if ($this->modeUser == 'single') {
                    $this->userNameSearch = $exactMatch->name;
                    $this->user_id = $exactMatch->id;
                    $this->user_items = $this->itemsUser($exactMatch);
                } else {
                    $this->userNameSearch = '';
                    $this->user_id_array[] = $exactMatch->id;
                    $this->user_items_array[] = $this->itemsUser($exactMatch);

                    $this->user_id_array = collect($this->user_id_array)
                        ->unique()
                        ->values()
                        ->all();
                    $this->user_items_array = collect($this->user_items_array)
                        ->unique('id')
                        ->values()
                        ->all();
                }
                $this->userResults = $this->getUserbyUser();
            }
        } else {
            if (Auth::user()->pr_id) {
                $this->userResults = $this->getUserbyUser();
            } else {
                $this->userResults = $this->mapUser(
                    $query->limit(12)->get()
                );
            }
        }
    }

    public function getUserbyUser($mode = 'complex')
    {
        $user = Auth::user();
        $userodiId = $user->pr_id ?? null;

        $query = $this->userQuery();

        if (! $userodiId) {
            $uefaultUser = $query
                ->latest()
                ->limit(12)
                ->get();

            return $mode === 'search'
                ? $this->mapUserSearch($uefaultUser)
                : $this->mapUser($uefaultUser);
        }

        $mainResults = $query
            ->whereHas('pr_rel', function ($q) use ($userodiId) {
                $q->where('prodis.id', $userodiId);
            })
            ->limit(12)
            ->get();

        if ($mainResults->count() < 12) {
            $extra = User::whereNotIn('id', $mainResults->pluck('id'))
                ->limit(12 - $mainResults->count())
                ->get();

            $mainResults = $mainResults->concat($extra);
        }

        return $mode === 'search'
            ? $this->mapUserSearch($mainResults)
            : $this->mapUser($mainResults);
    }

    public function fetchUser($query = '', $mode = 'single')
    {
        $this->modeUser = $mode;
        if (empty($query) || $this->user_id) {
            $this->userResults = $this->getUserbyUser();
        }

    }

    public function selectUser($id, $userName)
    {
        $this->user_id = $id;
        $this->userNameSearch = $userName;
        $this->userResults = $this->getUserbyUser();

        $uata = $this->userQuery()->find($id);
        if ($uata) {
            $this->user_items = $this->itemsUser($uata);
        }

        if (method_exists($this, 'fetchUser')) {
            $this->fetchUser('');
        }

        $this->resetErrorBag(['user_id', 'userNameSearch']);
    }

    public function selectUserArray($id)
    {
        $uata = $this->userQuery()->find($id);
        if ($uata && ! in_array($id, $this->user_id_array)) {
            $this->user_id_array[] = $id;
            $this->user_items_array[] = $this->itemsUser($uata);
        }
    }

    public function resetUserInput()
    {
        $this->reset(['user_id', 'user_items', 'userNameSearch']);
        $this->userResults = $this->getUserbyUser();
    }

    public function resetUserArray()
    {
        $this->user_id_array = [];
        $this->user_items_array = [];
        $this->userNameSearch = '';
    }

    public function searchOutputUser($queryUser, $searchRaw, $searchAngkatan, $perPage, $sortField = null, $sortDirection = 'asc', $idJadwal = null, $withRPS = false)
    {
        $search = trim($searchRaw);
        $searchLower = strtolower($search);
        $searchAngkatan = strtolower(trim($searchAngkatan));
        $searchClean = preg_replace('/[^A-Za-z0-9]/', '', $search);

        if (! empty($search) || ! empty($searchAngkatan) || $sortField) {

            $allUser = (clone $queryUser)->get();

            if (! empty($search) || ! empty($searchAngkatan)) {

                $mode = $this->detectSearchMode($searchLower);

                $allUser = $allUser->filter(function ($user) use ($searchLower, $searchAngkatan, $mode, $idJadwal, $withRPS) {
                    // $number = preg_replace('/[^0-9.]/', '', $searchLower);
                    // $isNumericSearch = is_numeric($number) && $number !== '';
                    // $numberBobot = preg_replace('/[^0-9.]/', '', $searchAngkatan);
                    // $isNumericBobot = is_numeric($numberBobot) && $numberBobot !== '';

                    $matchID = $this->matchID(
                        $user->id,
                        $searchLower
                    );

                    $matchIDRole = $this->matchID(
                        $user->role_id,
                        $searchLower
                    );

                    $matchRole = $this->containsStrict(
                        $user->role,
                        $searchLower
                    );

                    $matchName = $this->containsStrict(
                        $user->name,
                        $searchLower
                    );
                    $matchEmail = $this->containsStrict(
                        $user->email,
                        $searchLower
                    );
                    $matchStatus = $this->containsStrict(
                        $user->status,
                        $searchLower
                    );
                    $matchKampus = $this->containsStrict(
                        'Kampus '.$user->kode_wilayah,
                        $searchLower
                    ) || $this->containsStrict(
                        'Kampus '.$user->wilayah,
                        $searchLower
                    );

                    $matchIdentity1 = $this->matchOnlyCount(
                        $user->identity1,
                        $searchLower, ['nip', 'nim', 'id1', 'identity1']
                    ) || $this->containsStrict(
                        $user->identity1,
                        $searchLower
                    );
                    $matchIdentity2 = $this->matchOnlyCount(
                        $user->identity2,
                        $searchLower, ['nitk', 'nidn', 'id2', 'identity2']
                    ) || $this->containsStrict(
                        $user->identity2,
                        $searchLower
                    );
                    $matchIdentity3 = $this->matchOnlyCount(
                        $user->identity3 ?? null,
                        $searchLower, ['nidk', 'id3', 'identity3']
                    ) || $this->containsStrict(
                        $user->nidk,
                        $searchLower
                    );
                    $matchNIK = $this->matchOnlyCount(
                        $user->nik,
                        $searchLower, ['nik']
                    ) || $this->containsStrict(
                        $user->nik,
                        $searchLower
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL BOBOT
                    |--------------------------------------------------------------------------
                    */
                    // dump($searchAngkatan, $searchLower);
                    $matchAngkatan = false;
                    $matchAngkatan = $this->matchOnlyCount(
                        $user->mahasiswa->angkatan ?? null,
                        $searchLower, ['angkatan', 'angk', 'angkatan', 'tahun', 'thn']
                    );
                    if (! empty($searchAngkatan)) {
                        $matchAngkatan = $this->matchOnlyCount(
                            $user->mahasiswa->angkatan ?? null,
                            $searchAngkatan, ['angkatan', 'angk', 'angkatan', 'tahun', 'thn']
                        );
                    }

  
                    $searchNormalized = $this->normalizePhoneNumber($searchLower);
                    $userHPNormalized = $this->normalizePhoneNumber($user->no_hp);
                    $userWANormalized = $this->normalizePhoneNumber($user->no_wa);

                    $matchHP = ($searchNormalized !== '' && (
                        str_contains($userHPNormalized, $searchNormalized) || 
                        str_contains($userWANormalized, $searchNormalized)
                    ));
                    $matchGender = $this->containsStrict(
                        'Gender '.$user->gender,
                        $searchLower
                    );
                    $matchAgama = $this->containsStrict(
                        'Agama '.$user->agama,
                        $searchLower
                    );

                  $matchKodePr = $this->matchKode(
                        $user->kode_pr,
                        $searchLower
                    );
                    $matchKodeDp = $this->matchKode(
                        $user->kode_dp,
                        $searchLower
                    );
                    $matchKodeFk = $this->matchKode(
                        $user->kode_fk,
                        $searchLower
                    );

                    $basePr = [
                        $user->prodi,
                        $user->prodi_pr,
                        $user->prodi_strata,
                    ];
                    $matchPr = false;
                    foreach ($basePr as $pr) {
                        $candidates = [
                            $pr.' '.$user->kode_pr,
                            $pr.' ('.$user->kode_pr.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchPr = true;
                                break 2;
                            }
                        }
                    }

                    $baseDp = [
                        $user->departemen,
                        $user->departemen_dp,
                    ];
                    $matchDp = false;
                    foreach ($baseDp as $dp) {
                        $candidates = [
                            $dp.' '.$user->kode_dp,
                            $dp.' ('.$user->kode_dp.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchDp = true;
                                break 2;
                            }
                        }
                    }

                    $baseFk = [
                        $user->fakultas,
                        $user->fakultas_fk,
                    ];
                    $matchFk = false;
                    foreach ($baseFk as $fk) {
                        $candidates = [
                            $fk.' '.$user->kode_fk,
                            $fk.' ('.$user->kode_fk.')',
                        ];
                        foreach ($candidates as $candidate) {
                            if ($this->containsStrict($candidate, $searchLower)) {
                                $matchFk = true;
                                break 2;
                            }
                        }
                    }

                    $matchPoin = false;
                    $matchMasuk = false;
                    $matchDispensi = false;
                    $matchTerlambat = false;
                    $matchIzin = false;
                    $matchSakit = false;
                    $matchTidakMasuk = false;

                    $matchNilaiAkhirKelas = false;
                    $matchNilaiIndexKelas = false;
                    $matchNilaiMutuKelas = false;

                    if ($idJadwal) {

                        $totalSesiKelas = KelasJadwal::find($idJadwal)?->count_sesi;

                        $mhsPoin = round(
                            (($user->mhs_poin_absensi ?? 0) / (2 * ($totalSesiKelas ?? 16))) * 100,
                            2,
                        );
                        $matchPoin = $this->containsStrict(
                            $mhsPoin,
                            $searchLower
                        );

                        $mhsMsk = $user->mhs_masuk;
                        $matchMasuk = $this->matchOnlyCount(
                            $mhsMsk ?? null,
                            $searchLower, ['hadir', 'hdr', 'hadi', 'masuk', 'msk', 'mas']
                        );

                        $mhsDis = $user->mhs_dispensasi;
                        $matchDispensi = $this->matchOnlyCount(
                            $mhsDis ?? null,
                            $searchLower, ['dispen', 'dispensi', 'dispensasi', 'dspn']
                        );

                        $mhsTrlmb = $user->mhs_terlambat;
                        $matchTerlambat = $this->matchOnlyCount(
                            $mhsTrlmb ?? null,
                            $searchLower, ['terlambat', 'lambat', 'lmbt', 'lmt', 'terlam', 'terl', 'lam']
                        );

                        $mhsIzn = $user->mhs_izin;
                        $matchIzin = $this->matchOnlyCount(
                            $mhsIzn ?? null,
                            $searchLower, ['izin', 'izn', 'izi']
                        );

                        $mhsSkt = $user->mhs_sakit;
                        $matchSakit = $this->matchOnlyCount(
                            $mhsSkt ?? null,
                            $searchLower, ['sakit', 'meninggal', 'skt', 'meninggoy', 'sak']
                        );

                        $mhsTdkMsk = $user->mhs_tidak_masuk;
                        $matchTidakMasuk = $this->matchOnlyCount(
                            $mhsTdkMsk ?? null,
                            $searchLower, ['tidak masuk', 'tidak msk', 'kabur', 'tdk msk', 'kbr']
                        );

                        $matchNilaiAkhirKelas = $this->matchNilaiAkhir(
                            $user->mhs_nilai_akhir ?? 0,
                            $searchLower
                        );

                        $matchNilaiIndexKelas = $this->matchNilaiIndex(
                            $user->mhs_nilai_index ?? 0,
                            $searchLower
                        );

                        $matchNilaiMutuKelas = $this->matchNilaiMutu(
                            $user->mhs_nilai_mutu ?? 'E',
                            $searchLower
                        );
                    }

                    $matchRPS = false;
                    $matchSKS = false;
                    $matchNilaiAkhir = false;
                    $matchNilaiIndex = false;
                    $matchNilaiMutu = false;

                    if ($withRPS) {
                        $rps = (int) ($user->count_rps ?? $user->mahasiswa->count_rps ?? 0);
                        $matchRPS = false;
                        if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                            $targetRPS = (int) max(
                                $matches[1] ?? 0,
                                $matches[2] ?? 0
                            );
                            $matchRPS = $rps === $targetRPS;
                        }
                        $matchRPS = $this->matchCount(
                            $rps,
                            $searchLower, ['rps']
                        ) || $this->containsStrict(
                            $rps.' RPS',
                            $searchLower
                        );

                        $sks = (int) ($user->total_sks ?? $user->mahasiswa->total_sks ?? 0);
                        $matchSKS = false;
                        if (preg_match('/(\d+)\s*sks|sks\s*(\d+)/i', $searchLower, $matches)) {
                            $targetSKS = (int) max(
                                $matches[1] ?? 0,
                                $matches[2] ?? 0
                            );
                            $matchSKS = $sks === $targetSKS;
                        }
                        $matchSKS = $this->matchCount(
                            $sks,
                            $searchLower, ['sks']
                        ) || $this->containsStrict(
                            $sks.' SKS',
                            $searchLower
                        );

                        $matchNilaiAkhir = $this->matchNilaiAkhir(
                            $user->mahasiswa->rekap_mhs ?? 0,
                            $searchLower
                        );

                        $matchNilaiIndex = $this->matchNilaiIndex(
                            $user->mahasiswa->ipk_mhs ?? 0,
                            $searchLower
                        );

                        $matchNilaiMutu = $this->matchNilaiMutu(
                            $user->mahasiswa->mutu_mhs ?? 'E',
                            $searchLower
                        );
                    }

                    $matchCreatedAt = $this->matchDateField(
                        $user->created_at,
                        $searchLower,
                        ['created', 'dibuat', 'create']
                    );

                    $matchUpdatedAt = $this->matchDateField(
                        $user->updated_at,
                        $searchLower,
                        ['updated', 'diubah', 'update']
                    );

                    switch ($mode) {
                        case 'id':
                            return $matchID || $matchIDRole;
                        case 'nilai':
                            return $matchNilaiAkhir || $matchNilaiMutu || $matchNilaiAkhirKelas || $matchNilaiMutuKelas;
                        case 'index':
                            return $matchNilaiIndex;
                        case 'mutu':
                            return $matchNilaiMutu;
                    }

                    return
                        $matchID
                        || $matchIDRole
                        || $matchRole
                        || $matchName
                        || $matchEmail
                        || $matchStatus
                        || $matchKampus

                        || $matchIdentity1
                        || $matchIdentity2
                        || $matchIdentity3
                        || $matchNIK

                        || $matchAngkatan

                        || $matchKodePr
                        || $matchKodeDp
                        || $matchKodeFk

                        || $matchPr
                        || $matchDp
                        || $matchFk

                        || $matchPoin
                        || $matchMasuk
                        || $matchDispensi
                        || $matchTerlambat
                        || $matchIzin
                        || $matchSakit
                        || $matchTidakMasuk

                        || $matchNilaiAkhirKelas
                        || $matchNilaiIndexKelas
                        || $matchNilaiMutuKelas

                        || $matchNilaiAkhir
                        || $matchNilaiIndex
                        || $matchNilaiMutu

                        || $matchRPS
                        || $matchSKS

                        || $matchHP || $matchGender || $matchAgama

                        || $matchCreatedAt
                        || $matchUpdatedAt;
                });
            }

            $sortValue = match ($sortField) {
                'admin_id', 'dosen_id', 'mahasiswa_id' => fn ($user) => $user->role_id,
                'role' => fn ($user) => $user->role,
                'name' => fn ($user) => $user->name,
                'email' => fn ($user) => $user->email,

                'identity1', 'kode' => fn ($user) => $user->identity1,
                'identity2' => fn ($user) => $user->identity2,
                'identity3' => fn ($user) => $user->identity3,
                'nip' => fn ($user) => $user->admin->nip ?? $user->dosen->nip ?? null,
                'nitk' => fn ($user) => $user->admin->nitk ?? null,
                'nidn' => fn ($user) => $user->dosen->nidn ?? null,
                'nidk' => fn ($user) => $user->dosen->nidk ?? null,
                'nim' => fn ($user) => $user->mahasiswa->nim ?? null,
                'nik' => fn ($user) => $user->nik ?? null,

                'angkatan' => fn ($user) => $user->mahasiswa->angkatan ?? null,
                'status' => fn ($user) => $user->status ?? null,
                'kampus' => fn ($user) => $user->kode_wilayah ?? null,
                'prodi', 'program_studi' => fn ($user) => $user->prodi ?? null,

                'mhs_poin_absensi' => fn ($user) => $user->mhs_poin_absensi ?? null,
                'mhs_masuk' => fn ($user) => $user->mhs_masuk ?? null,
                'mhs_dispensasi' => fn ($user) => $user->mhs_dispensasi ?? null,
                'mhs_terlambat' => fn ($user) => $user->mhs_terlambat ?? null,
                'mhs_izin' => fn ($user) => $user->mhs_izin ?? null,
                'mhs_sakit' => fn ($user) => $user->mhs_sakit ?? null,
                'mhs_tidak_masuk' => fn ($user) => $user->mhs_tidak_masuk ?? null,
                'mhs_nilai_akhir', 'mhs_nilai_index', 'mhs_nilai_mutu' => fn ($user) => $user->mahasiswa->mhs_nilai_akhir ?? 0,

                'count_rps' => fn ($user) => $user->count_rps ?? $user->mahasiswa->count_rps ?? null,
                'total_sks' => fn ($user) => $user->total_sks ?? $user->mahasiswa->total_sks ?? null,

                'created_at' => fn ($user) => $user->created_at,
                'updated_at' => fn ($user) => $user->updated_at,

                default => fn ($user) => $user->id,
            };

            $allUser = $sortDirection === 'asc'
                ? $allUser->sortBy($sortValue)
                : $allUser->sortByDesc($sortValue);

            if (empty($perPage)) {
                return $allUser->values();
            }
            $currentPage = Paginator::resolveCurrentPage() ?: 1;

            return new LengthAwarePaginator(
                $allUser->forPage($currentPage, $perPage)->values(),
                $allUser->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        if (empty($perPage)) {
            return $queryUser;
        }

        return $queryUser->paginate($perPage);
    }
}
