<?php

namespace App\Http\Services;

use App\Models\Akademik\RPS;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Penilaian\RekapCPLMahasiswa;
use App\Models\Penilaian\RekapCPLProdi;
use App\Models\Penilaian\RekapCPMKMahasiswa;
use App\Models\Penilaian\RekapCPMKProdi;
use App\Models\Penilaian\RekapRPSProdi;
use App\Models\Penilaian\RekapSubCPMKMahasiswa;
use App\Models\Penilaian\RekapSubCPMKProdi;
use App\Models\ProgramStudi\Prodi;

trait RekapCapaian
{
    public function generateRekapCapaian(?int $prId = null)
    {
        $this->generateRekapRPSProdi($prId);

        $this->generateRekapSubCPMKMahasiswa($prId);
        $this->generateRekapCPMKMahasiswa($prId);
        $this->generateRekapCPLMahasiswa($prId);

        $this->generateRekapSubCPMKProdi($prId);
        $this->generateRekapCPMKProdi($prId);
        $this->generateRekapCPLProdi($prId);

    }

    public function generateRekapRPSProdi(?int $prId = null): void
    {
        if ($prId === null) {

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapRPSProdi($id));

            return;
        }

        $hasil = [];

        $nilais = NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->get();

        foreach ($nilais as $nilaiMahasiswa) {

            $rps = $nilaiMahasiswa->rps_rel;

            if (! $rps || $rps->is_draf) {
                continue;
            }

            $nilaiArray = collect($nilaiMahasiswa->nilai_array ?? []);
            $bobotArray = collect($nilaiMahasiswa->bobot_array ?? []);

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
                ($hasil[$rps->id]['nilai'] ?? 0)
                + $nilaiAkhir;

            $hasil[$rps->id]['jumlah'] =
                ($hasil[$rps->id]['jumlah'] ?? 0)
                + 1;
        }

        foreach ($hasil as $rpsId => $data) {

            $nilaiRps =
                $data['jumlah'] > 0
                    ? round(
                        $data['nilai'] / $data['jumlah'],
                        2
                    )
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

            Prodi::pluck('id')
                ->each(function ($id) {
                    $this->generateRekapCPLProdi($id);
                });

            return;
        }

        $hasil = [];

        $nilais = NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.mk_rel',
                'rps_rel.cpmks.cpls',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->get();

