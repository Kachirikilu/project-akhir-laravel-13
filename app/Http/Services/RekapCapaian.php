<?php

namespace App\Http\Services;

use App\Jobs\ProcessRekapCapaian;
use App\Livewire\Global\HasToast;
use App\Models\Akademik\RPS;
use App\Models\Auth\Mahasiswa;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Penilaian\RekapCPLMahasiswa;
use App\Models\Penilaian\RekapCPLProdi;
use App\Models\Penilaian\RekapCPMKMahasiswa;
use App\Models\Penilaian\RekapCPMKProdi;
use App\Models\Penilaian\RekapNilaiMahasiswa;
use App\Models\Penilaian\RekapRPSProdi;
use App\Models\Penilaian\RekapSubCPMKMahasiswa;
use App\Models\Penilaian\RekapSubCPMKProdi;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


trait RekapCapaian
{
    // public function generateRekapCapaianQueue(?int $prId = null)
    // {
    //     set_time_limit(0);
    //     DB::disableQueryLog();
    //     gc_collect_cycles();

    //     if ($prId === null) {
    //         $prodiIds = Prodi::pluck('id')->toArray();

    //         foreach ($prodiIds as $id) {
    //             $this->generateRekapNilaiMahasiswa($id);
    //             $this->generateRekapRPSProdi($id);
    //             $this->generateRekapSubCPMKMahasiswa($id);
    //             $this->generateRekapCPMKMahasiswa($id);
    //             $this->generateRekapCPLMahasiswa($id);
    //             $this->generateRekapSubCPMKProdi($id);
    //             $this->generateRekapCPMKProdi($id);
    //             $this->generateRekapCPLProdi($id);

    //             RekapSubCPMKMahasiswa::flushEventListeners();
    //             RekapCPMKMahasiswa::flushEventListeners();
    //             RekapCPLMahasiswa::flushEventListeners();
    //             RekapSubCPMKProdi::flushEventListeners();
    //             RekapCPMKProdi::flushEventListeners();
    //             RekapCPLProdi::flushEventListeners();
    //             Mahasiswa::flushEventListeners();

    //             unset($id);
    //             gc_collect_cycles();
    //         }
    //     } else {
    //         $this->generateRekapNilaiMahasiswa($prId);
    //         $this->generateRekapRPSProdi($prId);
    //         $this->generateRekapSubCPMKMahasiswa($prId);
    //         $this->generateRekapCPMKMahasiswa($prId);
    //         $this->generateRekapCPLMahasiswa($prId);
    //         $this->generateRekapSubCPMKProdi($prId);
    //         $this->generateRekapCPMKProdi($prId);
    //         $this->generateRekapCPLProdi($prId);

    //         gc_collect_cycles();
    //     }
    // }

    
    public function generateRekapCapaianQueue(?int $prId = null)
    {
        set_time_limit(0);
        DB::disableQueryLog();
        gc_collect_cycles();

        if ($prId === null) {
            // Ambil ID prodi dalam bentuk chunk agar hemat memori dari awal
            Prodi::select('id')->chunk(10, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $id = $prodi->id;

                    // Urutan eksekusi dijaga ketat: Nilai & IPK Mahasiswa wajib selesai duluan
                    $this->generateRekapNilaiMahasiswa($id);
                    $this->generateRekapRPSProdi($id);
                    $this->generateRekapSubCPMKMahasiswa($id);
                    $this->generateRekapCPMKMahasiswa($id);
                    $this->generateRekapCPLMahasiswa($id);
                    $this->generateRekapSubCPMKProdi($id);
                    $this->generateRekapCPMKProdi($id);
                    $this->generateRekapCPLProdi($id);

                    unset($id);
                    gc_collect_cycles();
                }
            });

