<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use App\Models\Akademik\SubCPMK;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;

trait WithRPSShow
{
    public $detailRPSModal = false;

    // public $detailRPSData = [];

    public $prodisRPS = [];

    public function showRPS($id)
    {
        $this->selected_id_rps = $id;

        try {
            $rps = RPS::with([
                'mk_rel.prodis',
                'tim_dosens'
            ])->findOrFail($id);

            // $this->prodisRPS = $rps->tim_dosens
            //     ->map(fn($tim) => $tim->pr_rel)
            //     ->filter()
            //     ->unique('id')
            //     ->sortBy([
            //         ['nama_pr', 'asc'],
            //         ['strata', 'desc'],
            //     ]);
           $this->prodisRPS = $rps->mk_rel->prodis->sortBy([
                ['nama_pr', 'asc'],
                ['strata', 'desc'],
            ]);
            $this->detailRPSModal = true;

            $this->dispatch('fill-modal-rps', rps: $rps);
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function printPDFRPS($rpsId, $prId = null)
    {
        try {
            $data = $this->handleRpsPdfExport($rpsId, $prId, 'stream', false);
            
            return response()->streamDownload(function () use ($data) {
                echo $data['content'];
            }, $data['name'], ['Content-Type' => 'application/pdf']);
            
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    protected function handleRpsPdfExport($rpsId, $prodiIdentifier, $exportType = 'stream', $isKode = false)
    {
        $rps = RPS::with([
                'mk_rel.prodis',
                'tim_dosens',
                'tim_dosens.dosens',
                // 'cpmks.scpmks.dosens',
                'cpmks.scpmks.refs',
                'cpmks.refs',
                'cpmks.cpls',
                'refs'
            ])->findOrFail($rpsId);
        $prodis = $rps->mk_rel->prodis;
        $prodi = null;

        // Logika Pemilihan Prodi yang sama
        if ($prodiIdentifier) {
            $found = $isKode ? $this->getProdiByKode($prodiIdentifier) : $prodis->find($prodiIdentifier);
            if ($found) $prodi = $prodis->firstWhere('id', $found->id);
        }
        if (!$prodi) $prodi = $prodis->firstWhere('id', Auth::user()->pr_id ?? null);
        if (!$prodi) $prodi = $prodis->first();

        if (!$prodi) {
            throw new \Exception("Data Program Studi tidak ditemukan pada RPS ini!");
        }

        // Generate Raw PDF
        $pdfRawContent = $this->generateRPSRawPDFContent($rps, $prodi);
        
        // Penamaan file yang aman
        $fileName = "RPS_{$prodi->kode}_{$rps->kode}_{$rps->mk_rel->mk}.pdf";
        $fileNameSafe = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $fileName);

        return [
            'content' => $pdfRawContent,
            'name' => $fileNameSafe,
            'rps' => $rps,
            'prodi' => $prodi
        ];
    }

    protected function generateRPSRawPDFContent(RPS $rps, Prodi $prodi): string
    {
        // $data = $this->formatRPSDetailForShow($rps);
        $logoPath = public_path('images/logo-unsri.png');
        $logoBase64 = '';

        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $dataLogo = file_get_contents($logoPath);
            $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($dataLogo);
        }

        $prodi = Prodi::with(['dp_rel', 'dp_rel.fk_rel'])->findOrFail($prodi->id);
        $tim_dosen = $rps->tim_dosens->where('pr_id', $prodi->id);

        $html = view('livewire.staff.obe-management.rps-management.rps-pdf-print', [
            'rps' => $rps,
            'prodi' => $prodi,
            'tim_dosen' => $tim_dosen,
            // 'detailRPSData' => $data,
            'logoBase64' => $logoBase64,
        ])->render();

        $browsershot = Browsershot::html($html)
            ->noSandbox()
            ->format('A4')
            ->margins(0, 10, 0, 10)
            ->showBackground();

        if ($chromePath = env('BROWSERSHOT_CHROME_PATH')) {
            $browsershot->setChromePath($chromePath);
        }

        return $browsershot->pdf();
    }

    public function formatRPSDetailForShow(RPS $rps): array
    {
        $mk = $rps->mk_rel;
        $prodi = $mk?->prodis->first();

        $timPengajar = $rps->dosens->map(function ($dosen) {
            return $dosen->name.'<br>(NIP: '.($dosen->nip ?? '-').')';
        })->filter()->implode("\n");
        $ketua = optional($rps->dosens->first(function ($d) {
            return (bool) ($d->pivot->is_ketua ?? false);
        }))->name ?: $rps->dosens->first()?->name;
        $instruktur = $rps->dosens->filter(function ($d) {
            return strtolower(trim((string) ($d->pivot->peran ?? ''))) === 'pengajar';
        })->pluck('name')->filter()->implode("\n");

        $dosenCount = $rps->dosens->count();

        $desRPS = $rps->deskripsi_rps ?? null;
        if (! str_ends_with($desRPS, '.') && ! empty($desRPS)) {
            $desRPS .= '.';
        }
        
        $data = [
            'id' => $rps->id,
            'rps_id' => $rps->id,
            'kode_blok' => $rps->kode_blok ?? null,
            'kode_rps' => $rps->kode ?? null,
            'nama_rps' => $rps->rps ?? null,
            'deskripsi' => $desRPS,
            'akademik' => $rps->akademik ?? null,
            'is_draf' => $rps->is_draf ?? false,
            'count_scpmk' => $rps->count_scpmk ?? null,
            'bobot_uts' => $rps->bobot_uts ?? null,
            'bobot_uas' => $rps->bobot_uas ?? null,
            'total_bobot' => $rps->sks ?? null,
            
            'fakultas' => $prodi?->dp_rel?->fk_rel?->nama_fk ?? '-',
            'departemen' => $prodi?->dp_rel?->nama_dp ?? '-',
            'prodi' => $prodi?->prodi ?? '-',

            'mk_id' => $mk->id ?? null,
            'kode_mk' => $rps->kode_mk,
            'nama_mk' => $mk->nama_mk,
            'level_mk' => $mk->level_mk ?? null,
            'bahan_kajian' => $mk?->bahan_kajian ?? '-',
            'sks' => $mk->sks_tm ?? $mk->sks_pl ?? $mk->sks_sm ?? '-',
            'sks_pr' => $mk?->sks_pr ?? '-',
            'semester' => $mk?->semester ?? '-',
            'revisi' => $rps->revisi_day ?? '-',

            'bobot_uts' => $rps->bobot_uts ?? '-',
            'bobot_uas' => $rps->bobot_uas ?? '-',

            // 'cpl' => $rps->cpls->map(function ($c) {
            //     return trim(($c->kode ?? '').': '.($c->deskripsi ?? ''));
            // })->implode("\n"),
            'cpmk' => $rps->cpmks->map(function ($c) {
                return trim(($c->kode ?? '').': '.($c->deskripsi_cpl ?? ''));
            })->implode("\n"),

            'tim_pengajar_label' => $dosenCount === 1 ? 'Dosen Pengampu' : 'Tim Pengajar',
            'tim_pengajar' => $timPengajar,
            'ketua_tim_pengajar' => $ketua ?? null,
            'instruktur' => $instruktur ?: null,
            'total_sks' => $rps->sks ?? '-',

            'program_pembelajaran' => $this->buildProgramPembelajaranRows($rps),
            'referensi' => $this->collectReferensiByPriority($rps),
        ];

        return $data;
    }

    private function collectReferensiByPriority(RPS $rps): string
    {
        $seenIds = [];
        $referensi = [];

        $rpsRefs = $rps->refs ?? collect();
        foreach ($rpsRefs as $ref) {
            if (! in_array($ref->id, $seenIds, true)) {
                $seenIds[] = $ref->id;
                $referensi[] = $this->formatReferensiCitation($ref);
            }
        }

        $cpmkRefs = collect($rps->cpmks ?? [])->flatMap(function ($cpmk) {
            return $cpmk->refs ?? [];
        })->unique('id');

        foreach ($cpmkRefs as $ref) {
            if (! in_array($ref->id, $seenIds, true)) {
                $seenIds[] = $ref->id;
                $referensi[] = $this->formatReferensiCitation($ref);
            }
        }

        $scpmkRefs = collect($rps->cpmks ?? [])->flatMap(function ($cpmk) {
            return $cpmk->scpmks ?? [];
        })->flatMap(function ($scpmk) {
            return $scpmk->refs ?? [];
        })->unique('id');

        foreach ($scpmkRefs as $ref) {
            if (! in_array($ref->id, $seenIds, true)) {
                $seenIds[] = $ref->id;
                $referensi[] = $this->formatReferensiCitation($ref);
            }
        }

        sort($referensi);

        return implode("\n", array_filter($referensi));
    }

    private function formatReferensiCitation($ref): string
    {
        $parts = [];

        if (! empty($ref->penulis ?? null)) {
            $parts[] = $ref->penulis;
        }

        if (! empty($ref->tahun ?? null)) {
            $parts[] = "({$ref->tahun})";
        }

        if (! empty($ref->judul ?? null)) {
            $parts[] = trim($ref->judul);
        }

        if (! empty($ref->penerbit ?? null)) {
            $parts[] = trim($ref->penerbit);
        }

        return trim(implode(' ', $parts));
    }

    private function buildProgramPembelajaranRows(RPS $rps): array
    {
        $rows = [];
        foreach ($rps->cpmks as $cpmk) {
            foreach ($cpmk->scpmks as $scpmk) {
                $rows[] = [
                    'cpmk' => $cpmk->kode,
                    'sub_cpmk' => $scpmk->kode,
                    'materi' => $scpmk->materi,
                    'referensi' => $this->formatSCPMKReferensi($scpmk),
                    'metodologi' => $scpmk->metodologi,
                    'tugas' => $scpmk->tugas,
                    'indikator' => $scpmk->indikator,
                    'bobot' => (float) $scpmk->bobot,
                    'dosen' => $this->getDosenNamesForSubCpmk($rps, $scpmk),
                    'metode' => $scpmk->metode,
                    'is_placeholder' => false,
                ];
            }
        }

        $utsFields = SubCPMK::$UTS_FIELDS;
        $uasFields = SubCPMK::$UAS_FIELDS;

        $hasUTS = collect($rows)->contains(function ($row) {
            return SubCPMK::isUTS($row['metode'] ?? '');
        });

        $hasUAS = collect($rows)->contains(function ($row) {
            return SubCPMK::isUAS($row['metode'] ?? '');
        });



        $finalRows = [];

        // Kondisi 1: Kedua ada (UTS dan UAS) → kembalikan apa adanya
        if ($hasUTS && $hasUAS) {
            $finalRows = $rows;
        }

        // Kondisi 2: Tidak ada salah satunya (hanya UTS atau hanya UAS) → tambah yang kurang
        elseif ($hasUTS && ! $hasUAS) {
            $finalRows = $rows;
            $finalRows[] = $this->createPlaceholderMeetingRow('UAS', $rps->bobot_uas, $rps);
        } elseif (! $hasUTS && $hasUAS) {
            $finalRows = $rows;
            $finalRows[] = $this->createPlaceholderMeetingRow('UTS', $rps->bobot_uts, $rps);
        }

        // Kondisi 3: Tidak ada keduanya → bikin placeholder UTS dan UAS dengan struktur 1-16
        else {
            $pointer = 0;
            for ($meeting = 1; $meeting <= 16; $meeting++) {
                if ($meeting === 8) {
                    $finalRows[] = $this->createPlaceholderMeetingRow('UTS', $rps->bobot_uts, $rps);

                    continue;
                }
                if ($meeting === 16) {
                    $finalRows[] = $this->createPlaceholderMeetingRow('UAS', $rps->bobot_uas, $rps);

                    continue;
                }
                if (isset($rows[$pointer])) {
                    $finalRows[] = $rows[$pointer++];
                }
            }
        }

        $finalRows = $this->normalizeProgramPembelajaranBobot($finalRows, $rps);

        $rpsDosens = $rps->dosens->pluck('name')->filter()->values()->toArray();
        $allAssigned = collect($finalRows)->where('is_placeholder', false)->pluck('dosen')->flatten()->unique()->filter()->values()->toArray();
        $unassigned = collect($rpsDosens)->diff($allAssigned)->values()->toArray();

        foreach ($finalRows as &$row) {
            if ($row['is_placeholder']) {
                $row['dosen'] = $rpsDosens;
            } else {
                $row['dosen'] = array_merge($row['dosen'], $unassigned);
                $row['dosen'] = array_unique($row['dosen']);
            }

            if ($row['dosen'] === $rpsDosens) {
                $row['dosen'] = count($rpsDosens) === 1 ? $rpsDosens : ['Tim Pengajar'];
            }

            $row['dosen'] = $this->formatDosenNames($row['dosen']);
            $row['bobot'] = $this->formatBobot($row['bobot']);
        }

        return $finalRows;
    }

    private function createPlaceholderMeetingRow(string $type, $weight, RPS $rps): array
    {
        return [
            'cpmk' => '',
            'sub_cpmk' => strtoupper($type),
            'materi' => '',
            'referensi' => '',
            'metodologi' => '',
            'tugas' => '',
            'indikator' => '',
            'bobot' => (float) $weight,
            'dosen' => $rps->dosens->pluck('name')->filter()->values()->toArray(),
            'metode' => $type,
            'is_placeholder' => true,
        ];
    }

    private function normalizeProgramPembelajaranBobot(array $rows, RPS $rps): array
    {
        $totalBobot = (float) ($rps->total_bobot ?? 0);

        if ($totalBobot < 80 || $totalBobot > 200 || $totalBobot === 0) {
            return $rows;
        }

        $currentTotal = array_sum(array_map(fn ($row) => (float) ($row['bobot'] ?? 0), $rows));
        if ($currentTotal <= 0) {
            return $rows;
        }

        $scale = 100 / $currentTotal;
        foreach ($rows as &$row) {
            $row['bobot'] = (float) ($row['bobot'] ?? 0) * $scale;
        }

        $weights = array_map(fn ($row) => (float) ($row['bobot'] ?? 0), $rows);
        if (count($weights) > 0) {
            $lastIndex = count($weights) - 1;
            $sumBeforeLast = array_sum(array_slice($weights, 0, $lastIndex));
            $rows[$lastIndex]['bobot'] = max(0.0, 100 - $sumBeforeLast);
        }

        return $rows;
    }

    private function formatSCPMKReferensi($scpmk): string
    {
        return collect($scpmk->refs ?? [])->map(function ($ref) {
            return trim(($ref->penulis_tahun ?? '').' '.($ref->judul ?? '').' '.($ref->penerbit ?? ''));
        })->filter()->implode("\n");
    }

    private function getDosenNamesForSubCpmk(RPS $rps, $scpmk): array
    {
        $assigned = collect($scpmk->dosens ?? [])->filter(function ($d) use ($rps) {
            return (int) ($d->pivot->rps_id ?? 0) === $rps->id;
        })->pluck('name')->filter()->values();

        return $assigned->toArray();
    }

    private function formatDosenNames(array $names): string
    {
        if (empty($names)) {
            return 'Tim Pengajar';
        }

        if (count($names) === 1) {
            return $names[0];
        }

        // Jika ada 'Tim', return 'Tim'
        if (in_array('Tim Pengajar', $names)) {
            return 'Tim Pengajar';
        }

        // Lebih dari 1, kembalikan nama dosen dalam baris baru.
        return implode("\n", $names);
    }

    private function formatBobot($value): string
    {
        // Jika null atau kosong, beri default 0%
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return '0%';
        }

        // Jika sudah ada tanda %, bersihkan dulu untuk diformat ulang atau langsung kembalikan
        $cleanValue = str_replace('%', '', (string) $value);
        $cleanValue = str_replace(',', '.', $cleanValue); // Pastikan format desimal titik untuk float

        if (! is_numeric($cleanValue)) {
            return '0%';
        }

        // Format angka: 15.00 -> 15, 15.50 -> 15,5
        $formatted = rtrim(rtrim(number_format((float) $cleanValue, 2, ',', '.'), '0'), ',');

        return $formatted.'%';
    }
}
