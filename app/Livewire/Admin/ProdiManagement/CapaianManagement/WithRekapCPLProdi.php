<?php

namespace App\Livewire\Admin\ProdiManagement\CapaianManagement;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Penilaian\RekapCPLProdi;

trait WithRekapCPLProdi
{
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
