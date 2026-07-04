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
        $version = Cache::get('stats_obe_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $cacheKey = "stats_obe_pr_{$version}_" . ($prodiId ?? 'all') . $suffix;

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prodiId) {
            $relations = [
                'mahasiswa' => 'mahasiswa.pr_rel',
                'rps' => 'mk_rel.prodis',
                'cpl' => 'prodis',
                'cpmk' => 'rps.mk_rel.prodis',
                'scpmk' => 'cpmks.rps.mk_rel.prodis',
            ];

            $applyFilter = function ($query, $modelKey) use ($isTrash, $prodiId, $relations) {
                if ($isTrash) $query->onlyTrashed();
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
        $version = Cache::get('stats_rps_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $cacheKey = "stats_rps_pr_{$version}_" . ($prodiId ?? 'all') . $suffix;
        
        $currentYear = now()->year;
        $fiveYearsAgoYear = now()->subYears(5)->year;

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $currentYear, $fiveYearsAgoYear, $prodiId) {
            $queryRPS = RPS::query();
            if ($isTrash) $queryRPS->onlyTrashed();
            
            if ($prodiId !== null) {
                $queryRPS->whereHas('mk_rel.prodis', fn ($q) => $q->where('prodis.id', $prodiId));
            }

            return [
                'rps-akademik' => (clone $queryRPS)->where('akademik', 'like', "%$currentYear%")->count(),
                'rps-rev-new'  => (clone $queryRPS)->whereYear('revisi', $currentYear)->count(),
                'rps-aktif'    => (clone $queryRPS)->where('is_draf', false)->count(),
                'rps-draf'     => (clone $queryRPS)->where('is_draf', true)->count(),
                'rps-older-5'  => (clone $queryRPS)->whereRaw('RIGHT(akademik, 4) < ?', [$fiveYearsAgoYear])->count(),
            ];
        });

        $prodiStats = Cache::remember('stats_rps_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            return [
                'rps-prodi' => (clone $queryRPS)->whereHas('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
                'rps-prodi-non-aktif' => (clone $queryRPS)->whereHas('mk_rel.prodis', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->whereDoesntHave('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
            ];
        });

        if (Auth::user()->dosen) {
            $userId = Auth::id();
            $userStats = Cache::remember('stats_rps_user_pr_'.$userId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryRPS = RPS::query();
                if ($isTrash) {
                    $queryRPS->onlyTrashed();
                }

                return [
                    'rps-saya' => (clone $queryRPS)->whereHas('tim_dosens.dosens.user', function ($q) use ($userId) {
                        $q->where('users.id', $userId);
                    })->count(),
                ];
            });
        } else {
            $userStats = [];
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }
    public function clearRpsProdiStatsCache($prodiId = null)
    {
        $this->clearCacheProdiStats('rps', $prodiId, 'Rencana Pembelajaran Semester');
    }

    private function getStatsKurikulumProdi(string $prefix, bool $isTrash = false, $prodiId = null): array
    {
        $version = Cache::get("stats_{$prefix}_version", 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        
        $cacheKey = "stats_{$prefix}_pr_{$version}_" . ($prodiId ?? 'all') . $suffix;
        $modelMap = ['cpl' => CPL::class, 'cpmk' => CPMK::class, 'scpmk' => SubCPMK::class];
        $modelClass = $modelMap[$prefix];

        $relationMap = [
            'cpl' => 'prodis',
            'cpmk' => 'rps.mk_rel.prodis',
            'scpmk' => 'cpmks.rps.mk_rel.prodis',
        ];

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($modelClass, $prefix, $isTrash, $prodiId, $relationMap) {
            $query = $modelClass::query();
            if ($isTrash) $query->onlyTrashed();

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
        $version = Cache::get('stats_mhs_version', 1);
        $suffix = $isTrash ? '_trash' : '_normal';
        $cacheKey = "stats_mhs_pr_{$version}_" . ($prodiId ?? 'all') . $suffix;

        return Cache::remember($cacheKey, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prodiId) {
            $query = User::query();
            if ($isTrash) $query->onlyTrashed();
            if ($prodiId !== null) {
                $query->whereHas('mahasiswa.pr_rel', fn ($q) => $q->where('prodis.id', $prodiId));
            }

            return [
                'mahasiswa-aktif' => (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', 'aktif'))->count(),
                'mahasiswa-non-aktif' => (clone $query)->whereHas('mahasiswa', fn ($q) => $q->where('status', '!=', 'aktif'))->count(),
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
            $this->toast(text: "Data statistik $label untuk Program Studi ini diperbarui!", type: 'info', variant: 'info');
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
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        $globalStats = Cache::remember('stats_kelas'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryKelas = Kelas::query();
            if ($isTrash) {
                $queryKelas->onlyTrashed();
            }

            return [
                'kelas' => (clone $queryKelas)->count(),
                'kelas-tp' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                    $q->where('tipe_sks', 1);
                })->count(),

                'kelas-pr' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                    $q->where('tipe_sks', 2);
                })->count(),

                'kelas-pl' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                    $q->where('tipe_sks', 3);
                })->count(),

                'kelas-sm' => (clone $queryKelas)->whereHas('rps_rel.mk_rel', function ($q) {
                    $q->where('tipe_sks', 4);
                })->count(),
            ];
        });
        $prodiStats = Cache::remember('stats_kelas_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryKelas = Kelas::query();
            if ($isTrash) {
                $queryKelas->onlyTrashed();
            }

            $tabQuery = clone $queryKelas;
            // $this->buttonMKSwitch($tabQuery);

            return [
                'kelas-prodi' => (clone $tabQuery)->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
                'kelas-opsi' => (clone $tabQuery)->count(),
                'kelas-wajib' => (clone $tabQuery)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('is_wajib', true))->count(),
                'kelas-pilihan' => (clone $tabQuery)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('is_wajib', false))->count(),
                'kelas-uni' => (clone $tabQuery)->whereHas('rps_rel.mk_rel', fn ($q) => $q->where('level_mk', 4))->count(),
            ];
        });
        if (Auth::user()->dosen) {
            $userId = Auth::id();
            $userStats = Cache::remember('stats_kelas_user_'.$userId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryKelas = Kelas::query();
                if ($isTrash) {
                    $queryKelas->onlyTrashed();
                }

                return [
                    'kelas-saya' => (clone $queryKelas)->whereHas('rps_rel.tim_dosens.dosens.user', fn ($q) => $q->where('users.id', $userId))->count(),
                ];
            });
        } else {
            $userStats = [];
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearKelasStatsCache()
    {
        Cache::forget('stats_kelas_normal');
        Cache::forget('stats_kelas_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_kelas_prodi_'.$prId.'_normal');
        Cache::forget('stats_kelas_prodi_'.$prId.'_trash');
        if (Auth::user()->dosen) {
            $userId = Auth::id();
            Cache::forget('stats_kelas_user_'.$userId.'_normal');
            Cache::forget('stats_kelas_user_'.$userId.'_trash');
        }
        $this->toast(text: 'Data statistik Kelas diperbarui!', type: 'info', variant: 'info');
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
        $this->toast(text: 'Data statistik OBE diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS // Statistik RPS RPS RPS
    private function getStatsRps(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';
        $currentYear = now()->year;
        $fiveYearsAgoYear = now()->subYears(5)->year;

        $globalStats = Cache::remember('stats_rps'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $currentYear, $fiveYearsAgoYear) {
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

        $prodiStats = Cache::remember('stats_rps_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryRPS = RPS::query();
            if ($isTrash) {
                $queryRPS->onlyTrashed();
            }

            return [
                'rps-prodi' => (clone $queryRPS)->whereHas('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
                'rps-prodi-non-aktif' => (clone $queryRPS)->whereHas('mk_rel.prodis', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->whereDoesntHave('tim_dosens.pr_rel', function ($q) use ($prId) {
                    $q->where('prodis.id', $prId);
                })->count(),
            ];
        });

        if (Auth::user()->dosen) {
            $userId = Auth::id();
            $userStats = Cache::remember('stats_rps_user_'.$userId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $queryRPS = RPS::query();
                if ($isTrash) {
                    $queryRPS->onlyTrashed();
                }

                return [
                    'rps-saya' => (clone $queryRPS)->whereHas('tim_dosens.dosens.user', function ($q) use ($userId) {
                        $q->where('users.id', $userId);
                    })->count(),
                ];
            });
        } else {
            $userStats = [];
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearRpsStatsCache()
    {
        Cache::forget('stats_rps_normal');
        Cache::forget('stats_rps_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_rps_prodi_'.$prId.'_normal');
        Cache::forget('stats_rps_prodi_'.$prId.'_trash');
        if (Auth::user()->dosen) {
            $userId = Auth::id();
            Cache::forget('stats_rps_user_'.$userId.'_normal');
            Cache::forget('stats_rps_user_'.$userId.'_trash');
        }
        $this->toast(text: 'Data statistik Rencana Pembelajaran Semester diperbarui!', type: 'info', variant: 'info');
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

    // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL
    // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL // Statistik CPL CPL CPL
    public function clearCplStatsCache()
    {
        Cache::forget('stats_cpl_normal');
        Cache::forget('stats_cpl_trash');
        $this->toast(text: 'Data statistik Capaian Pembelajaran Lulusan diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK
    // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK // Statistik CPMK CPMK CPMK
    public function clearCpmkStatsCache()
    {
        Cache::forget('stats_cpmk_normal');
        Cache::forget('stats_cpmk_trash');
        $this->toast(text: 'Data statistik Capaian Pembelajaran Mata Kuliah diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK
    // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK // Statistik Sub-CPMK Sub-CPMK Sub-CPMK
    public function clearScpmkStatsCache()
    {
        Cache::forget('stats_scpmk_normal');
        Cache::forget('stats_scpmk_trash');
        $this->toast(text: 'Data statistik Sub-CPMK diperbarui!', type: 'info', variant: 'info');
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
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_ref_prodi_'.$prId.'_normal');
        Cache::forget('stats_ref_prodi_'.$prId.'_trash');
        $this->toast(text: 'Data statistik Referensi diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen
    // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen // Statistik Tim Dosen
    private function getStatsTimDosen(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';
        $userId = Auth::id();

        $globalStats = Cache::remember('stats_tim_dosen'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
            $query = TimDosen::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'tim-dosen-rps' => (clone $query)->whereHas('rps')->count(),
                'tim-dosen-non-rps' => (clone $query)->whereDoesntHave('rps')->count(),
                'tim-dosen-all' => (clone $query)->count(),
                'tim-dosen-saya' => (clone $query)->whereHas('dosens', fn ($q) => $q->where('user_id', $userId))->count(),
            ];
        });
        // Stats Prodi
        $prodiStats = Cache::remember('stats_tim_dosen_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $query = TimDosen::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'tim-dosen-prodi' => (clone $query)->whereHas('pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
            ];
        });

        if (Auth::user()->dosen) {
            $userId = Auth::id();
            $userStats = Cache::remember('stats_tim_dosen_user_'.$userId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $userId) {
                $query = TimDosen::query();
                if ($isTrash) {
                    $query->onlyTrashed();
                }

                return [
                    'tim-dosen-saya' => (clone $query)->whereHas('dosens', fn ($q) => $q->where('user_id', $userId))->count(),
                ];
            });
        } else {
            $userStats = [];
        }

        return array_merge($globalStats, $prodiStats, $userStats);
    }

    public function clearTimDosenStatsCache()
    {
        Cache::forget('stats_tim_dosen_normal');
        Cache::forget('stats_tim_dosen_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_tim_dosen_prodi_'.$prId.'_normal');
        Cache::forget('stats_tim_dosen_prodi_'.$prId.'_trash');
        if (Auth::user()->dosen) {
            $userId = Auth::id();
            Cache::forget('stats_tim_dosen_user_'.$userId.'_normal');
            Cache::forget('stats_tim_dosen_user_'.$userId.'_trash');
        }
        $this->toast(text: 'Data statistik Tim Dosen diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    private function getStatsDosen(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        $globalStats = Cache::remember('stats_dosen'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'dosen-rps' => (clone $query)->whereHas('dosen.tim_dosens.rps')->count(),
                'dosen-non-rps' => (clone $query)->whereDoesntHave('dosen.tim_dosens.rps')->count(),
                'dosen-all' => (clone $query)->whereHas('dosen')->count(),
                'dosen-aktif' => (clone $query)->whereHas('dosen', fn ($q) => $q->where('status', 'aktif'))->count(),
                'dosen-non-aktif' => (clone $query)->whereHas('dosen', fn ($q) => $q->where('status', '!=', 'aktif'))->count(),
            ];
        });

        $prodiStats = Cache::remember('stats_dosen_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'dosen-prodi' => (clone $query)->whereHas('dosen.pr_rel', fn ($q) => $q->where('prodis.id', $prId))->count(),
            ];
        });

        return array_merge($globalStats, $prodiStats);
    }

    public function clearDosenStatsCache()
    {
        Cache::forget('stats_dosen_normal');
        Cache::forget('stats_dosen_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_dosen_prodi_'.$prId.'_normal');
        Cache::forget('stats_dosen_prodi_'.$prId.'_trash');
        $this->toast(text: 'Data statistik Dosen diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen
    // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen // Statistik Dosen

    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
   private function getStatsMahasiswa(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        $globalStats = Cache::remember('stats_mhs'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $query = User::query();
            if ($isTrash) {
                $query->onlyTrashed();
            }

            return [
                'mahasiswa-opsi' => (clone $query)->whereHas('mahasiswa')->count(),
                'mahasiswa-aktif' => (clone $query)->whereHas('mahasiswa', function ($q) {
                    $q->where('status', 'aktif');
                })->count(),
                'mahasiswa-non-aktif' => (clone $query)->whereHas('mahasiswa', function ($q) {
                    $q->where('status', '!=', 'aktif');
                })->count(),
            ];
        });
        $prodiStats = Cache::remember('stats_mhs_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
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
        Cache::forget('stats_mhs_normal');
        Cache::forget('stats_mhs_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_mhs_prodi_'.$prId.'_normal');
        Cache::forget('stats_mhs_prodi_'.$prId.'_trash');
        $this->toast(text: 'Data statistik Mahasiswa diperbarui!', type: 'info', variant: 'info');
    }

    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa
    // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa // Statistik Mahasiswa

    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah // Statistik Mata Kuliah
    private function getStatsMK(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        $globalStats = Cache::remember('stats_mk'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
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
        $prodiStats = Cache::remember('stats_mk_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryMK = MataKuliah::query();
            if ($isTrash) {
                $queryMK->onlyTrashed();
            }

            $tabQuery = clone $queryMK;
            $this->buttonMKSwitch($tabQuery);

            return [
                'mk-prodi' => (clone $tabQuery)->whereHas('prodis', fn ($q) => $q->where('prodis.id', $prId))->count(),
                'mk-opsi' => (clone $tabQuery)->count(),
                'mk-wajib' => (clone $tabQuery)->where('is_wajib', true)->count(),
                'mk-pilihan' => (clone $tabQuery)->where('is_wajib', false)->count(),
                'mk-uni' => (clone $tabQuery)->where('level_mk', 4)->count(),
            ];
        });

        return array_merge($globalStats, $prodiStats);
    }

    public function clearMkStatsCache()
    {
        Cache::forget('stats_mk_normal');
        Cache::forget('stats_mk_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_mk_prodi_'.$prId.'_normal');
        Cache::forget('stats_mk_prodi_'.$prId.'_trash');
        $this->toast(text: 'Data statistik Mata Kuliah diperbarui!', type: 'info', variant: 'info');
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

        $this->toast(text: 'Data statistik Program Studi diperbarui!', type: 'info', variant: 'info');
    }
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi
    // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi // Statistik Program Studi

    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User) // Statistik Pengguna (User)
    private function getStatsUser(bool $isTrash = false): array
    {
        $suffix = $isTrash ? '_trash' : '_normal';
        $prId = Auth::user()->pr_id ?? 'all';

        $globalStats = Cache::remember('stats_user'.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash) {
            $queryUser = User::query();
            if ($isTrash) {
                $queryUser->onlyTrashed();
            }

            return [
                'user-opsi' => (clone $queryUser)->count(),
                'user-aktif' => (clone $queryUser)->where(fn ($q) => $q->whereHas('admin', fn ($s) => $s->where('status', 'Aktif'))->orWhereHas('dosen', fn ($s) => $s->where('status', 'Aktif'))->orWhereHas('mahasiswa', fn ($s) => $s->where('status', 'Aktif')))->count(),
                'user-non-aktif' => (clone $queryUser)->where(fn ($q) => $q->whereHas('admin', fn ($s) => $s->where('status', '!=', 'Aktif'))->orWhereHas('dosen', fn ($s) => $s->where('status', '!=', 'Aktif'))->orWhereHas('mahasiswa', fn ($s) => $s->where('status', '!=', 'Aktif')))->count(),
                'user' => (clone $queryUser)->count(),
                'admin' => (clone $queryUser)->whereHas('admin')->count(),
                'dosen' => (clone $queryUser)->whereHas('dosen')->count(),
                'mahasiswa' => (clone $queryUser)->whereHas('mahasiswa')->count(),
            ];
        });
        $prodiStats = Cache::remember('stats_user_prodi_'.$prId.$suffix, now()->addMinutes($this->cacheDurationMinutes), function () use ($isTrash, $prId) {
            $queryUser = User::query();
            if ($isTrash) {
                $queryUser->onlyTrashed();
            }

            return [
                'user-prodi' => $queryUser->where(function ($q) use ($prId) {
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
        Cache::forget('stats_user_normal');
        Cache::forget('stats_user_trash');
        $prId = Auth::user()->pr_id;
        Cache::forget('stats_user_prodi_'.$prId.'_normal');
        Cache::forget('stats_user_prodi_'.$prId.'_trash');
        $this->toast(text: 'Data statistik Pengguna diperbarui!', type: 'info', variant: 'info');
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
    //             $q->whereHas('admin', fn ($s) => $s->where('status', '!=', 'Aktif'))
    //                 ->orWhereHas('dosen', fn ($s) => $s->where('status', '!=', 'Aktif'))
    //                 ->orWhereHas('mahasiswa', fn ($s) => $s->where('status', '!=', 'Aktif'));
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
