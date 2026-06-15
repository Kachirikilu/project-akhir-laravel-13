<?php

namespace App\Livewire\Staff\OBEManagement\RPSManagement;

use App\Models\Akademik\RPS;
use Illuminate\Support\Facades\DB;

trait WithRPSPertemuan
{
    private function parsePertemuanDosen(array $pertemuanDosen, array $selectedDosenIds, array $cpmkSubItems, array $dosenItemsArray = []): array
    {
        $parsed = [];
        $errors = [];
        $scpmkIds = $this->flattenScpmkIds($cpmkSubItems);
        $flags = $this->getPertemuanFlags($cpmkSubItems);
        $meetingMap = $this->buildPertemuanToScpmkMap(count($scpmkIds), $scpmkIds, $flags['hasUTS'], $flags['hasUAS']);

        $selectedDosenIds = array_map('intval', $selectedDosenIds);

        $pertemuanDosen = $this->normalizePertemuanDosenKeys($pertemuanDosen, $selectedDosenIds);

        foreach ($pertemuanDosen as $dosenId => $rawValue) {
            $nip = $this->getDosenNipForError((int) $dosenId, $dosenItemsArray);

            if (! in_array((int) $dosenId, $selectedDosenIds, true)) {
                $errors[] = "Dosen dengan NIP {$nip} tidak dipilih atau tidak valid!";

                continue;
            }

            $rawValue = trim((string) ($rawValue ?? ''));
            if ($rawValue === '') {
                $parsed[(int) $dosenId] = [];

                continue;
            }

            $numbers = [];
            $parts = preg_split('/\s*,\s*/u', $rawValue, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($parts as $segment) {
                if (preg_match('/^(\d+)\s*[-–]\s*(\d+)$/u', $segment, $match)) {
                    $start = (int) $match[1];
                    $end = (int) $match[2];

                    if ($start > $end) {
                        $errors[] = "Range pertemuan tidak valid untuk Dosen ID {$dosenId}: {$segment}.";

                        continue;
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        $numbers[] = $i;
                    }

                    continue;
                }

                if (preg_match('/^\d+$/', $segment)) {
                    $numbers[] = (int) $segment;

                    continue;
                }

                $errors[] = "Format pertemuan tidak valid untuk Dosen NIP {$nip}: {$segment}.";
            }

            $numbers = array_values(array_unique($numbers));
            sort($numbers);

            // Jika semua pertemuan yang tersedia dipilih, anggap tidak ada assignment khusus
            $availableMeetings = array_keys(array_filter($meetingMap, fn($v) => $v !== null));
            if (empty(array_diff($availableMeetings, $numbers))) {
                $parsed[(int) $dosenId] = [];
                continue;
            }

            $mappedScpmk = [];

            foreach ($numbers as $meeting) {
                if ($meeting < 1 || $meeting > 16) {
                    $errors[] = "Nilai pertemuan untuk Dosen NIP {$nip} harus antara 1 dan 16!";

                    continue;
                }

                if (! array_key_exists($meeting, $meetingMap) || $meetingMap[$meeting] === null) {
                    if (in_array($meeting, [8, 16], true)) {
                        continue;
                    }

                    $errors[] = "Pertemuan {$meeting} untuk Dosen NIP {$nip} tidak tersedia untuk jumlah Sub-CPMK saat ini!";

                    continue;
                }

                $mappedScpmk[$meeting] = $meetingMap[$meeting];
            }

            $parsed[(int) $dosenId] = $mappedScpmk;
        }

        return ['data' => $parsed, 'errors' => array_values(array_unique($errors))];
    }

    private function normalizePertemuanDosenKeys(array $pertemuanDosen, array $selectedDosenIds): array
    {
        if (empty($pertemuanDosen) || empty($selectedDosenIds)) {
            return $pertemuanDosen;
        }

        $keys = array_keys($pertemuanDosen);
        $isSequential = $keys === range(0, count($keys) - 1);
        $allKeysMatchSelected = true;
        foreach ($keys as $key) {
            if (! is_numeric($key) || ! in_array((int) $key, $selectedDosenIds, true)) {
                $allKeysMatchSelected = false;
                break;
            }
        }

        if (! $allKeysMatchSelected && $isSequential) {
            $mapped = [];
            foreach (array_values($pertemuanDosen) as $idx => $value) {
                if (! isset($selectedDosenIds[$idx])) {
                    continue;
                }
                $mapped[$selectedDosenIds[$idx]] = $value;
            }

            if (! empty($mapped)) {
                return $mapped;
            }
        }

        if (! $allKeysMatchSelected) {
            $filtered = [];
            foreach ($pertemuanDosen as $key => $value) {
                if (is_numeric($key) && in_array((int) $key, $selectedDosenIds, true)) {
                    $filtered[(int) $key] = $value;
                }
            }
            if (! empty($filtered)) {
                return $filtered;
            }
        }

        return $pertemuanDosen;
    }

    private function getDosenNipForError(int $dosenId, array $dosenItemsArray = []): string
    {
        $source = [];

        if (! empty($dosenItemsArray) && is_array($dosenItemsArray)) {
            $source = $dosenItemsArray;
        } elseif (! empty($this->dosen_items_array) && is_array($this->dosen_items_array)) {
            $source = $this->dosen_items_array;
        }

        $detail = collect($source)->firstWhere('id', $dosenId);
        if (! empty($detail['kode'])) {
            return $detail['kode'];
        }
        if (! empty($detail['nip'])) {
            return $detail['nip'];
        }

        return (string) $dosenId;
    }

    private function flattenScpmkIds(array $cpmkSubItems): array
    {
        $scpmkIds = [];

        foreach ($cpmkSubItems as $group) {
            foreach ($group['scpmk'] ?? [] as $scpmk) {
                if (! empty($scpmk['id'])) {
                    $scpmkIds[] = $scpmk['id'];
                }
            }
        }

        return array_values($scpmkIds);
    }

    private function getPertemuanFlags(array $cpmkSubItems): array
    {
        $hasUTS = false;
        $hasUAS = false;

        foreach ($cpmkSubItems as $group) {
            foreach ($group['scpmk'] ?? [] as $scpmk) {
                $method = strtoupper(trim((string) ($scpmk['metode'] ?? '')));

                if ($method === 'UTS') {
                    $hasUTS = true;
                }

                if (in_array($method, ['UAS', 'LAPORAN AKHIR', 'HASIL PROJEK', 'HASIL PROYEK'], true)) {
                    $hasUAS = true;
                }
            }
        }

        return ['hasUTS' => $hasUTS, 'hasUAS' => $hasUAS];
    }

    private function buildPertemuanToScpmkMap(int $totalScpmk, array $scpmkIds, bool $hasUTS, bool $hasUAS): array
    {
        $map = [];

        if ($totalScpmk === 16) {
            for ($meeting = 1; $meeting <= 16; $meeting++) {
                $map[$meeting] = $scpmkIds[$meeting - 1] ?? null;
            }

            return $map;
        }

        $fillIndex = 0;
        for ($meeting = 1; $meeting <= 16; $meeting++) {
            if (! $hasUTS && $meeting === 8) {
                $map[$meeting] = null;

                continue;
            }

            if (! $hasUAS && $meeting === 16) {
                $map[$meeting] = null;

                continue;
            }

            $fillIndex++;
            $map[$meeting] = ($fillIndex <= $totalScpmk)
                ? ($scpmkIds[$fillIndex - 1] ?? null)
                : null;
        }

        return $map;
    }

    private function syncDosenPertemuanToScpmk(RPS $rps, array $pertemuanDosen, array $cpmkSubItems): void
    {
        DB::table('dosen_pivot_scpmk')->where('rps_id', $rps->id)->delete();

        $insertRows = [];
        foreach ($pertemuanDosen as $dosenId => $scpmkIdsForDosen) {
            foreach ($scpmkIdsForDosen as $meeting => $scpmkId) {
                if (empty($scpmkId)) {
                    continue;
                }

                $insertRows[] = [
                    'rps_id' => $rps->id,
                    'dosen_id' => $dosenId,
                    'scpmk_id' => $scpmkId,
                    'sort_order' => $meeting,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($insertRows)) {
            DB::table('dosen_pivot_scpmk')->insert($insertRows);
        }
    }

    private function loadPertemuanDosenFromRps(int $rpsId, array $cpmkSubItems, array $selectedDosenIds): array
    {
        $rows = DB::table('dosen_pivot_scpmk')
            ->where('rps_id', $rpsId)
            ->get();

        $flags = $this->getPertemuanFlags($cpmkSubItems);
        $selectedDosenIdsInt = array_map('intval', $selectedDosenIds);

        $pertemuan = [];

        foreach ($rows as $row) {
            $dosenId = (int) $row->dosen_id;
            $order = (int) $row->sort_order;

            if (! in_array($dosenId, $selectedDosenIdsInt, true)) {
                continue;
            }

            $pertemuan[$dosenId][] = $order;
        }

        $result = [];
        foreach ($selectedDosenIdsInt as $id) {
            $numbers = isset($pertemuan[$id]) ? array_values(array_unique($pertemuan[$id])) : [];

            // --- LOGIKA OTOMATIS 8 & 16 ---
            if (! empty($numbers)) {
                if (! $flags['hasUTS'] && ! in_array(8, $numbers)) {
                    $numbers[] = 8;
                }
                if (! $flags['hasUAS'] && ! in_array(16, $numbers)) {
                    $numbers[] = 16;
                }
            }
            // ------------------------------

            sort($numbers);
            $result[$id] = $this->formatPertemuanRanges($numbers);
        }

        return $result;
    }

    private function formatPertemuanRanges(array $numbers): string
    {
        if (empty($numbers)) {
            return '';
        }

        $ranges = [];
        $start = $prev = null;

        foreach ($numbers as $number) {
            if ($start === null) {
                $start = $prev = $number;

                continue;
            }

            if ($number === $prev + 1) {
                $prev = $number;

                continue;
            }

            $ranges[] = $start === $prev ? (string) $start : "{$start}-{$prev}";
            $start = $prev = $number;
        }

        if ($start !== null) {
            $ranges[] = $start === $prev ? (string) $start : "{$start}-{$prev}";
        }

        return implode(', ', $ranges);
    }
}