        foreach ($nilais as $nilaiMahasiswa) {

            // dump([
            //     'mahasiswa_id' => $nilaiMahasiswa->mahasiswa_id,
            //     'rps_id' => $nilaiMahasiswa->rps_id,
            //     'nilai_array' => $nilaiMahasiswa->nilai_array,
            //     'bobot_array' => $nilaiMahasiswa->bobot_array,
            // ]);

            $rps = $nilaiMahasiswa->rps_rel;

            if (! $rps || $rps->is_draf) {
                continue;
            }

            $sks = $rps->mk_rel?->sks_kuliah ?? 1;

            $nilaiArray = collect($nilaiMahasiswa->nilai_array ?? []);
            $bobotArray = collect($nilaiMahasiswa->bobot_array ?? []);

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

                $cpmk = $rps->cpmks
                    ->firstWhere('id', $cpmkId);

                if (! $cpmk) {
                    continue;
                }

                foreach ($cpmk->cpls as $cpl) {

                    $hasil[$cpl->id]['nilai'] =
                        ($hasil[$cpl->id]['nilai'] ?? 0)
                        + ($nilaiCpmk * $sks);

                    $hasil[$cpl->id]['bobot'] =
                        ($hasil[$cpl->id]['bobot'] ?? 0)
                        + $sks;
                }
            }
        }

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

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapCPMKProdi($id));

            return;
        }

        RekapCPMKMahasiswa::query()
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->selectRaw('
            cpmk_id,
            AVG(nilai) as nilai
        ')
            ->groupBy('cpmk_id')
            ->get()
            ->each(function ($item) use ($prId) {

                RekapCPMKProdi::updateOrCreate(
                    [
                        'pr_id' => $prId,
                        'cpmk_id' => $item->cpmk_id,
                    ],
                    [
                        'nilai' => round($item->nilai, 2),
                    ]
                );
            });
    }

    public function generateRekapSubCPMKProdi(?int $prId = null): void
    {
        if ($prId === null) {

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapSubCPMKProdi($id));

            return;
        }

        RekapSubCPMKMahasiswa::query()
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->selectRaw('
            scpmk_id,
            AVG(nilai) as nilai
        ')
            ->groupBy('scpmk_id')
            ->get()
            ->each(function ($item) use ($prId) {

                RekapSubCPMKProdi::updateOrCreate(
                    [
                        'pr_id' => $prId,
                        'scpmk_id' => $item->scpmk_id,
                    ],
                    [
                        'nilai' => round($item->nilai, 2),
                    ]
                );
            });
    }

    public function generateRekapCPLMahasiswa(?int $prId = null): void
    {
        if ($prId === null) {

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapCPLMahasiswa($id));

            return;
        }

        $hasil = [];

        $nilais = NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.mk_rel',
                'rps_rel.cpmks.cpls',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', function ($q) use ($prId) {
                $q->where('pr_id', $prId);
            })
            ->get();

        foreach ($nilais as $nilaiMahasiswa) {

            $mahasiswaId = $nilaiMahasiswa->mahasiswa_id;

            $rps = $nilaiMahasiswa->rps_rel;

            if (! $rps || $rps->is_draf) {
                continue;
            }

            $sks = $rps->mk_rel?->sks_kuliah ?? 1;

            $nilaiArray = collect($nilaiMahasiswa->nilai_array ?? []);
            $bobotArray = collect($nilaiMahasiswa->bobot_array ?? []);

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

                $cpmk = $rps->cpmks
                    ->firstWhere('id', $cpmkId);

                if (! $cpmk) {
                    continue;
                }

                foreach ($cpmk->cpls as $cpl) {

                    $hasil[$mahasiswaId][$cpl->id]['nilai'] =
                        ($hasil[$mahasiswaId][$cpl->id]['nilai'] ?? 0)
                        + ($nilaiCpmk * $sks);

                    $hasil[$mahasiswaId][$cpl->id]['bobot'] =
                        ($hasil[$mahasiswaId][$cpl->id]['bobot'] ?? 0)
                        + $sks;
                }
            }
        }

        foreach ($hasil as $mahasiswaId => $cpls) {

            foreach ($cpls as $cplId => $data) {

                $nilaiAkhir =
                    $data['bobot'] > 0
                        ? round(
                            $data['nilai'] / $data['bobot'],
                            2
                        )
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

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapCPMKMahasiswa($id));

            return;
        }

        $rekaps = RekapSubCPMKMahasiswa::query()
            ->with('scpmk_rel.cpmks')
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->get();

        $hasil = [];

        foreach ($rekaps as $rekap) {

            foreach ($rekap->scpmk_rel->cpmks as $cpmk) {

                $hasil[$rekap->mahasiswa_id][$cpmk->id]['nilai'] =
                    ($hasil[$rekap->mahasiswa_id][$cpmk->id]['nilai'] ?? 0)
                    + $rekap->nilai;

                $hasil[$rekap->mahasiswa_id][$cpmk->id]['jumlah'] =
                    ($hasil[$rekap->mahasiswa_id][$cpmk->id]['jumlah'] ?? 0)
                    + 1;
            }
        }

        foreach ($hasil as $mahasiswaId => $cpmks) {

            foreach ($cpmks as $cpmkId => $data) {

                RekapCPMKMahasiswa::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'cpmk_id' => $cpmkId,
                    ],
                    [
                        'nilai' => round(
                            $data['nilai'] / $data['jumlah'],
                            2
                        ),
                    ]
                );
            }
        }
    }

    public function generateRekapSubCPMKMahasiswa(?int $prId = null): void
    {
        if ($prId === null) {

            Prodi::pluck('id')
                ->each(fn ($id) => $this->generateRekapSubCPMKMahasiswa($id));

            return;
        }

        $hasil = [];

        $nilais = NilaiMahasiswa::query()
            ->with([
                'mahasiswa_rel',
                'rps_rel.cpmks.scpmks',
            ])
            ->whereHas('mahasiswa_rel', fn ($q) => $q->where('pr_id', $prId))
            ->get();

        foreach ($nilais as $nilaiMahasiswa) {

            $mahasiswaId = $nilaiMahasiswa->mahasiswa_id;

            $rps = $nilaiMahasiswa->rps_rel;

            if (! $rps || $rps->is_draf) {
                continue;
            }

            $nilaiArray = collect($nilaiMahasiswa->nilai_array ?? []);

            $meetingIndex = 0;

            foreach ($rps->cpmks as $cpmk) {

                foreach ($cpmk->scpmks as $scpmk) {

                    $nilai = $nilaiArray[$meetingIndex] ?? null;

                    if ($nilai !== null) {

                        $hasil[$mahasiswaId][$scpmk->id]['nilai'] =
                            ($hasil[$mahasiswaId][$scpmk->id]['nilai'] ?? 0)
                            + $nilai;

                        $hasil[$mahasiswaId][$scpmk->id]['jumlah'] =
                            ($hasil[$mahasiswaId][$scpmk->id]['jumlah'] ?? 0)
                            + 1;
                    }

                    $meetingIndex++;
                }
            }
        }

        foreach ($hasil as $mahasiswaId => $scpmks) {

            foreach ($scpmks as $scpmkId => $data) {

                RekapSubCPMKMahasiswa::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswaId,
                        'scpmk_id' => $scpmkId,
                    ],
                    [
                        'nilai' => round(
                            $data['nilai'] / $data['jumlah'],
                            2
                        ),
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