            $this->cleanupGlobalListeners();

        } else {
            $this->generateRekapNilaiMahasiswa($prId);
            $this->generateRekapRPSProdi($prId);
            $this->generateRekapSubCPMKMahasiswa($prId);
            $this->generateRekapCPMKMahasiswa($prId);
            $this->generateRekapCPLMahasiswa($prId);
            $this->generateRekapSubCPMKProdi($prId);
            $this->generateRekapCPMKProdi($prId);
            $this->generateRekapCPLProdi($prId);

            gc_collect_cycles();
        }
    }


    private function cleanupGlobalListeners()
    {
        RekapSubCPMKMahasiswa::flushEventListeners();
        RekapCPMKMahasiswa::flushEventListeners();
        RekapCPLMahasiswa::flushEventListeners();
        RekapSubCPMKProdi::flushEventListeners();
        RekapCPMKProdi::flushEventListeners();
        RekapCPLProdi::flushEventListeners();
        Mahasiswa::flushEventListeners();
        gc_collect_cycles();
    }

    public function generateRekapCapaian($prId = null, $cooldown = null)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        if ($prId) {
            $prodi = Prodi::find($prId);
            $prName = $prodi->prodi ?? 'ini';
        }

        $cooldown ??= ($prId === null ? 60 : 15);

        $runningAllKey = 'rekap_capaian_running_all';
        $runningProdiKey = 'rekap_capaian_running_prodi_ids';
        $runningProdiIds = Cache::get($runningProdiKey, []);

        $dbJobs = DB::table('jobs')
            ->where('payload', 'like', '%ProcessRekapCapaian%')
            ->get();

        $isAnyJobRunningInDB = $dbJobs->isNotEmpty();
        $isAllProdiRunningInDB = false;
        $runningProdiIdsInDB = [];

        if ($isAnyJobRunningInDB) {
            foreach ($dbJobs as $job) {
                $payload = json_decode($job->payload, true);
                $commandStr = $payload['data']['command'] ?? '';

                if (str_contains($commandStr, '"prId";N;')) {
                    $isAllProdiRunningInDB = true;
                }

                if (preg_match('/"prId";i:(\d+);/', $commandStr, $matches)) {
                    $runningProdiIdsInDB[] = (int) $matches[1];
                }
            }
        }

        if (Cache::has($runningAllKey) || $isAllProdiRunningInDB) {
            $this->toast(
                text: 'Gagal! Proses Rekap Capaian untuk SELURUH PROGRAM STUDI sedang berjalan di latar belakang. Silakan tunggu hingga selesai!',
                variant: 'warning',
                duration: 12000
            );

            return;
        }

        if ($prId === null) {
            if (! empty($runningProdiIds) || ! empty($runningProdiIdsInDB)) {
                $this->toast(
                    text: 'Gagal! Tidak bisa merekap seluruh Program Studi karena masih ada proses rekap prodi yang sedang berjalan!',
                    variant: 'warning',
                    duration: 12000
                );

                return;
            }
        }

        if ($prId !== null) {
            $mergedRunningProdi = array_unique(array_merge(
                array_map('intval', $runningProdiIds),
                $runningProdiIdsInDB
            ));

            if (in_array((int) $prId, $mergedRunningProdi)) {
                $this->toast(
                    text: "Gagal! Proses rekap untuk Program Studi {$prName} sedang berjalan di latar belakang!",
                    variant: 'warning',
                    duration: 12000
                );

                return;
            }
        }

        $cacheKey = 'cooldown_rekap_pr_'.($prId ?? 'all');
        if (Cache::has($cacheKey)) {
            $sisaWaktu = Cache::get($cacheKey) - time();
            if ($sisaWaktu > 0) {
                $pesanWaktu = $sisaWaktu >= 3600
                    ? ceil($sisaWaktu / 3600).' jam'
                    : ceil($sisaWaktu / 60).' menit';

                $messagePr = "seluruh Program Studi";
                if ($prId) {
                    $messagePr = "Program Studi $prName";
                }
                $this->toast(
                    text: "Fitur rekap untuk $messagePr sedang beristirahat setelah proses selesai. Silakan coba lagi dalam {$pesanWaktu}!",
                    variant: 'warning',
                    duration: 12000
                );

                return;
            }
        }

        if ($prId === null) {
            Cache::put($runningAllKey, true, now()->addHours(2));
        } else {
            $runningProdiIds[] = (int) $prId;
            Cache::put($runningProdiKey, array_unique($runningProdiIds), now()->addHours(2));
        }

        ProcessRekapCapaian::dispatch($prId, $cooldown);

        $this->toast(
            text: 'Rekap data berhasil dimasukkan ke antrean sistem, silakan tunggu beberapa saat.',
            variant: 'info',
            type: 'info',
            duration: 24000
        );
    }

    public function generateRekapNilaiMahasiswa(?int $prId = null): void
    {
        $mahasiswaQuery = Mahasiswa::query();

        if ($prId !== null) {
            $mahasiswaQuery->where('pr_id', $prId);
        }

        // Memotong proses per 100 mahasiswa untuk menghemat RAM server
        $mahasiswaQuery->chunkById(100, function ($mahasiswas) {
            foreach ($mahasiswas as $mahasiswa) {
                $mahasiswaId = $mahasiswa->id;

                $nilai_mahasiswa = NilaiMahasiswa::query()
                    ->with([
                        'rps_rel.mk_rel',
                    ])
                    ->where('mahasiswa_id', $mahasiswaId)
                    ->get();

                if ($nilai_mahasiswa->isEmpty()) {
                    RekapNilaiMahasiswa::updateOrCreate(
                        ['mahasiswa_id' => $mahasiswaId],
                        [
                            'nilai' => null,
                            'nilai_ipk' => null,
                            'count_rps' => 0,
                            'total_sks' => 0,
                        ]
                    );
                    continue;
                }

                // 1. Ambil data unik berdasarkan rps_id tertinggi (mengantisipasi remedial/double input)
                $nilaiUnik = $nilai_mahasiswa->groupBy('rps_id')->map(function ($group) {
                    return collect($group)->sortByDesc('nilai')->first();
                });

                // 2. Hitung Nilai Rata-Rata Angka Global
                $nilaiRata = round($nilaiUnik->whereNotNull('nilai')->avg('nilai'), 2);

                // 3. Hitung Jumlah RPS dan Total SKS
                $jumlahRps = $nilaiUnik->pluck('rps_id')->filter()->count();
                
                $totalSks = $nilaiUnik->sum(function ($item) {
                    return $item->rps_rel?->mk_rel?->sks_kuliah ?? 0;
                });

                // 4. HITUNG IPK AKURAT BERDASARKAN BOBOT SKS × ACCESSOR nilai_index
                $totalBobotSks = $nilaiUnik->sum(function ($item) {
                    $sks = $item->rps_rel?->mk_rel?->sks_kuliah ?? 0;
                    // Memanggil $item->nilai_index langsung memicu logika match ($this->nilai_mutu) di model
                    $indexMataKuliah = (float) ($item->nilai_index ?? 0.00); 
                    
                    return $sks * $indexMataKuliah;
                });


                $ipk = $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0.00;
                // 5. Simpan ke database rekap
                // $r = RekapNilaiMahasiswa::updateOrCreate(
                RekapNilaiMahasiswa::updateOrCreate(
                    ['mahasiswa_id' => $mahasiswaId],
                    [
                        'nilai' => $nilaiRata,
                        'nilai_ipk' => $ipk,
                        'count_rps' => $jumlahRps,
                        'total_sks' => $totalSks,
                    ]
                );

                // Log::info('IPK Debug', [
                //     'mahasiswa_id' => $mahasiswaId,
                //     'ipk' => $ipk,
                //     'total_sks' => $totalSks,
                //     'total_bobot' => $totalBobotSks,
                // ]);
                // dump($ipk, $r);
            }
        });
    }

    public function generateRekapRPSProdi(?int $prId = null): void
    {
        if ($prId === null) {
            // Optimasi 1: Pecah pengambilan data Prodi agar tidak membebani memori
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapRPSProdi($prodi->id);
                }
            });

            return;
        }

        $hasil = [];

        NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->chunkById(200, function ($nilais) use (&$hasil) {

                foreach ($nilais as $nilai_mahasiswa) {
                    $rps = $nilai_mahasiswa->rps_rel;

                    if (! $rps || $rps->is_draf) {
                        continue;
                    }

                    $nilaiArray = collect($nilai_mahasiswa->nilai_array ?? []);
                    $bobotArray = collect($nilai_mahasiswa->bobot_array ?? []);

                    $total = 0;
                    $totalBobot = 0;

                    foreach ($nilaiArray as $i => $nilai) {
                        $bobot = $bobotArray[$i] ?? 0;

                        if ($nilai === null) {
                            continue;
                        }

                        $total += $nilai * $bobot;
                        $totalBobot += $bobot;
                    }

                    if ($totalBobot <= 0) {
                        continue;
                    }

                    $nilaiAkhir = $total / $totalBobot;

                    $hasil[$rps->id]['nilai'] =
                        ($hasil[$rps->id]['nilai'] ?? 0) + $nilaiAkhir;

                    $hasil[$rps->id]['jumlah'] =
                        ($hasil[$rps->id]['jumlah'] ?? 0) + 1;
                }
            });

        foreach ($hasil as $rpsId => $data) {
            $nilaiRps =
                $data['jumlah'] > 0
                    ? round($data['nilai'] / $data['jumlah'], 2)
                    : null;

            RekapRPSProdi::updateOrCreate(
                [
                    'rps_id' => $rpsId,
                    'pr_id' => $prId,
                ],
                [
                    'nilai' => $nilaiRps,
                ]
            );
        }
    }

    public function generateRekapCPLProdi(?int $prId = null): void
    {
        if ($prId === null) {
            // Gabungkan loop Prodi menggunakan chunkById agar hemat RAM
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapCPLProdi($prodi->id);
                }
            });

            return;
        }

        $hasil = [];

        NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.mk_rel',
                'rps_rel.cpmks.cpls',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->chunkById(100, function ($nilais) use (&$hasil) {
                foreach ($nilais as $nilai_mahasiswa) {
                    $rps = $nilai_mahasiswa->rps_rel;

                    if (! $rps || $rps->is_draf) {
                        continue;
                    }

                    $sks = $rps->mk_rel?->sks_kuliah ?? 1;

                    $nilaiArray = collect($nilai_mahasiswa->nilai_array ?? []);
                    $bobotArray = collect($nilai_mahasiswa->bobot_array ?? []);

                    $mapping = $this->buildCpmkMeetingMap($rps);

                    foreach ($mapping as $cpmkId => $indexes) {
                        $total = 0;
                        $totalBobot = 0;

                        foreach ($indexes as $idx) {
                            $nilai = $nilaiArray[$idx] ?? null;
                            $bobot = $bobotArray[$idx] ?? 0;

                            if ($nilai === null) {
                                continue;
                            }

                            $total += $nilai * $bobot;
                            $totalBobot += $bobot;
                        }

                        if ($totalBobot <= 0) {
                            continue;
                        }

                        $nilaiCpmk = $total / $totalBobot;

                        $cpmk = $rps->cpmks->firstWhere('id', $cpmkId);

                        if (! $cpmk) {
                            continue;
                        }

                        foreach ($cpmk->cpls as $cpl) {
                            $hasil[$cpl->id]['nilai'] =
                                ($hasil[$cpl->id]['nilai'] ?? 0) + ($nilaiCpmk * $sks);

                            $hasil[$cpl->id]['bobot'] =
                                ($hasil[$cpl->id]['bobot'] ?? 0) + $sks;
                        }
                    }
                }
            });

        foreach ($hasil as $cplId => $data) {
            $nilaiAkhir =
                $data['bobot'] > 0
                    ? round($data['nilai'] / $data['bobot'], 2)
                    : null;

            RekapCPLProdi::updateOrCreate(
                [
                    'pr_id' => $prId,
                    'cpl_id' => $cplId,
                ],
                [
                    'nilai' => $nilaiAkhir,
                ]
            );
        }
    }

    public function generateRekapCPMKProdi(?int $prId = null): void
    {
        if ($prId === null) {
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapCPMKProdi($prodi->id);
                }
            });

            return;
        }

        RekapCPMKMahasiswa::query()
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->selectRaw('cpmk_id, AVG(nilai) as nilai')
            ->groupBy('cpmk_id')
            ->orderBy('cpmk_id')
            ->chunk(200, function ($items) use ($prId) {
                foreach ($items as $item) {
                    RekapCPMKProdi::updateOrCreate(
                        [
                            'pr_id' => $prId,
                            'cpmk_id' => $item->cpmk_id,
                        ],
                        [
                            'nilai' => round($item->nilai, 2),
                        ]
                    );
                }
            });
    }

    public function generateRekapSubCPMKProdi(?int $prId = null): void
    {
        if ($prId === null) {
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapSubCPMKProdi($prodi->id);
                }
            });

            return;
        }

        RekapSubCPMKMahasiswa::query()
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->selectRaw('scpmk_id, AVG(nilai) as nilai')
            ->groupBy('scpmk_id')
            ->orderBy('scpmk_id')
            ->chunk(200, function ($items) use ($prId) {
                foreach ($items as $item) {
                    RekapSubCPMKProdi::updateOrCreate(
                        [
                            'pr_id' => $prId,
                            'scpmk_id' => $item->scpmk_id,
                        ],
                        [
                            'nilai' => round($item->nilai, 2),
                        ]
                    );
                }
            });
    }

    public function generateRekapCPLMahasiswa(?int $prId = null): void
    {
        if ($prId === null) {
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapCPLMahasiswa($prodi->id);
                }
            });

            return;
        }

        $hasil = [];

        NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.mk_rel',
                'rps_rel.cpmks.cpls',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->chunkById(100, function ($nilais) use (&$hasil) {
                foreach ($nilais as $nilai_mahasiswa) {
                    $mahasiswaId = $nilai_mahasiswa->mahasiswa_id;
                    $rps = $nilai_mahasiswa->rps_rel;

                    if (! $rps || $rps->is_draf) {
                        continue;
                    }

                    $sks = $rps->mk_rel?->sks_kuliah ?? 1;
                    $nilaiArray = collect($nilai_mahasiswa->nilai_array ?? []);
                    $bobotArray = collect($nilai_mahasiswa->bobot_array ?? []);
                    $mapping = $this->buildCpmkMeetingMap($rps);

                    foreach ($mapping as $cpmkId => $indexes) {
                        $total = 0;
                        $totalBobot = 0;

                        foreach ($indexes as $idx) {
                            $nilai = $nilaiArray[$idx] ?? null;
                            $bobot = $bobotArray[$idx] ?? 0;

                            if ($nilai === null) {
                                continue;
                            }

                            $total += $nilai * $bobot;
                            $totalBobot += $bobot;
                        }

                        if ($totalBobot <= 0) {
                            continue;
                        }

                        $nilaiCpmk = $total / $totalBobot;
                        $cpmk = $rps->cpmks->firstWhere('id', $cpmkId);

                        if (! $cpmk) {
                            continue;
                        }

                        foreach ($cpmk->cpls as $cpl) {
                            $hasil[$mahasiswaId][$cpl->id]['nilai'] =
                                ($hasil[$mahasiswaId][$cpl->id]['nilai'] ?? 0) + ($nilaiCpmk * $sks);

                            $hasil[$mahasiswaId][$cpl->id]['bobot'] =
                                ($hasil[$mahasiswaId][$cpl->id]['bobot'] ?? 0) + $sks;
                        }
                    }
                }
            });

        // Simpan data akumulasi chunk ke database
        foreach ($hasil as $mahasiswaId => $cpls) {
            foreach ($cpls as $cplId => $data) {
                $nilaiAkhir =
                    $data['bobot'] > 0
                        ? round($data['nilai'] / $data['bobot'], 2)
                        : null;

                RekapCPLMahasiswa::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'cpl_id' => $cplId,
                    ],
                    [
                        'nilai' => $nilaiAkhir,
                    ]
                );
            }
        }
    }

    public function generateRekapCPMKMahasiswa(?int $prId = null): void
    {
        if ($prId === null) {
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapCPMKMahasiswa($prodi->id);
                }
            });

            return;
        }

        $hasil = [];

        RekapSubCPMKMahasiswa::query()
            ->with('scpmk_rel.cpmks')
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->chunkById(150, function ($rekaps) use (&$hasil) {
                foreach ($rekaps as $rekap) {
                    if (! $rekap->scpmk_rel) {
                        continue;
                    }

                    foreach ($rekap->scpmk_rel->cpmks as $cpmk) {
                        $hasil[$rekap->mahasiswa_id][$cpmk->id]['nilai'] =
                            ($hasil[$rekap->mahasiswa_id][$cpmk->id]['nilai'] ?? 0) + $rekap->nilai;

                        $hasil[$rekap->mahasiswa_id][$cpmk->id]['jumlah'] =
                            ($hasil[$rekap->mahasiswa_id][$cpmk->id]['jumlah'] ?? 0) + 1;
                    }
                }
            });

        foreach ($hasil as $mahasiswaId => $cpmks) {
            foreach ($cpmks as $cpmkId => $data) {
                if ($data['jumlah'] <= 0) {
                    continue;
                }

                RekapCPMKMahasiswa::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'cpmk_id' => $cpmkId,
                    ],
                    [
                        'nilai' => round($data['nilai'] / $data['jumlah'], 2),
                    ]
                );
            }
        }
    }

    public function generateRekapSubCPMKMahasiswa(?int $prId = null): void
    {
        if ($prId === null) {
            Prodi::select('id')->chunkById(50, function ($prodis) {
                foreach ($prodis as $prodi) {
                    $this->generateRekapSubCPMKMahasiswa($prodi->id);
                }
            });

            return;
        }

        $hasil = [];

        NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->chunkById(100, function ($nilais) use (&$hasil) {
                foreach ($nilais as $nilai_mahasiswa) {
                    $mahasiswaId = $nilai_mahasiswa->mahasiswa_id;
                    $rps = $nilai_mahasiswa->rps_rel;

                    if (! $rps || $rps->is_draf) {
                        continue;
                    }

                    $nilaiArray = collect($nilai_mahasiswa->nilai_array ?? []);
                    $meetingIndex = 0;

                    foreach ($rps->cpmks as $cpmk) {
                        foreach ($cpmk->scpmks as $scpmk) {
                            $nilai = $nilaiArray[$meetingIndex] ?? null;

                            if ($nilai !== null) {
                                $hasil[$mahasiswaId][$scpmk->id]['nilai'] =
                                    ($hasil[$mahasiswaId][$scpmk->id]['nilai'] ?? 0) + $nilai;

                                $hasil[$mahasiswaId][$scpmk->id]['jumlah'] =
                                    ($hasil[$mahasiswaId][$scpmk->id]['jumlah'] ?? 0) + 1;
                            }

                            $meetingIndex++;
                        }
                    }
                }
            });

        foreach ($hasil as $mahasiswaId => $scpmks) {
            foreach ($scpmks as $scpmkId => $data) {
                if ($data['jumlah'] <= 0) {
                    continue;
                }

                RekapSubCPMKMahasiswa::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'scpmk_id' => $scpmkId,
                    ],
                    [
                        'nilai' => round($data['nilai'] / $data['jumlah'], 2),
                    ]
                );
            }
        }
    }

    protected function buildCpmkMeetingMap(RPS $rps): array
    {
        $mapping = [];

        $utsKeywords = collect(
            explode(',', env('UTS_FIELDS', 'UTS,EVALUASI AWAL'))
        )
            ->map(fn ($v) => strtoupper(trim($v)))
            ->filter()
            ->values();

        $uasKeywords = collect(
            explode(',', env('UAS_FIELDS', 'UAS,EVALUASI AKHIR,LAPORAN AKHIR,HASIL PROYEK,HASIL PROJEK'))
        )
            ->map(fn ($v) => strtoupper(trim($v)))
            ->filter()
            ->values();

        $meetingIndex = 0;

        $allScpmks = $rps->cpmks
            ->flatMap(fn ($cpmk) => $cpmk->scpmks);

        $hasUTS = $allScpmks->contains(function ($scpmk) use ($utsKeywords) {
            return $utsKeywords->contains(
                strtoupper(trim($scpmk->metode ?? ''))
            );
        });

        $hasUAS = $allScpmks->contains(function ($scpmk) use ($uasKeywords) {
            return $uasKeywords->contains(
                strtoupper(trim($scpmk->metode ?? ''))
            );
        });

        $cpmks = $rps->cpmks->sortBy(function ($cpmk) {
            return $cpmk->pivot->sort_order ?? 0;
        });

        foreach ($cpmks as $cpmk) {

            $mapping[$cpmk->id] = [];

            $scpmks = $cpmk->scpmks
                ->sortBy(function ($scpmk) {
                    return $scpmk->pivot->sort_order ?? 0;
                });

            foreach ($scpmks as $scpmk) {
                if (! $hasUTS && $meetingIndex === 7) {
                    $meetingIndex++;
                }
                if (! $hasUAS && $meetingIndex === 15) {
                    $meetingIndex++;
                }

                $mapping[$cpmk->id][] = $meetingIndex;

                $meetingIndex++;
            }
        }

        return $mapping;
    }
}
