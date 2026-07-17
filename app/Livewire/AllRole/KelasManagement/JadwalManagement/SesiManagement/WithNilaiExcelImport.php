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

            foreach ($this->parseNilaiExcel($singleFile) as $sheet) {

                $this->parsedNilaiHeaders = $sheet['headers'];

                foreach ($sheet['rows'] as $parsed) {

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

            foreach ($this->parseNilaiExcel($fileExcel) as $sheet) {
                $totalSheetsProcessed++;
                foreach ($sheet['rows'] as $parsed) {
                    try {
                        $prepared = $this->prepareDataForValidation($parsed);
                        $this->processParsedNilai($prepared);
                        
                        $successCount++;
                    } catch (ValidationException $e) {
                        $errors[$sheet['sheet']][] = [
                            'nim' => $parsed['nim'],
                            'message' => implode(', ', $e->validator->errors()->all()),
                        ];
                    } catch (\Throwable $e) {
                        $errors[$sheet['sheet']][] = [
                            'nim' => $parsed['nim'],
                            'message' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Auth::logout();

            if ($totalSheetsProcessed === 0) {
                throw new \Exception('Tidak ada halaman (sheet) dalam file Excel yang sesuai dengan standar format format nilai!');
            }

            $failCount = collect($errors)->flatten(1)->count();
            $namaFileAsli = $fileExcel->getClientOriginalName();

            $resMessage = "📄 File: ```{$namaFileAsli}```\n".
                          "📊 Total Sheet Diproses: *{$totalSheetsProcessed}* halaman\n\n".
                          "✅ Sukses disimpan: *{$successCount}* mahasiswa\n".
                          "❌ Gagal diproses: *{$failCount}* baris\n";

            if ($failCount > 0) {
                $resMessage .= "\n*Daftar Error:*\n";
                foreach ($errors as $sheetName => $sheetErrors) {
                    $resMessage .= "\n📄 *Sheet: {$sheetName}*\n";
                    foreach ($sheetErrors as $i => $err) {
                        $no = $i + 1;
                        $resMessage .= "{$no}. NIM ```{$err['nim']}```\n";
                        $resMessage .= "- {$err['message']}\n";
                    }
                }
            }

            return ['status' => true, 'head' => '*🟢 Hasil Impor Nilai Excel via WhatsApp*', 'message' => $resMessage];

        } catch (\Throwable $e) {
            Log::error('Gagal memproses excel langsung: '.$e->getMessage());

            return ['status' => true, 'head' => '❌ *Gagal Memproses File!*', 'message' => ''.$e->getMessage()];
        }
    }

    private function processParsedNilai(array $parsed): void
    {
        $validatedData = $this->inputModalNilai($parsed);
        $this->saveNilaiFromExcel($validatedData);
    }

    private function prepareDataForValidation($parsed)
    {
        return array_merge([
            'nilai_angka' => 0,
            'nilai_index' => 0,
            'nilai_mutu'  => 'E',
            '_index'      => count($this->parsedNilaiRows),
        ], $parsed);
    }

    private function parseNilaiExcel($excelFile): array
    {
        $spreadsheet = IOFactory::load($excelFile->getRealPath());

        $result = [];

        foreach ($spreadsheet->getAllSheets() as $worksheet) {

            $sheetName = $worksheet->getTitle();
            $allData = $worksheet->toArray(null, true, false, false);

            if (empty($allData) || count($allData) < 4) {
                continue;
            }

            $header1 = $allData[0] ?? [];
            $header2 = $allData[1] ?? [];
            $header3 = $allData[2] ?? [];

            $dataIndex = $this->getColumnIndexes($header1);

            if ($dataIndex['kode_rps'] === false || $dataIndex['kode_jadwal'] === false) {
                continue;
            }

            $subCpmkColumns = $this->extractSubCpmkColumns(
                $header1,
                $header2,
                $header3,
                $dataIndex
            );

            $headers = collect($subCpmkColumns)
                ->map(fn($item) => [
                    'cpmk' => $item['cpmk'],
                    'sub_cpmk' => $item['kode_scpmk'],
                    'pertemuan' => $item['pertemuan'],
                    'bobot' => $item['bobot'],
                ])
                ->values()
                ->toArray();

            $rows = [];

            foreach (array_slice($allData, 3) as $row) {

                if (collect($row)->filter(fn($v) => trim((string)$v) !== '')->isEmpty()) {
                    continue;
                }

                $rows[] = $this->parseNilaiRow(
                    $row,
                    $subCpmkColumns,
                    $dataIndex
                );
            }

            $result[] = [
                'sheet' => $sheetName,
                'headers' => $headers,
                'rows' => $rows,
            ];
        }

        return $result;
    }

    private function extractSubCpmkColumns(
        array $header1,
        array $header2,
        array $header3,
        array $dataIndex
    ): array {

        $subCpmkColumns = [];
        $currentCPMK = null;

        for ($col = $dataIndex['nilai']; $col < $dataIndex['nilai_max']; $col++) {

            $rawCpmk = trim((string)($header1[$col] ?? ''));

            if ($rawCpmk !== '' && !str_contains(strtolower($rawCpmk), 'rekap')) {
                $currentCPMK = $rawCpmk;
            }

            $rawSub = trim((string)($header2[$col] ?? ''));

            if ($rawSub === '' || str_contains(strtolower($rawCpmk), 'rekap')) {
                continue;
            }

            preg_match('/([A-Za-z0-9\-]+)\s*\(P\-(\d+)\)/i', $rawSub, $matches);

            $kodeSCPMK = $matches[1] ?? $rawSub;
            $pertemuan = $matches[2] ?? null;

            $bobot = (float)($header3[$col] ?? 0);

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

        return $subCpmkColumns;
        }
}
