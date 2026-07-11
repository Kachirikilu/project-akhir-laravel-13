<?php

namespace App\Livewire\Global;

use App\Models\Akademik\CPL;
use App\Models\Akademik\CPMK;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Referensi;
use App\Models\Akademik\RPS;
use App\Models\Akademik\SubCPMK;
use App\Models\Akademik\TimDosen;
use App\Models\Auth\User;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait HasStats
{
    public int $cacheDurationMinutes = 60;

    private function flushObeCache()
    {
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys('stats_obe_pr_*');
            foreach ($keys as $key) {
                $redis->del($key);
            }
        }
    }

    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    private function getStatsObeProdi(bool $isTrash = false, $prodiId = null): array
    {
        // 1. Ambil versi saat ini (default ke 1 jika belum pernah diset)
        $version = Cache::get('stats_obe_version_pr', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $cacheKey = "stats_obe_pr_{$version}_".($prodiId ?? 'all').$suffix;

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prodiId) {
            $relations = [
                'mahasiswa' => 'mahasiswa.pr_rel',
                'rps' => 'mk_rel.prodis',
                'cpl' => 'prodis',
                'cpmk' => 'rps.mk_rel.prodis',
                'scpmk' => 'cpmks.rps.mk_rel.prodis',
            ];

            $applyFilter = function ($query, $modelKey) use ($isTrash, $prodiId, $relations) {
                if ($isTrash) {
                    $query->onlyTrashed();
                }
                if ($prodiId !== null) {
                    $query->whereHas($relations[$modelKey], fn ($q) => $q->where('prodis.id', $prodiId));
                }

                return $query;
            };

            return [
                'mahasiswa' => $applyFilter(User::query(), 'mahasiswa')->count(),
                'rps' => $applyFilter(RPS::query(), 'rps')->count(),
                'cpl' => $applyFilter(CPL::query(), 'cpl')->count(),
                'cpmk' => $applyFilter(CPMK::query(), 'cpmk')->count(),
                'scpmk' => $applyFilter(SubCPMK::query(), 'scpmk')->count(),
            ];
        });
    }

    public function clearObeProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('obe', $prodiId, 'OBE');
    }

    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    private function getStatsRpsProdi(bool $isTrash = false, $prodiId = null): array
    {
        $version = Cache::get('stats_rps_version_pr', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = $prodiId ?? (Auth::user()->pr_id ?? 'all');
        $userId = Auth::id();

        // 1. Definisikan semua Key dengan $version
        $keyGlobal = "stats_rps_global_pr_{$version}{$suffix}";
        $keyProdi = "stats_rps_prodi_pr_{$version}_{$prId}{$suffix}";
        $keyUser = "stats_rps_user_pr_{$version}_{$userId}{$suffix}";

        $currentYear = now()->year;
        $fiveYearsAgoYear = now()->subYears(5)->year;

        // 2. Global Stats
        $globalStats = Cache::remember($keyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $currentYear, $fiveYearsAgoYear, $prId) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            // Filter prodi jika perlu di sini (sesuai kebutuhan global Anda)
            if ($prId !== 'all') {
                $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $prId));
            }

            return [
                'rps-akademik' => (clone $queryRPS)->where('akademik', 'like', "%$currentYear%")->count(),
                'rps-rev-new' => (clone $queryRPS)->whereYear('revisi', $currentYear)->count(),
                'rps-aktif' => (clone $queryRPS)->where('is_draf', false)->count(),
                'rps-draf' => (clone $queryRPS)->where('is_draf', true)->count(),
                'rps-older-5' => (clone $queryRPS)->whereRaw('RIGHT(akademik, 4) < ?', [$fiveYearsAgoYear])->count(),
            ];
        });

        // 3. Prodi Stats (Sekarang menggunakan key yang menyertakan $version)
        $prodiStats = Cache::remember($keyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            return [
                'rps-prodi' => (clone $queryRPS)->whereHas('mk_rel.prodis', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
                'rps-prodi-aktif' => (clone $queryRPS)->whereHas('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
                'rps-prodi-non-aktif' => (clone $queryRPS)->whereHas('mk_rel.prodis', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->whereDoesntHave('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
            ];
        });

        // 4. User Stats
        $userStats = [];
        if (Auth::user()->dosen) {
            $userStats = Cache::remember($keyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryRPS = RPS::query();
                if ($isTrash) {
                    $queryRPS->onlyTrashed();
                }

                return [
                    'rps-saya' => (clone $queryRPS)->whereHas('tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId))->count(),
                ];
            });
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearRpsProdiStatsCache($prId = null)
    {
        $currentVersion = Cache::get('stats_rps_version', 1);
        Cache::forever('stats_rps_version_pr', $currentVersion + 1);
        Cache::forget('stats_rps_global_pr_'.$currentVersion.'_normal');
        Cache::forget('stats_rps_global_pr_'.$currentVersion.'_trash');
        if ($prId) {
            // $this->toast(text: 'Data statistik Rencana Pembelajaran Semester untuk Program Studi ini diperbarui!', type: 'info', variant: 'info');
        }
    }

    private function getStatsKurikulumProdi(string $prefix, bool $isTrash = false, $prodiId = null): array
    {
        $version = Cache::get("stats_{$prefix}_version", 1);
        $suffix = $isTrash ? '_trash' : '_normal';

        $cacheKey = "stats_{$prefix}_pr_{$version}_".($prodiId ?? 'all').$suffix;
        $modelMap = ['cpl' => CPL::class, 'cpmk' => CPMK::class, 'scpmk' => SubCPMK::class];
        $modelClass = $modelMap[$prefix];

        $relationMap = [
            'cpl' => 'prodis',
            'cpmk' => 'rps.mk_rel.prodis',
            'scpmk' => 'cpmks.rps.mk_rel.prodis',
        ];

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($modelClass, $prefix, $isTrash, $prodiId, $relationMap) {
            $query = $modelClass::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            if ($prodiId !== null) {
                $query->whereHas($relationMap[$prefix], fn ($q) => $q->where('prodis.id', $prodiId));
            }

            $now = now();
            $stats = [];
            $stats["{$prefix}-month"] = (clone $query)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
            $stats["{$prefix}-6-months"] = (clone $query)->where('created_at', '>=', $now->subMonths(6))->count();
            $stats["{$prefix}-year"] = (clone $query)->whereYear('created_at', $now->year)->count();
            $stats["{$prefix}-older-5"] = (clone $query)->where('created_at', '<', now()->subYears(5))->count();

            return $stats;
        });
    }

    public function clearCplProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('cpl', $prodiId, 'Capaian Pembelajaran Lulusan');
    }

    public function clearCpmkProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('cpmk', $prodiId, 'Capaian Pembelajaran Mata Kuliah');
    }

    public function clearScpmkProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('scpmk', $prodiId, 'Sub-CPMK');
    }
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS

    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    private function getStatsMahasiswaProdi(bool $isTrash = false, $prodiId = null): array
    {
        $version = Cache::get('stats_mhs_version_pr', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $cacheKey = "stats_mhs_pr_{$version}_".($prodiId ?? 'all').$suffix;

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prodiId) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }
            if ($prodiId !== null) {
                $query->whereHas('mahasiswa.pr_rel', fn ($q) => $q->where('prodis.id', $prodiId));
            }

            return [
                'mahasiswa-aktif' => $aktif = (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', 'aktif'))->count(),
                'mahasiswa-non-aktif' => $nonAktif = (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', 'non-aktif'))->count(),
                'mahasiswa-total' => $aktif + $nonAktif,
            ];
        });
    }

    public function clearMahasiswaProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('mhs', $prodiId, 'Mahasiswa');
    }

    private function clearCacheProdiStats($prefix, $prodiId, $label)
    {
        if ($prodiId !== null) {
            $version = Cache::get("stats_{$prefix}_version", 1);
            Cache::forget("stats_{$prefix}_pr_{$version}_{$prodiId}_normal");
            Cache::forget("stats_{$prefix}_pr_{$version}_{$prodiId}_trash");
            // $this->toast(text: "Data statistik $label untuk Program Studi ini diperbarui!", type: 'info', variant: 'info');
        } else {
            Cache::put("stats_{$prefix}_version", time());
        }
    }
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa

    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas
    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas
    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas
    private function getStatsKelas(bool $isTrash = false): array
    {
        $version = Cache::get('stats_kelas_version', 1);
        $prId = Auth::user()->pr_id ?? 'all';
        $suffix = $isTrash ? '_trash' : '_normal';
        $userId = Auth::id();

        // SEMUA KEY HARUS MEMAKAI $version
        $keyGlobal = "stats_kelas_global_{$version}{$suffix}";
        $keyProdi = "stats_kelas_pr_{$version}_{$prId}{$suffix}";
        $keyUser = "stats_kelas_user_{$userId}_{$version}{$suffix}";

        // 1. Global Stats
        $globalStats = Cache::remember($keyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryKelas = Kelas::query();
            if ($isTrash) {
                $queryKelas->onlyTrashed();
            }

            return [
                'kelas' => (clone $queryKelas)->count(),
                'kelas-tp' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('tipe_sks', 1))->count(),
                'kelas-pr' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('tipe_sks', 2))->count(),
                'kelas-pl' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('tipe_sks', 3))->count(),
                'kelas-sm' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('tipe_sks', 4))->count(),
                'kelas-wajib' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('is_wajib', true))->count(),
                'kelas-pilihan' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('is_wajib', false))->count(),

                'kelas-pr' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('level_mk', 1))->count(),
                'kelas-dp' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('level_mk', 2))->count(),
                'kelas-fk' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('level_mk', 3))->count(),
                'kelas-uni' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('level_mk', 4))->count(),
            ];
        });

        // 2. Prodi Stats
        $prodiStats = Cache::remember($keyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryKelas = Kelas::query();
            if ($isTrash) {
                $queryKelas->onlyTrashed();
            }
            if ($prId !== 'all') {
                $queryKelas->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $prId));
            }

            return ['kelas-prodi' => (clone $queryKelas)->count()];
        });

        // 3. User Stats
        $userStats = [];
        if (Auth::check()) {
            $userStats = Cache::remember($keyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $today = now()->format('Y-m-d');

                $queryKelas = Kelas::query();
                if ($isTrash) {
                    $queryKelas->onlyTrashed();
                }

                $queryJadwal = KelasJadwal::query()
                    ->whereHas('sesis', fn ($q) => $q->whereDate('tanggal', $today))
                    ->whereHas('kelas_rel', function ($q) use ($isTrash) {
                        if ($isTrash) {
                            $q->onlyTrashed();
                        }
                    });

                if (Auth::user()->dosen) {
                    $queryBase = (clone $queryKelas)->whereHas('rps_rel.tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId));
                    $queryJadwal->whereHas('kelas_rel.rps_rel.tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId));
                } elseif (Auth::user()->mahasiswa) {
                    $queryBase = (clone $queryKelas)->whereHas('jadwals.mahasiswas.user', fn ($q) => $q->where('users.id', $userId));
                    $queryJadwal->whereHas('mahasiswas.user', fn ($q) => $q->where('users.id', $userId));
                } else {
                    return [
                        'kelas-saya' => 0,
                        'jadwal-saya-hari-ini' => 0,
                        'jadwal-sks-saya-hari-ini' => 0,
                        'kelas-sks-saya' => 0,
                    ];
                }

                $totalSksHariIni = (clone $queryJadwal)
                    ->with('kelas_rel.rps_rel.mk_rel')
                    ->get()
                    ->sum(fn ($jadwal) => $jadwal->kelas_rel?->rps_rel?->mk_rel?->sks_kuliah ?? 0);

                $totalSksSaya = (clone $queryBase)
                    ->with('rps_rel.mk_rel')
                    ->get()
                    ->unique('rps_rel.mk_rel.id')
                    ->sum('rps_rel.mk_rel.sks_kuliah');

                return [
                    'kelas-saya' => (clone $queryBase)->count(),
                    'jadwal-saya-hari-ini' => (clone $queryJadwal)->count(),
                    'jadwal-sks-saya-hari-ini' => $totalSksHariIni,
                    'kelas-sks-saya' => $totalSksSaya,
                ];
            });
        }        $userStats = [];
        if (Auth::check()) {
            $userStats = Cache::remember($keyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $today = now()->format('Y-m-d');

                $queryKelas = Kelas::query();
                if ($isTrash) {
                    $queryKelas->onlyTrashed();
                }

                $queryJadwal = KelasJadwal::query()
                    ->whereHas('sesis', fn ($q) => $q->whereDate('tanggal', $today))
                    ->whereHas('kelas_rel', function ($q) use ($isTrash) {
                        if ($isTrash) {
                            $q->onlyTrashed();
                        }
                    });

                if (Auth::user()->dosen) {
                    $queryBase = (clone $queryKelas)->whereHas('rps_rel.tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId));
                    $queryJadwal->whereHas('kelas_rel.rps_rel.tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId));
                } elseif (Auth::user()->mahasiswa) {
                    $queryBase = (clone $queryKelas)->whereHas('jadwals.mahasiswas.user', fn ($q) => $q->where('users.id', $userId));
                    $queryJadwal->whereHas('mahasiswas.user', fn ($q) => $q->where('users.id', $userId));
                } else {
                    return ['kelas-saya' => 0, 'jadwal-saya-hari-ini' => 0, 'jadwal-sks-saya-hari-ini' => 0];
                }

                $totalSksHariIni = (clone $queryJadwal)
                    ->with('kelas_rel.rps_rel.mk_rel')
                    ->get()
                    ->sum(fn ($jadwal) => $jadwal->kelas_rel?->rps_rel?->mk_rel?->sks_kuliah ?? 0);

                return [
                    'kelas-saya' => (clone $queryBase)->count(),
                    'jadwal-saya-hari-ini' => (clone $queryJadwal)->count(),
                    'jadwal-sks-saya-hari-ini' => $totalSksHariIni,
                ];
            });
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearKelasStatsCache()
    {
        $currentVersion = Cache::get('stats_kelas_version', 1);
        Cache::forever('stats_kelas_version', $currentVersion + 1);

        // $this->toast(text: 'Data statistik Kelas telah diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas
    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas
    // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas // Statistik Kelas Kelas

    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE // Statistik OBE OBE OBE
    private function getStatsObe(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $globalStats = Cache::remember('stats_obe'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            return [
                'rps' => RPS::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'cpl' => CPL::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'cpmk' => CPMK::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'scpmk' => SubCPMK::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'ref' => Referensi::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'tim-dosen' => TimDosen::query()->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
                'dosen' => User::query()->whereHas('dosen')->when($isTrash, fn ($q) => $q->onlyTrashed())->count(),
            ];
        });

        return $globalStats;
    }

    public function clearObeStatsCache()
    {
        Cache::forget('stats_obe_normal');
        Cache::forget('stats_obe_trash');
        // $this->toast(text: 'Data statistik OBE diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    private function getStatsRps(bool $isTrash = false): array
    {
        $version = Cache::get('stats_rps_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';

        $user = Auth::user();
        $prId = $user ? ($user->pr_id ?? 'all') : 'all';
        $userId = Auth::id();

        $cacheKeyGlobal = "stats_rps_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_rps_prodi_{$version}_{$prId}{$suffix}";
        $cacheKeyUser = "stats_rps_user_{$version}_{$userId}{$suffix}";

        $currentYear = now()->year;
        $fiveYearsAgoYear = now()->subYears(5)->year;

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $currentYear, $fiveYearsAgoYear) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            return [
                'rps-akademik' => (clone $queryRPS)->where('akademik', 'like', "%$currentYear%")->count(),
                'rps-rev-new' => (clone $queryRPS)->whereYear('revisi', $currentYear)->count(),
                'rps-aktif' => (clone $queryRPS)->where('is_draf', false)->count(),
                'rps-draf' => (clone $queryRPS)->where('is_draf', true)->count(),
                'rps-older-5' => (clone $queryRPS)->whereRaw('RIGHT(akademik, 4) < ?', [$fiveYearsAgoYear])->count(),
            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            return [
                'rps-prodi' => (clone $queryRPS)->whereHas('tim_dosens.pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
                'rps-prodi-non-aktif' => (clone $queryRPS)->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $prId))
                    ->whereDoesntHave('tim_dosens.pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
            ];
        });

        $userStats = [];
        if (Auth::user()->dosen) {
            $userStats = Cache::remember($cacheKeyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryRPS = RPS::query();
                if ($isTrash) {
                    $queryRPS->onlyTrashed();
                }

                return [
                    'rps-saya' => (clone $queryRPS)->whereHas('tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId))->count(),
                    'rps-saya-aktif' => (clone $queryRPS)->where('is_draf', false)->whereHas('tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId))->count(),
                ];
            });
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearRpsStatsCache()
    {
        $currentVersion = Cache::get('stats_rps_version', 1);
        Cache::forever('stats_rps_version', $currentVersion + 1);

        // $this->toast(text: 'Data statistik Rencana Pembelajaran Semester diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL
    // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL
    private function getStatsKurikulum(string $prefix, bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';

        $modelMap = ['cpl' => CPL::class, 'cpmk' => CPMK::class, 'scpmk' => SubCPMK::class];
        $modelClass = $modelMap[$prefix];

        return Cache::remember("stats_{$prefix}_{$suffix}", now()->addMinutes($this->cacheDurationMinutes), function () use ($modelClass, $prefix, $isTrash) {
            $query = $modelClass::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            $now = now();
            $stats = [];
            $stats["{$prefix}-month"] = (clone $query)->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
            $stats["{$prefix}-6-months"] = (clone $query)->where('created_at', '>=', $now->subMonths(6))->count();
            $stats["{$prefix}-year"] = (clone $query)->whereYear('created_at', $now->year)->count();
            $stats["{$prefix}-older-5"] = (clone $query)->where('created_at', '<', now()->subYears(5))->count();

            return $stats;
        });
    }

    public function clearCplStatsCache()
    {
        Cache::forget('stats_cpl_normal');
        Cache::forget('stats_cpl_trash');
        // $this->toast(text: 'Data statistik Capaian Pembelajaran Lulusan diperbarui!', type: 'info', variant: 'info');
    }

    public function clearCpmkStatsCache()
    {
        Cache::forget('stats_cpmk_normal');
        Cache::forget('stats_cpmk_trash');
        // $this->toast(text: 'Data statistik Capaian Pembelajaran Mata Kuliah diperbarui!', type: 'info', variant: 'info');
    }

    public function clearScpmkStatsCache()
    {
        Cache::forget('stats_scpmk_normal');
        Cache::forget('stats_scpmk_trash');
        // $this->toast(text: 'Data statistik Sub-CPMK diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK
    // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK

    // Statistik Referensi // Statistik Referensi // Statistik Referensi // Statistik Referensi // Statistik Referensi
    // Statistik Referensi // Statistik Referensi // Statistik Referensi // Statistik Referensi // Statistik Referensi
    private function getStatsReferensi(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $currentYear = now()->year;

        return Cache::remember('stats_ref_'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $currentYear) {
            $query = Referensi::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'ref-year' => (clone $query)->where('tahun', $currentYear)->count(),
                'ref-2-3-years' => (clone $query)->whereBetween('tahun', [$currentYear - 3, $currentYear - 2])->count(),
                'ref-4-5-years' => (clone $query)->whereBetween('tahun', [$currentYear - 5, $currentYear - 4])->count(),
                'ref-6-10-years' => (clone $query)->whereBetween('tahun', [$currentYear - 10, $currentYear - 6])->count(),
                'ref-older-10' => (clone $query)->where('tahun', '<', $currentYear - 10)->count(),
            ];
        });
    }

    public function clearReferensiStatsCache()
    {
        Cache::forget('stats_ref_normal');
        Cache::forget('stats_ref_trash');
        // $this->toast(text: 'Data statistik Referensi diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen
    // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen
    private function getStatsTimDosen(bool $isTrash = false): array
    {
        $version = Cache::get('stats_tim_dosen_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';
        $userId = Auth::id();

        $cacheKeyGlobal = "stats_tim_dosen_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_tim_dosen_prodi_{$version}_{$prId}{$suffix}";
        $cacheKeyUser = "stats_tim_dosen_user_{$version}_{$userId}{$suffix}";

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
            $query = TimDosen::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'tim-dosen-rps' => (clone $query)->whereHas('rps')->count(),
                'tim-dosen-non-rps' => (clone $query)->whereDoesntHave('rps')->count(),
                'tim-dosen-all' => (clone $query)->count(),
                'tim-dosen-saya' => (clone $query)->whereHas('dosens', fn ($q) => $q->where('user_id', $userId))->count(),
                'tim-dosen-saya-aktif' => (clone $query)->whereHas('dosens', fn ($q) => $q->where('user_id', $userId))
                    ->whereHas('rps', fn ($q) => $q->where('is_draf', false))->count(),
                'tim-dosen-saya-ketua' => (clone $query)->whereHas('dosens', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('tim_dosen_pivot_dosen.is_ketua', true);
                })->count(),
                'tim-dosen-saya-ketua-aktif' => (clone $query)->whereHas('dosens', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('tim_dosen_pivot_dosen.is_ketua', true);
                })->whereHas('rps', fn ($q) => $q->where('is_draf', false))->count(),

            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $query = TimDosen::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'tim-dosen-prodi' => (clone $query)->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
            ];
        });

        $userStats = [];
        if (Auth::user()->dosen) {
            $userStats = Cache::remember($cacheKeyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $query = TimDosen::query();
                if ($isTrash) {
                    $query->onlyTrashed();
                }

                return [
                    'tim-dosen-saya' => (clone $query)->whereHas('dosens', fn ($q) => $q->where('user_id', $userId))->count(),
                ];
            });
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearTimDosenStatsCache()
    {
        $currentVersion = Cache::get('stats_tim_dosen_version', 1);
        Cache::forever('stats_tim_dosen_version', $currentVersion + 1);
        // $this->toast( ext: 'Data statistik Tim Dosen telah diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    private function getStatsDosen(bool $isTrash = false): array
    {
        // Mengambil versi saat ini. Jika belum ada, gunakan 1.
        $version = Cache::get('stats_dosen_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        // Key cache dengan menyertakan $version
        $cacheKeyGlobal = "stats_dosen_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_dosen_prodi_{$version}_{$prId}{$suffix}";

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'dosen-rps' => (clone $query)->whereHas('dosen.tim_dosens.rps')->count(),
                'dosen-non-rps' => (clone $query)->whereDoesntHave('dosen.tim_dosens.rps')->count(),
                'dosen-all' => (clone $query)->whereHas('dosen')->count(),
                'dosen-aktif' => (clone $query)->whereHas('dosen', fn ($q) => $q->where('status', 'aktif'))->count(),
                'dosen-non-aktif' => (clone $query)->whereHas('dosen', fn ($q) => $q->where('status', 'non-aktif'))->count(),
            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'dosen-prodi' => (clone $query)->whereHas('dosen.pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
                'dosen-prodi-aktif' => (clone $query)
                    ->whereHas('dosen', fn ($q) => $q->where('status', 'Aktif'))
                    ->whereHas('dosen.pr_rel', fn ($q) => $q->where('prodis.id', $prId))
                    ->count(),
            ];
        });

        return array_merge($globalStats, $prodiStats);
    }

    public function clearDosenStatsCache()
    {
        $currentVersion = Cache::get('stats_dosen_version', 1);
        Cache::forever('stats_dosen_version', $currentVersion + 1);
        // $this->toast(text: 'Data statistik Dosen telah diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen

    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    private function getStatsMahasiswa(bool $isTrash = false): array
    {
        // Mengambil versi saat ini. Jika belum ada, gunakan 1.
        $version = Cache::get('stats_mhs_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        // Key cache dengan menyertakan $version
        $cacheKeyGlobal = "stats_mhs_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_mhs_prodi_{$version}_{$prId}{$suffix}";

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'mahasiswa-opsi' => (clone $query)->whereHas('mahasiswa')->count(),
                'mahasiswa-aktif' => (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', 'aktif'))->count(),
                'mahasiswa-non-aktif' => (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', 'non-aktif'))->count(),
            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'mahasiswa-prodi' => (clone $query)->whereHas('mahasiswa.pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
            ];
        });

        return array_merge($globalStats, $prodiStats);
    }

    public function clearMahasiswaStatsCache()
    {
        $currentVersion = Cache::get('stats_mhs_version', 1);
        Cache::forever('stats_mhs_version', $currentVersion + 1);

        // $this->toast(text: 'Data statistik Mahasiswa telah diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa

    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    private function getStatsMk(bool $isTrash = false): array
    {
        // Mengambil versi saat ini. Jika belum ada, gunakan 1.
        $version = Cache::get('stats_mk_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';
        $userId = Auth::id();

        $cacheKeyGlobal = "stats_mk_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_mk_prodi_{$version}_{$prId}{$suffix}";
        $cacheKeyUser = "stats_mk_user_{$version}_{$userId}{$suffix}";

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryMK = MataKuliah::query();
            if ($isTrash) {
                $queryMK->onlyTrashed();
            }

            return [
                'mk' => (clone $queryMK)->count(),
                'mk-tp' => (clone $queryMK)->where('tipe_sks', 1)->count(),
                'mk-pr' => (clone $queryMK)->where('tipe_sks', 2)->count(),
                'mk-pl' => (clone $queryMK)->where('tipe_sks', 3)->count(),
                'mk-sm' => (clone $queryMK)->where('tipe_sks', 4)->count(),
            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryMK = MataKuliah::query();
            if ($isTrash) {
                $queryMK->onlyTrashed();
            }

            $tabQuery = clone $queryMK;

            return [
                'mk-prodi' => (clone $tabQuery)->whereHas('prodis', fn ($q) => $q->where('prodis.id', $prId))->count(),
                'mk-opsi' => (clone $tabQuery)->count(),
                'mk-wajib' => (clone $tabQuery)->where('is_wajib', true)->count(),
                'mk-pilihan' => (clone $tabQuery)->where('is_wajib', false)->count(),
                'mk-pr' => (clone $tabQuery)->where('level_mk', 1)->count(),
                'mk-dp' => (clone $tabQuery)->where('level_mk', 2)->count(),
                'mk-fk' => (clone $tabQuery)->where('level_mk', 3)->count(),
                'mk-uni' => (clone $tabQuery)->where('level_mk', 4)->count(),
            ];
        });

        $userStats = [];
        if (Auth::user()->dosen) {
            $userStats = Cache::remember($cacheKeyUser, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryMK = MataKuliah::whereHas('rps.tim_dosens.dosens.user', function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
                if ($isTrash) {
                    $queryMK->onlyTrashed();
                }
                $mkSaya = $queryMK->get();
                $currentMonth = now()->month;
                $isGanjil = ($currentMonth >= 9 || $currentMonth <= 2);
                $mkSemesterSaya = $mkSaya->filter(function ($mk) use ($isGanjil) {
                    return $isGanjil ? ($mk->semester % 2 != 0) : ($mk->semester % 2 == 0);
                });

                return [
                    'mk-saya' => $mkSaya->count(),
                    'mk-sks-saya' => $mkSaya->sum('sks'),
                    'mk-sks-semester-saya' => $mkSemesterSaya->sum('sks'),
                ];
            });
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearMkStatsCache()
    {
        $currentVersion = Cache::get('stats_mk_version', 1);
        Cache::forever('stats_mk_version', $currentVersion + 1);

        // $this->toast(text: 'Data statistik Mata Kuliah telah diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah

    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    private function getStatsProdi(bool $isTrash = false): array
    {
        $cacheKey = 'stats_prodi_'.($isTrash ? 'trash' : 'normal');

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryPr = Prodi::query();
            $queryDp = Departemen::query();
            $queryFk = Fakultas::query();

            if ($isTrash) {
                $queryPr->onlyTrashed();
                $queryDp->onlyTrashed();
                $queryFk->onlyTrashed();
            }

            return [
                'prodi' => (clone $queryPr)->count(),
                'sarjana' => (clone $queryPr)->where('strata', 'Sarjana')->count(),
                'magister' => (clone $queryPr)->where('strata', 'Magister')->count(),
                'doktor' => (clone $queryPr)->where('strata', 'Doktor')->count(),
                'departemen' => (clone $queryDp)->count(),
                'fakultas' => (clone $queryFk)->count(),
            ];
        });
    }

    public function clearProdiStatsCache()
    {
        Cache::forget('stats_prodi_normal');
        Cache::forget('stats_prodi_trash');

        // $this->toast(text: 'Data statistik Program Studi diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi

    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    private function getStatsUser(bool $isTrash = false): array
    {
        // Mengambil versi saat ini. Jika belum ada, gunakan 1.
        $version = Cache::get('stats_user_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        // Key cache menggunakan $version
        $cacheKeyGlobal = "stats_user_global_{$version}{$suffix}";
        $cacheKeyProdi = "stats_user_prodi_{$version}_{$prId}{$suffix}";

        $globalStats = Cache::remember($cacheKeyGlobal, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryUser = User::query();
            if ($isTrash) {
                $queryUser->onlyTrashed();
            }

            return [
                'user-opsi' => (clone $queryUser)->count(),
                'user-aktif' => (clone $queryUser)->where(fn ($q) => $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif')))->count(),
                'user-non-aktif' => (clone $queryUser)->where(fn ($q) => $q->whereHas('admin', fn ($s) => $s->where('status', 'Non-Aktif'))->orWhereHas('dosen', fn ($s) => $s->where('status', 'Non-Aktif'))->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Non-Aktif')))->count(),
                'user' => (clone $queryUser)->count(),
                'admin' => (clone $queryUser)->whereHas('admin')->count(),
                'dosen' => (clone $queryUser)->whereHas('dosen')->count(),
                'mahasiswa' => (clone $queryUser)->whereHas('mahasiswa')->count(),
            ];
        });

        $prodiStats = Cache::remember($cacheKeyProdi, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryUser = User::query();
            if ($isTrash) {
                $queryUser->onlyTrashed();
            }

            return [
                'user-prodi' => (clone $queryUser)->where(function ($q) use ($prId) {
                    $q->whereHas('admin.pr_rel', fn ($s) => $s->where('prodis.id', $prId))
                        ->orWhereHas('dosen.pr_rel', fn ($s) => $s->where('prodis.id', $prId))
                        ->orWhereHas('mahasiswa.pr_rel', fn ($s) => $s->where('prodis.id', $prId));
                })->count(),
            ];
        });

        return array_merge($globalStats, $prodiStats);
    }

    public function clearUserStatsCache()
    {
        $currentVersion = Cache::get('stats_user_version', 1);
        Cache::forever('stats_user_version', $currentVersion + 1);

        // $this->toast(text: 'Data statistik Pengguna telah diperbarui!', type: 'info', variant: 'info');
    }
    // private function getStatsUser(bool $isTrash = false): array
    // {
    //     $cacheKey = 'stats_user_'.($isTrash ? 'trash' : 'normal');

    //     return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () {
    //         $stats = [];

    //         $queryUser = User::query();

    //         // 1. Statistik Dasar
    //         $stats['user-prodi'] = (clone $queryUser)->where(function ($q) {
    //             $q->whereHas('admin.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
    //                 ->orWhereHas('dosen.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
    //                 ->orWhereHas('mahasiswa.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id));
    //         })->count();

    //         $stats['user-prodi'] = (clone $queryUser)->where(function ($q) {
    //             $q->whereHas('admin.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
    //                 ->orWhereHas('dosen.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id))
    //                 ->orWhereHas('mahasiswa.pr_rel', fn ($s) => $s->where('prodis.id', Auth::user()->pr_id));
    //         })->count();

    //         $stats['user-opsi'] = (clone $queryUser)->count();
    //         $stats['user-aktif'] = (clone $queryUser)->where(function ($q) {
    //             $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))
    //                 ->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))
    //                 ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif'));
    //         })->count();

    //         $stats['user-non-aktif'] = (clone $queryUser)->where(function ($q) {
    //             $q->whereHas('admin', fn ($s) => $s->where('status', 'Non-Aktif'))
    //                 ->orWhereHas('dosen', fn ($s) => $s->where('status', 'Non-Aktif'))
    //                 ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Non-Aktif'));
    //         })->count();

    //         $stats['user'] = (clone $queryUser)->count();
    //         $stats['admin'] = (clone $queryUser)->whereHas('admin')->count();
    //         $stats['dosen'] = (clone $queryUser)->whereHas('dosen')->count();
    //         $stats['mahasiswa'] = (clone $queryUser)->whereHas('mahasiswa')->count();

    //         // $angkatanFilter = $this->generateAngkatanFilter();

    //         // $stats['angkatan_list'] = $angkatanFilter;
    //         // $stats['total-seluruh-angkatan'] = $stats['mahasiswa'];
    //         // $stats['angkatan'] = [];

    //         // foreach ($angkatanFilter as $angkatan) {
    //         //     $stats['angkatan'][$angkatan] = (clone $query)
    //         //         ->whereHas('mahasiswa', fn ($q) => $q->where('angkatan', $angkatan))
    //         //         ->count();
    //         // }
    //         return $stats;
    //     });
    // }
    // protected function generateAngkatanFilter(int $jumlah = 5): array
    // {
    //     $now = Carbon::now();
    //     $tahunTerbaru =
    //         $now->month >= 6
    //             ? $now->year
    //             : $now->year - 1;

    //     return collect(range(0, $jumlah - 1))
    //         ->map(fn ($i) => $tahunTerbaru - $i)
    //         ->values()
    //         ->toArray();
    // }

    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
}
