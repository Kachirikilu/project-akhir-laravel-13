<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Livewire\Global\HasToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait WithNilaiExcelImport
{
    use HasToast;

    public $noPreview = true;

    public $uploadedFileNames = [];

    public function togglePreview()
    {
        $this->noPreview = ! $this->noPreview;
    }

    public function updatedExcelNilaiFile()
    {
        if (! $this->excel_nilai_file) {
            return;
        }
        $files = is_array($this->excel_nilai_file) ? $this->excel_nilai_file : [$this->excel_nilai_file];
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            if (!in_array($fileName, $this->uploadedFileNames)) {
                $this->uploadedFileNames[] = $fileName;
                
                try {
                    $this->importNilaiExcel($file);
                } catch (\Throwable $e) {
                    $this->toast(text: "Gagal memproses {$fileName}: ".$e->getMessage(), variant: 'danger');
                }
            }
        }
    }
    protected function parseNilaiRow(
        array $row,
        array $subCpmkColumns,
        array $data_index,
    ): array {
        $subCpmk = [];
        foreach ($subCpmkColumns as $col => $meta) {
            $nilaiRaw = $row[$col] ?? '';
            $nilaiFix = is_numeric($nilaiRaw) ? (float) $nilaiRaw : null;
            $subCpmk[] = [
                'cpmk' => $meta['cpmk'],
                'kode_scpmk' => $meta['kode_scpmk'],
                'pertemuan' => $meta['pertemuan'],
                'bobot' => $meta['bobot'],
                'nilai' => $nilaiFix,
            ];
        }

        return [
            'kode_rps' => trim((string) ($row[$data_index['kode_rps']] ?? '')),
            'nama_mk' => trim((string) ($row[$data_index['mk']] ?? '')),
            'kode_jadwal' => trim((string) ($row[$data_index['kode_jadwal']] ?? '')),
            'nim' => trim((string) ($row[$data_index['nim']] ?? '')),
            'nama' => trim((string) ($row[$data_index['nama']] ?? '')),
            'angkatan' => trim((string) ($row[$data_index['angkatan']] ?? '')),
            'sub_cpmk' => $subCpmk,
        ];
    }

    private function getColumnIndexes(array $header1): array
    {
        $indexes = [
            'kode_rps' => $this->getColumnIndex($header1, ['kode rps', 'rps']),
            'kode_jadwal' => $this->getColumnIndex($header1, ['nama kelas', 'kode kelas', 'kode jadwal', 'keals jadwal', 'kode jadwal kelas']),
            'mk' => $this->getColumnIndex($header1, ['nama mk', 'nama mata kuliah', 'mata kuliah']),
            'nim' => $this->getColumnIndex($header1, ['nim', 'id mahasiswa', 'nomor induk mahasiswa']),
            'nama' => $this->getColumnIndex($header1, ['nama mahasiswa', 'nama mhs']),
            'angkatan' => $this->getColumnIndex($header1, ['angkatan', 'tahun masuk', 'angkatan mahasiswa']),
        ];

        $foundIndexes = array_filter($indexes, fn ($i) => is_int($i));

        if (! empty($foundIndexes)) {
            $indexes['nilai'] = max($foundIndexes) + 1;
            $indexes['nilai_max'] = $indexes['nilai'] + 16;
        } else {
            $indexes['nilai'] = 0;
            $indexes['nilai_max'] = 16;
        }

        return $indexes;
    }

    private function getColumnIndex(array $header, array $aliases): int|false
    {
        $aliases = array_map(
            fn ($alias) => Str::lower(trim($alias)),
            $aliases
        );

        return collect($header)->search(function ($value) use ($aliases) {
            $value = Str::lower(trim((string) $value));

            return in_array($value, $aliases, true);
        });
    }

    public function importNilaiExcel()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        $this->reset(['parsedNilaiRows', 'rowNilaiErrors']);
        $this->setPage(1, 'excelPage');

        $this->validate([
            'excel_nilai_file' => 'required|array',
            'excel_nilai_file.*' => 'file|mimes:xlsx,xls|max:10240',
        ]);

        $this->parsedNilaiRows = [];

        foreach ($this->excel_nilai_file as $singleFile) {

            $spreadsheet = IOFactory::load($singleFile->getRealPath());
            $allSheets = $spreadsheet->getAllSheets();

            foreach ($allSheets as $worksheet) {

                $allData = $worksheet->toArray(null, true, false, false);

                if (empty($allData) || count($allData) < 4) {
                    continue;
                }

                $header1 = $allData[0] ?? []; // CPMK
                $header2 = $allData[1] ?? []; // SCPMK
                $header3 = $allData[2] ?? []; // Bobot

                $data_index = $this->getColumnIndexes($header1);

                if ($data_index['kode_rps'] === false || $data_index['kode_jadwal'] === false) {
                    continue;
                }
                $subCpmkColumns = [];
                $currentCPMK = null;

                for ($col = $data_index['nilai']; $col < $data_index['nilai_max']; $col++) {
                    $rawCpmk = trim((string) ($header1[$col] ?? ''));
                    if ($rawCpmk !== '' && ! str_contains(strtolower($rawCpmk), 'rekap')) {
                        $currentCPMK = $rawCpmk;
                    }
                    $rawSub = trim((string) ($header2[$col] ?? ''));

                    if ($rawSub === '' || str_contains(strtolower($rawCpmk), 'rekap')) {
                        continue;
                    }

                    preg_match('/([A-Za-z0-9\-]+)\s*\(P\-(\d+)\)/i', $rawSub, $matches);

                    $kodeSCPMK = $matches[1] ?? $rawSub;
                    $pertemuan = $matches[2] ?? null;
                    $bobot = (float) ($header3[$col] ?? 0);
                    if ($bobot > 1) {
                        $bobot /= 100;
                    }

                    $subCpmkColumns[$col] = [
                        'cpmk' => $currentCPMK,
                        'kode_scpmk' => $kodeSCPMK,
                        'pertemuan' => $pertemuan,
                        'bobot' => $bobot,
                    ];
                }

                $this->parsedNilaiHeaders = collect($subCpmkColumns)
                    ->map(fn ($item) => [
                        'cpmk' => $item['cpmk'],
                        'sub_cpmk' => $item['kode_scpmk'],
                        'pertemuan' => $item['pertemuan'],
                        'bobot' => $item['bobot'],
                    ])
                    ->values()
                    ->toArray();

                $dataRows = array_slice($allData, 3);
                foreach ($dataRows as $row) {
                    if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->isEmpty()) {
                        continue;
                    }
                    // $validatedData = $this->inputModalNilai($row);

                    $parsed = $this->parseNilaiRow(
                        $row,
                        $subCpmkColumns,
                        $data_index,
                    );
                    $parsed['_index'] = count($this->parsedNilaiRows);
                    $this->parsedNilaiRows[] = $parsed;
                }
            }
        }

        $this->toast(
            text: 'Semua file Excel berhasil dimuat. Silakan periksa nilai mahasiswa!'
        );
    }

    public function directImportFromWhatsApp($fileExcel, $user)
    {
        Log::info('=== MEMULAI PROSES IMPOR LANGSUNG VIA WHATSAPP ===');

        if (! $user || ! ($user->admin || $user->dosen)) {
            return [
                'status' => true,
                'head' => '*❌ Akses Gagal!*',
                'message' => 'Mahasiswa tidak memiliki akses untuk menginput File Excel!',
            ];
        }
        // Auth::login($user);

        try {
            $spreadsheet = IOFactory::load($fileExcel->getRealPath());

            $allSheets = $spreadsheet->getAllSheets();

            $successCount = 0;
            $errors = [];
            $totalSheetsProcessed = 0;

            foreach ($allSheets as $worksheet) {
                $sheetName = $worksheet->getTitle();
                $allData = $worksheet->toArray(null, true, false, false);

                if (empty($allData) || count($allData) < 4) {
                    continue;
                }

                $header1 = $allData[0] ?? [];
                $header2 = $allData[1] ?? [];
                $header3 = $allData[2] ?? [];

                $data_index = $this->getColumnIndexes($header1);

                if ($data_index['kode_rps'] === false || $data_index['kode_jadwal'] === false) {
                    Log::warning("Sheet '{$sheetName}' dilewati karena struktur header tidak sesuai!");

                    continue;
                }

                $totalSheetsProcessed++;

                $subCpmkColumns = [];
                $currentCPMK = null;

                for ($col = $data_index['nilai']; $col < $data_index['nilai_max']; $col++) {
                    $rawCpmk = trim((string) ($header1[$col] ?? ''));
                    if ($rawCpmk !== '' && ! str_contains(strtolower($rawCpmk), 'rekap')) {
                        $currentCPMK = $rawCpmk;
                    }
                    $rawSub = trim((string) ($header2[$col] ?? ''));

                    if ($rawSub === '' || str_contains(strtolower($rawSub), 'rekap')) {
                        continue;
                    }
                    preg_match('/([A-Za-z0-9\-]+)\s*\(P\-(\d+)\)/i', $rawSub, $matches);

                    $kodeSCPMK = $matches[1] ?? $rawSub;
                    $pertemuan = $matches[2] ?? null;
                    $bobotRaw = $header3[$col] ?? 0;
                    $bobot = (float) $bobotRaw;
                    if ($bobot > 1) {
                        $bobot /= 100;
                    }

                    // 5. Simpan ke daftar kolom
                    $subCpmkColumns[$col] = [
                        'cpmk' => $currentCPMK,
                        'kode_scpmk' => $kodeSCPMK,
                        'pertemuan' => $pertemuan,
                        'bobot' => $bobot,
                    ];
                }

                $dataRows = array_slice($allData, 3);
                foreach ($dataRows as $row) {
                    if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->isEmpty()) {
                        continue;
                    }
                    $parsed = $this->parseNilaiRow(
                        $row,
                        $subCpmkColumns,
                        $data_index,
                    );
                    try {
                        $this->saveNilaiFromExcel($parsed);
                        $successCount++;
                    } catch (\Throwable $e) {
                        $errors[] = "[Sheet: {$sheetName}] NIM {$parsed['nim']}: ".$e->getMessage();
                    }
                }
            }

            // Auth::logout();

            if ($totalSheetsProcessed === 0) {
                throw new \Exception('Tidak ada halaman (sheet) dalam file Excel yang sesuai dengan standar format format nilai!');
            }

            $failCount = count($errors);
            $namaFileAsli = $fileExcel->getClientOriginalName();

            $resMessage = "📄 File: ```{$namaFileAsli}```\n".
                          "📊 Total Sheet Diproses: *{$totalSheetsProcessed}* halaman\n\n".
                          "✅ Sukses disimpan: *{$successCount}* mahasiswa\n".
                          "❌ Gagal diproses: *{$failCount}* baris\n";

            if ($failCount > 0) {
                $resMessage .= "\n*Daftar Eror:*\n".implode("\n", array_slice($errors, 0, 5));
            }

            return ['status' => true, 'head' => '*🟢 Hasil Impor Nilai Excel via WhatsApp*', 'message' => $resMessage];

        } catch (\Throwable $e) {
            Log::error('Gagal memproses excel langsung: '.$e->getMessage());

            return ['status' => true, 'head' => '❌ *Gagal Memproses File!*', 'message' => ''.$e->getMessage()];
        }
    }
}
