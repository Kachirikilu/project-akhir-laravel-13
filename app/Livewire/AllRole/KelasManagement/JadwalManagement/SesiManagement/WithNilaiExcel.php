<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\MultiNilaiExport;
use App\Exports\NilaiExport;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\HasGetByKode;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use App\Models\Kelas\Kelas;
// use Illuminate\Support\LazyCollection;
// use App\Models\Akademik\RPS;
use App\Models\Kelas\KelasJadwal;
use App\Models\Penilaian\NilaiMahasiswa;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

trait WithNilaiExcel
{
    use HasToast;
    use HasGetByKode;
    use WithFileUploads;
    use WithSesiFilters;

    public $showNilaiExcelModal;

    public $excel_nilai_file;

    public array $parsedNilaiRows = [];

    public array $rowNilaiErrors = [];

    public $excelNilaiPerPage = 30;

    public array $parsedNilaiHeaders = [];

    public array $jadwalQueue = [];

    public function exportNilaiExcel($idJadwal = null)
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (! empty($idJadwal)) {
            $jadwal = KelasJadwal::where('id', $idJadwal)->first();

            return $this->prosesNilaiExcelTunggal($jadwal);
        }

        if (! empty($this->jadwal)) {
            return $this->prosesNilaiExcelTunggal($this->jadwal);
        }

        if (! empty($this->kelas)) {
            $jadwals = $this->kelas->jadwals()->get();
            if ($jadwals->isEmpty()) {
                $this->toast(text: '⚠️ Tidak ada jadwal aktif untuk kelas ini.', type: 'error');

                return;
            }

            if ($jadwals->count() === 1) {
                return $this->prosesNilaiExcelTunggal($jadwals->first());
            }

            return $this->prosesNilaiExcelMultiSheet($jadwals, $this->kelas);
        }
    }

    private function prosesNilaiExcelMultiSheet($jadwals, $kelas)
    {
        $kode = $kelas->kode;
        $rps = $kelas->kode_rps;
        $mk = $kelas->mk;
        $nowStr = now()->format('Y-m-d');

        $fileName = $kode.'_'.$rps.'_'.$mk.'_'.$nowStr.'.xlsx';
        $fileNameSafe = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
            '-',
            $fileName
        );

        return Excel::download(
            new MultiNilaiExport($jadwals),
            $fileNameSafe,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    private function prosesNilaiExcelTunggal($jadwal)
    {
        $mk = $jadwal->mk;
        $nowStr = now()->format('Y-m-d');

        $fileName = $jadwal->kode.'_'.$jadwal->kode_rps.'_'.$mk.'_'.$nowStr.'.xlsx';
        $fileNameSafe = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
            '-',
            $fileName
        );

        $sheetName = $jadwal->kode;
        $sheetNameSafe = substr(
            str_replace(['*', ':', '?', '/', '\\', '[', ']'], '-', $sheetName),
            0,
            31
        );
        $sheets[] = new NilaiExport($jadwal, $sheetNameSafe);

        return Excel::download(
            new NilaiExport($jadwal, $sheetNameSafe),
            $fileNameSafe,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    // public function exportNilaiExcel()
    // {
    //     if (! $this->AuthCheck('staff')) {
    //         return;
    //     }

    //     if (! (Auth::user()->admin || Auth::user()->dosen)) {
    //         return;
    //     }

    //     if (! empty($this->jadwal)) {
    //         return $this->prosesNilaiExcelTunggal($this->jadwal);
    //     }

    //     if (! empty($this->kelas)) {
    //         $jadwals = $this->kelas->jadwals()->get();
    //         if ($jadwals->count() === 1) {
    //             return $this->prosesNilaiExcelTunggal($jadwals->first());
    //         }

    //         return $this->prosesDownloadZip($jadwals, $this->kelas);
    //     }
    // }

    // private function prosesDownloadZip($jadwals, $kelas)
    // {
    //     $kode = $kelas->kode;
    //     $rps = $kelas->kode_rps;
    //     $mk = $kelas->mk;

    //     $zipName = str_replace(
    //         ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
    //         '-',
    //         $kode.'_'.$rps.'_'.$mk.'_'.now()->format('Y-m-d').'.zip'
    //     );

    //     $tempDir = storage_path('app/temp');
    //     if (! file_exists($tempDir)) {
    //         mkdir($tempDir, 0777, true);
    //     }

    //     $zipPath = $tempDir.'/'.$zipName;

    //     $zip = new ZipArchive;
    //     if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    //         throw new \Exception('Gagal membuat file ZIP di server.');
    //     }

    //     foreach ($jadwals as $jadwal) {
    //         $rawFileName = $jadwal->kode.'_'.$jadwal->kode_rps.'_'.$mk.'_'.now()->format('Y-m-d').'.xlsx';
    //         $fileName = str_replace(
    //             ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
    //             '-',
    //             $rawFileName
    //         );

    //         $excelContent = Excel::raw(
    //             new NilaiExport($jadwal->id),
    //             \Maatwebsite\Excel\Excel::XLSX
    //         );

    //         $zip->addFromString($fileName, $excelContent);
    //     }

    //     $zip->close();

    //     if (! file_exists($zipPath)) {
    //         throw new \Exception('File ZIP gagal digenerate.');
    //     }

    //     return response()->download($zipPath)->deleteFileAfterSend(true);
    // }

    // private function prosesNilaiExcelTunggal($jadwal)
    // {
    //     $mk = $jadwal->mk;
    //     $nowStr = now()->format('Y-m-d');

    //     $fileName = $jadwal->kode.'_'.$jadwal->kode_rps.'_'.$mk.'_'.$nowStr.'.xlsx';
    //     $fileNameSafe = str_replace('/', '-', $fileName);

    //     return Excel::download(new NilaiExport($jadwal->id), $fileNameSafe);
    // }

    private function prosesExcelNilaiForWhatsApp($jadwals, $kelas)
    {
        $kode = $kelas->kode;
        $rps = $kelas->kode_rps;
        $mk = $kelas->mk;

        $zipName = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
            '-',
            $kode.'_'.$rps.'_'.$mk.'_'.now()->format('Y-m-d').'.zip'
        );

        $tempDir = storage_path('app/temp');
        if (! file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $zipPath = $tempDir.'/'.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Gagal membuat file ZIP di server.');
        }

        foreach ($jadwals as $jadwal) {
            $rawFileName = $jadwal->kode.'_'.$jadwal->kode_rps.'_'.$mk.'_'.now()->format('Y-m-d').'.xlsx';
            $fileName = str_replace(
                ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
                '-',
                $rawFileName
            );

            $excelContent = Excel::raw(
                new NilaiExport($jadwal->id),
                \Maatwebsite\Excel\Excel::XLSX
            );

            $zip->addFromString($fileName, $excelContent);
        }

        $zip->close();

        if (! file_exists($zipPath)) {
            throw new \Exception('File ZIP gagal digenerate.');
        }

        $base64Data = base64_encode(file_get_contents($zipPath));
        unlink($zipPath);

        return response()->json([
            'status' => true,
            'head' => '*📦 Berkas ZIP Berhasil Dibuat!*',
            'message' => "Kelas memiliki beberapa jadwal aktif. Berikut paket file ZIP dokumen nilai untuk kelas: *{$kode}*",
            'file_type' => 'zip',
            'file_name' => $zipName,
            'file_base64' => $base64Data,
        ]);
    }

    // public function exportNilaiExcel()
    // {
    //     if (! $this->AuthCheck('staff')) {
    //         return;
    //     }
    //     if (! (Auth::user()->admin || Auth::user()->dosen)) {
    //         return;
    //     }
    //     if (! empty($this->jadwal)) {
    //         return $this->prosesNilaiExcelTunggal($this->jadwal);
    //     }
    //     if (! empty($this->kelas)) {
    //         $this->jadwalQueue = $this->kelas->jadwals()->pluck('id')->toArray();

    //         return $this->downloadNextInQueue();
    //     }
    // }

    // public function downloadNextInQueue()
    // {
    //     if (empty($this->jadwalQueue)) {
    //         return;
    //     }
    //     $currentJadwalId = array_shift($this->jadwalQueue);

    //     $j = KelasJadwal::find($currentJadwalId);
    //     if ($j) {
    //         if (! empty($this->jadwalQueue)) {
    //             $this->dispatch(
    //                 'download-multiple',
    //                 jadwalIds: $this->kelas->jadwals()->pluck('id')->toArray()
    //             );
    //         }

    //         return $this->prosesNilaiExcelTunggal($j);
    //     }
    // }

    public function getPaginatedNilaiRowsProperty()
    {
        $items = collect($this->parsedNilaiRows)
            ->map(function ($row, $index) {

                return array_merge($row, [
                    '_index' => $index,
                ]);
            });

        $page = $this->getPage('excelPage');

        return new LengthAwarePaginator(
            $items->forPage($page, $this->excelNilaiPerPage)->values()->toArray(),
            $items->count(),
            $this->excelNilaiPerPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'excelPage',
            ]
        );
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

        // 1. LOOP UTAMA: Mengulang setiap file berkas yang diunggah
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

                $startNilaiIndex = 6; // G

                $nilaiRPSIndex = collect($header1)->search(function ($v) {
                    return in_array(Str::lower(trim((string) $v)), ['kode rps', 'rps']);
                });

                $nilaiJadwalKelasIndex = collect($header1)->search(function ($v) {
                    return in_array(Str::lower(trim((string) $v)), ['nama kelas', 'kode kelas', 'kode jadwal', 'keals jadwal']);
                });

                $nilaiAngkaIndex = collect($header1)->search(fn ($v) => Str::lower(trim((string) $v)) === 'nilai angka');

                // Jika format sheet ini bukan template nilai, lewati sheet ini
                if ($nilaiRPSIndex === false || $nilaiJadwalKelasIndex === false || $nilaiAngkaIndex === false) {
                    continue;
                }

                $subCpmkColumns = [];
                $currentCPMK = null;

                for ($col = $startNilaiIndex; $col < $nilaiAngkaIndex; $col++) {
                    $rawCpmk = trim((string) ($header1[$col] ?? ''));
                    if ($rawCpmk !== '') {
                        $currentCPMK = $rawCpmk;
                    }

                    $rawSub = trim((string) ($header2[$col] ?? ''));
                    if ($rawSub === '') {
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

                $nilaiIndexIndex = $nilaiAngkaIndex + 1;
                $nilaiMutuIndex = $nilaiAngkaIndex + 2;
                $dataRows = array_slice($allData, 3);

                foreach ($dataRows as $row) {
                    if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->isEmpty()) {
                        continue;
                    }
                    $subCpmk = [];

                    foreach ($subCpmkColumns as $col => $meta) {
                        $nilaiRaw = $row[$col] ?? '';
                        $subCpmk[] = [
                            'cpmk' => $meta['cpmk'],
                            'kode_scpmk' => $meta['kode_scpmk'],
                            'pertemuan' => $meta['pertemuan'],
                            'bobot' => $meta['bobot'],
                            'nilai' => is_numeric($nilaiRaw) ? (float) $nilaiRaw : null,
                        ];
                    }

                    $this->parsedNilaiRows[] = [
                        '_index' => count($this->parsedNilaiRows),
                        'kode_rps' => trim((string) ($row[$nilaiRPSIndex] ?? '')),
                        'nama_mk' => trim((string) ($row[1] ?? '')),
                        'kode_jadwal' => trim((string) ($row[2] ?? '')),
                        'nim' => trim((string) ($row[3] ?? '')),
                        'nama' => trim((string) ($row[4] ?? '')),
                        'angkatan' => trim((string) ($row[5] ?? '')),
                        'sub_cpmk' => $subCpmk,
                        'nilai_angka' => is_numeric($row[$nilaiAngkaIndex] ?? null) ? (float) $row[$nilaiAngkaIndex] : 0,
                        'nilai_index' => is_numeric($row[$nilaiIndexIndex] ?? null) ? (float) $row[$nilaiIndexIndex] : null,
                        'nilai_mutu' => strtoupper(trim((string) ($row[$nilaiMutuIndex] ?? ''))),
                        'role' => 'mahasiswa',
                    ];
                }
            } // 🌟 Akhir loop sheet
        } // Akhir loop file

        $this->toast(
            text: 'Semua file Excel berhasil dimuat. Silakan periksa nilai mahasiswa!'
        );
    }

    public function clearNilaiExcelFile()
    {
        $this->excel_nilai_file = null;
        $this->reset([
            'parsedNilaiRows',
            'rowNilaiErrors',
            'parsedNilaiHeaders',
        ]);
        if (method_exists($this, 'setPage')) {
            $this->setPage(1, 'excelPage');
        }
        $this->dispatch('reset-file-input', id: 'excel_nilai_file');
        $this->toast(type: 'info', text: 'File berhasil dihapus.');
    }

    public function updatedExcelNilaiFile()
    {
        if (! $this->excel_nilai_file) {
            return;
        }
        try {
            $this->importNilaiExcel();
        } catch (\Throwable $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');
        }
    }

    public function removeParsedNilaiRow($index)
    {
        if (isset($this->parsedNilaiRows[$index])) {
            unset($this->parsedNilaiRows[$index]);
            $this->parsedNilaiRows = array_values($this->parsedNilaiRows);
            $this->toast(text: 'Baris mahasiswa dihapus!');
        }
    }

    public function recalculateRowNilai(int $rowIndex)
    {
        if (! isset($this->parsedNilaiRows[$rowIndex])) {
            return;
        }

        $row = &$this->parsedNilaiRows[$rowIndex];
        $subCpmks = $row['sub_cpmk'] ?? [];

        $totalNilai = 0;
        $count = 0;
        foreach ($subCpmks as $sub) {
            if (isset($sub['nilai']) && is_numeric($sub['nilai'])) {
                $totalNilai += (float) $sub['nilai'];
            }
            $count++;
        }

        $average = $count > 0 ? ($totalNilai / $count) : 0;
        $average = round($average, 2);

        $row['nilai_angka'] = $average;

        if ($average >= 86) {
            $row['nilai_index'] = 4;
            $row['nilai_mutu'] = 'A';
        } elseif ($average >= 71) {
            $row['nilai_index'] = 3;
            $row['nilai_mutu'] = 'B';
        } elseif ($average >= 56) {
            $row['nilai_index'] = 2;
            $row['nilai_mutu'] = 'C';
        } elseif ($average >= 41) {
            $row['nilai_index'] = 1;
            $row['nilai_mutu'] = 'D';
        } else {
            $row['nilai_index'] = 0;
            $row['nilai_mutu'] = 'E';
        }
    }

    public function saveNilaiExcel()
    {
        $this->validate([
            'excel_nilai_file' => 'required|array',
            'excel_nilai_file.*' => 'file|mimes:xlsx,xls|max:27684',
        ], [
            'excel_nilai_file.required' => 'File Excel Data Nilai Mahasiswa wajib diunggah!',
            'excel_nilai_file.array' => 'Format unggahan tidak valid!',
            'excel_nilai_file.*.file' => 'Salah satu file Excel Data Nilai Mahasiswa harus berupa file yang valid!',
            'excel_nilai_file.*.mimes' => 'Setiap file Excel Data Nilai Mahasiswa harus berformat .xlsx atau .xls!',
            'excel_nilai_file.*.max' => 'Ukuran masing-masing file tidak boleh lebih dari 27 MB!',
        ]);
        if (empty($this->parsedNilaiRows)) {
            $this->toast(text: 'Tidak ada data nilai untuk disimpan!', variant: 'warning');

            return;
        }

        try {
            foreach ($this->parsedNilaiRows as $rowIndex => $row) {
                $this->recalculateRowNilai($rowIndex);
            }
            $this->stream('import-progress', 'Inisialisasi pemrosesan simpan nilai...');
            $this->procesImportNilaiExcel();
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ '.$e->getMessage());
        }
    }

    public function procesImportNilaiExcel()
    {
        $successCount = 0;
        $this->rowNilaiErrors = [];
        $successfulIndices = [];
        $total = count($this->parsedNilaiRows);

        LazyCollection::make($this->parsedNilaiRows)
            ->chunk(20)
            ->each(function ($chunk) use (&$successCount, &$successfulIndices, $total) {
                foreach ($chunk as $index => $row) {
                    try {
                        $validatedData = $this->inputModalNilai($row);
                        $this->saveNilaiFromExcel($validatedData);

                        $successfulIndices[] = $index;
                        $successCount++;
                    } catch (ValidationException $e) {
                        $this->rowNilaiErrors[$index] = $e->errors();
                    } catch (\Throwable $e) {
                        $this->rowNilaiErrors[$index] = ['general' => [$e->getMessage()]];
                    }
                }

                $message = "Memproses nilai... $successCount dari $total mahasiswa berhasil disimpan!";
                $this->stream(to: 'import-progress', content: $message, replace: true);
            });

        foreach (array_reverse($successfulIndices) as $idx) {
            unset($this->parsedNilaiRows[$idx]);
            unset($this->rowNilaiErrors[$idx]);
        }
        $this->parsedNilaiRows = array_values($this->parsedNilaiRows);

        $newRowErrors = [];
        $i = 0;
        foreach ($this->rowNilaiErrors as $oldIdx => $errors) {
            $newRowErrors[$i] = $errors;
            $i++;
        }
        $this->rowNilaiErrors = $newRowErrors;

        $failCount = count($this->parsedNilaiRows);
        $messageText = "Import Nilai Selesai | Sukses: $successCount | Gagal: $failCount";

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_nilai_file');
            $this->showNilaiExcelModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');
        }

        $this->dispatch('refresh-nilai-data');
    }

    private function getSesiImportNilai($jadwalId)
    {
        return $this->inputSesiSearch($jadwalId)->get()->sortBy('pertemuan_ke')->values();
    }

    private function saveNilaiFromExcel($validated)
    {
        DB::transaction(function () use ($validated) {
            $nim = trim($validated['nim']);
            $mahasiswa = Mahasiswa::query()
                ->where('nim', $nim)
                ->first();

            if (! $mahasiswa) {
                throw new \Exception(
                    "Mahasiswa dengan NIM {$nim} tidak ditemukan!"
                );
            }

            $jadwal = $this->getJadwalByKode($validated['kode_jadwal']);

            $rpsId = null;
            $ganjilGenap = '';
            $tahunAkademik = '';
            $jadwalId = null;

            if ($jadwal) {
                $rpsId = $jadwal->kelas_rel?->rps_id;
                $ganjilGenap = (string) $jadwal->ganjil_genap;
                $tahunAkademik = (string) $jadwal->tahun_akademik;
                $jadwalId = $jadwal->id;
            } else {
                $rps = $this->getRPSByKode($validated['kode_rps']);

                if (! $rps) {
                    throw new \Exception(
                        "Kelas '{$validated['kode_jadwal']}' maupun RPS dengan kode '{$validated['kode_rps']}' tidak dapat ditemukan."
                    );
                }

                $rpsId = $rps->id;
                $jadwalId = null;

                $now = now();
                $currentYear = $now->year;
                $currentMonth = $now->month;

                if ($currentMonth >= 2 && $currentMonth <= 7) {
                    $ganjilGenap = 'Genap';
                    $tahunAkademik = ($currentYear - 1).'/'.$currentYear;
                } else {
                    $ganjilGenap = 'Ganjil';
                    $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
                    $tahunAkademik = $prevYear.'/'.($prevYear + 1);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Cari atau Instansiasi Objek NilaiMahasiswa
            |--------------------------------------------------------------------------
            */
            $nilai_mahasiswa = NilaiMahasiswa::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('rps_id', $rpsId)
                ->where('ganjil_genap', $ganjilGenap)
                ->where('tahun_akademik', $tahunAkademik)
                ->lockForUpdate()
                ->first();

            if (! $nilai_mahasiswa) {
                $nilai_mahasiswa = new NilaiMahasiswa;
                $nilai_mahasiswa->mahasiswa_id = $mahasiswa->id;
                $nilai_mahasiswa->ganjil_genap = $ganjilGenap;
                $nilai_mahasiswa->tahun_akademik = $tahunAkademik;
            }

            $nilai_mahasiswa->rps_id = $rpsId;
            $nilai_mahasiswa->kj_id = $jadwalId;

            $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
            $bobotArray = $nilai_mahasiswa->bobot_array ?? [];

            /*
            |--------------------------------------------------------------------------
            | 3. Mapping index nilai dari sub_cpmk
            |--------------------------------------------------------------------------
            */
            $mapScpmk = [];
            if ($jadwalId) {
                $sesis = $this->getSesiImportNilai($jadwalId);
                foreach ($sesis as $index => $sesi) {
                    $kodeSCPMK = preg_replace(
                        '/[^A-Za-z0-9]/',
                        '',
                        $sesi->scpmk_atr?->kode
                            ?? $sesi->scpmk_atr?->kode_scpmk
                            ?? ''
                    );
                    if ($kodeSCPMK !== '') {
                        $mapScpmk[$kodeSCPMK] = $index;
                    }
                }
            }

            foreach ($validated['sub_cpmk'] ?? [] as $subIndex => $sub) {
                $kodeSCPMK = preg_replace('/[^A-Za-z0-9]/', '', $sub['kode_scpmk'] ?? '');

                $targetIndex = (isset($mapScpmk[$kodeSCPMK]) && $kodeSCPMK !== '')
                    ? $mapScpmk[$kodeSCPMK]
                    : $subIndex;

                $nilaiArray[$targetIndex] = is_numeric($sub['nilai'] ?? null)
                    ? (float) $sub['nilai']
                    : 0;

                $bobotArray[$targetIndex] = is_numeric($sub['bobot'] ?? null)
                    ? (float) $sub['bobot']
                    : 0;
            }

            ksort($nilaiArray);
            ksort($bobotArray);

            // 🌟 KUNCI PERBAIKAN: NORMALISASI BOBOT SUPAYA TOTAL HARUS 100% (1.0)
            $totalBobotRPS = array_sum($bobotArray);
            $totalNilaiAkhir = 0;

            if ($totalBobotRPS > 0) {
                foreach ($nilaiArray as $index => $nilai) {
                    $bobotMentah = $bobotArray[$index] ?? 0;

                    // Normalisasikan bobot tiap elemen terhadap total bobot keseluruhan
                    $bobotNormal = $bobotMentah / $totalBobotRPS;

                    $totalNilaiAkhir += ($nilai * $bobotNormal);
                }
            }

            $nilai_mahasiswa->nilai_array = $nilaiArray;
            $nilai_mahasiswa->bobot_array = $bobotArray;

            // 🌟 Mengabaikan $validated['nilai_angka'] dan menggunakan kalkulasi proporsional murni
            $nilai_mahasiswa->nilai = round($totalNilaiAkhir, 2);

            $nilai_mahasiswa->save();
        });
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
        Auth::login($user);

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

                $startNilaiIndex = 6;

                $nilaiRPSIndex = collect($header1)->search(fn ($v) => in_array(Str::lower(trim((string) $v)), ['kode rps', 'rps']));
                $nilaiJadwalKelasIndex = collect($header1)->search(fn ($v) => in_array(Str::lower(trim((string) $v)), ['nama kelas', 'kode kelas', 'kode jadwal', 'keals jadwal']));
                $nilaiAngkaIndex = collect($header1)->search(fn ($v) => Str::lower(trim((string) $v)) === 'nilai angka');

                if ($nilaiRPSIndex === false || $nilaiJadwalKelasIndex === false || $nilaiAngkaIndex === false) {
                    Log::warning("Sheet '{$sheetName}' dilewati karena struktur header tidak sesuai.");

                    continue;
                }

                $totalSheetsProcessed++;

                $subCpmkColumns = [];
                $currentCPMK = null;

                for ($col = $startNilaiIndex; $col < $nilaiAngkaIndex; $col++) {
                    $rawCpmk = trim((string) ($header1[$col] ?? ''));
                    if ($rawCpmk !== '') {
                        $currentCPMK = $rawCpmk;
                    }

                    $rawSub = trim((string) ($header2[$col] ?? ''));
                    if ($rawSub === '') {
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

                $dataRows = array_slice($allData, 3);

                foreach ($dataRows as $row) {
                    if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->isEmpty()) {
                        continue;
                    }

                    $subCpmk = [];
                    $totalNilai = 0;
                    $count = 0;

                    foreach ($subCpmkColumns as $col => $meta) {
                        $nilaiRaw = $row[$col] ?? '';
                        $nilaiFix = is_numeric($nilaiRaw) ? (float) $nilaiRaw : null;

                        if ($nilaiFix !== null) {
                            $totalNilai += $nilaiFix;
                        }
                        $count++;

                        $subCpmk[] = [
                            'cpmk' => $meta['cpmk'],
                            'kode_scpmk' => $meta['kode_scpmk'],
                            'pertemuan' => $meta['pertemuan'],
                            'bobot' => $meta['bobot'],
                            'nilai' => $nilaiFix,
                        ];
                    }

                    $average = $count > 0 ? round(($totalNilai / $count), 2) : 0;

                    $mockRowData = [
                        'kode_rps' => trim((string) ($row[$nilaiRPSIndex] ?? '')),
                        'nama_mk' => trim((string) ($row[1] ?? '')),
                        'kode_jadwal' => trim((string) ($row[2] ?? '')),
                        'nim' => trim((string) ($row[3] ?? '')),
                        'nama' => trim((string) ($row[4] ?? '')),
                        'angkatan' => trim((string) ($row[5] ?? '')),
                        'sub_cpmk' => $subCpmk,
                        'nilai_angka' => $average,
                    ];

                    try {
                        $this->saveNilaiFromExcel($mockRowData);
                        $successCount++;
                    } catch (\Throwable $e) {
                        $errors[] = "[Sheet: {$sheetName}] NIM {$mockRowData['nim']}: ".$e->getMessage();
                    }
                }
            }

            Auth::logout();

            if ($totalSheetsProcessed === 0) {
                throw new \Exception('Tidak ada halaman (sheet) dalam file Excel yang sesuai dengan standar format format nilai.');
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
            Auth::logout();
            Log::error('Gagal memproses excel langsung: '.$e->getMessage());

            return ['status' => true, 'head' => '❌ *Gagal Memproses File!*', 'message' => ''.$e->getMessage()];
        }
    }

    // private function saveNilaiMurniDariWhatsApp($validated)
    // {
    //     DB::transaction(function () use ($validated) {
    //         $nim = trim($validated['nim']);

    //         // 1. Cari Mahasiswa murni berdasarkan NIM
    //         $mahasiswa = Mahasiswa::query()
    //             ->where('nim', $nim)
    //             ->first();

    //         if (! $mahasiswa) {
    //             throw new \Exception("Mahasiswa dengan NIM {$nim} tidak ditemukan di sistem!");
    //         }

    //         // 2. Cari Jadwal Kelas murni via Model langsung
    //         $jadwal = KelasJadwal::query()
    //             ->where('kode_jadwal', $validated['kode_jadwal'])
    //             ->first();

    //         $rpsId = null;
    //         $ganjilGenap = '';
    //         $tahunAkademik = '';
    //         $jadwalId = null;

    //         if ($jadwal) {
    //             $rpsId = $jadwal->kelas_rel?->rps_id;
    //             $ganjilGenap = (string) $jadwal->ganjil_genap;
    //             $tahunAkademik = (string) $jadwal->tahun_akademik;
    //             $jadwalId = $jadwal->id;
    //         } else {
    //             $rps = RPS::query()
    //                 ->where('kode_rps', $validated['kode_rps'])
    //                 ->first();

    //             if (! $rps) {
    //                 throw new \Exception("Kelas '{$validated['kode_jadwal']}' maupun RPS dengan kode '{$validated['kode_rps']}' tidak ditemukan.");
    //             }

    //             $rpsId = $rps->id;
    //             $jadwalId = null;

    //             $now = now();
    //             $currentYear = $now->year;
    //             $currentMonth = $now->month;

    //             if ($currentMonth >= 2 && $currentMonth <= 7) {
    //                 $ganjilGenap = 'Genap';
    //                 $tahunAkademik = ($currentYear - 1).'/'.$currentYear;
    //             } else {
    //                 $ganjilGenap = 'Ganjil';
    //                 $prevYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    //                 $tahunAkademik = $prevYear.'/'.($prevYear + 1);
    //             }
    //         }

    //         // 3. Cari atau Instansiasi Objek NilaiMahasiswa
    //         $nilai_mahasiswa = NilaiMahasiswa::query()
    //             ->where('mahasiswa_id', $mahasiswa->id)
    //             ->where('rps_id', $rpsId)
    //             ->where('ganjil_genap', $ganjilGenap)
    //             ->where('tahun_akademik', $tahunAkademik)
    //             ->lockForUpdate()
    //             ->first();

    //         if (! $nilai_mahasiswa) {
    //             $nilai_mahasiswa = new NilaiMahasiswa;
    //             $nilai_mahasiswa->mahasiswa_id = $mahasiswa->id;
    //             $nilai_mahasiswa->ganjil_genap = $ganjilGenap;
    //             $nilai_mahasiswa->tahun_akademik = $tahunAkademik;
    //         }

    //         $nilai_mahasiswa->rps_id = $rpsId;
    //         $nilai_mahasiswa->kj_id = $jadwalId;

    //         $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
    //         $bobotArray = $nilai_mahasiswa->bobot_array ?? [];

    //         // 4. Mapping index nilai dari sub_cpmk menggunakan query Sesi murni
    //         $mapScpmk = [];
    //         if ($jadwalId) {
    //             // Ambil sesi langsung menggunakan Model murni untuk memotong fungsi bawaan Livewire
    //             $sesis = Sesi::query()
    //                 ->where('kj_id', $jadwalId) // Sesuaikan foreign key jadwal ke sesi Anda
    //                 ->orderBy('pertemuan_ke')
    //                 ->get();

    //             foreach ($sesis as $index => $sesi) {
    //                 // Ambil kode SCPMK dengan aman, pastikan relasi 'scpmk_atr' tidak mengikat Auth::user()
    //                 $kodeSCPMK = preg_replace(
    //                     '/[^A-Za-z0-9]/',
    //                     '',
    //                     $sesi->scpmk_atr?->kode
    //                         ?? $sesi->scpmk_atr?->kode_scpmk
    //                         ?? ''
    //                 );
    //                 if ($kodeSCPMK !== '') {
    //                     $mapScpmk[$kodeSCPMK] = $index;
    //                 }
    //             }
    //         }

    //         foreach ($validated['sub_cpmk'] ?? [] as $subIndex => $sub) {
    //             $kodeSCPMK = preg_replace('/[^A-Za-z0-9]/', '', $sub['kode_scpmk'] ?? '');

    //             $targetIndex = (isset($mapScpmk[$kodeSCPMK]) && $kodeSCPMK !== '')
    //                 ? $mapScpmk[$kodeSCPMK]
    //                 : $subIndex;

    //             $nilaiArray[$targetIndex] = is_numeric($sub['nilai'] ?? null) ? (float) $sub['nilai'] : 0;
    //             $bobotArray[$targetIndex] = is_numeric($sub['bobot'] ?? null) ? (float) $sub['bobot'] : 0;
    //         }

    //         ksort($nilaiArray);
    //         ksort($bobotArray);

    //         $nilai_mahasiswa->nilai_array = $nilaiArray;
    //         $nilai_mahasiswa->bobot_array = $bobotArray;
    //         $nilai_mahasiswa->nilai = $validated['nilai_angka'];

    //         $nilai_mahasiswa->save();
    //     });
    // }
    // private function saveNilaiFromExcel($validated)
    // {
    //     DB::transaction(function () use ($validated) {
    //         $nim = trim($validated['nim']);
    //         $mahasiswa = Mahasiswa::query()
    //             ->where('nim', $nim)
    //             ->first();

    //         if (! $mahasiswa) {
    //             throw new \Exception(
    //                 "Mahasiswa dengan NIM {$nim} tidak ditemukan!"
    //             );
    //         }

    //         $jadwal = $this->getJadwalByKode($validated['kode_jadwal']);
    //         $rps = $this->getRPSByKode($validated['kode_rps']);
    //         // dd($jadwal->id);
    //         if (! $jadwal) {
    //             throw new Exception(
    //                 "Kelas {$validated['kode_jadwal']} tidak ditemukan."
    //             );
    //         }

    //         $nilai_mahasiswa = NilaiMahasiswa::query()
    //             ->where('mahasiswa_id', $mahasiswa->id)
    //             ->where('rps_id', $jadwal->kelas_rel->rps_id)
    //             ->where('ganjil_genap', (string) $jadwal->ganjil_genap)
    //             ->where('tahun_akademik', (string) $jadwal->tahun_akademik)
    //             ->lockForUpdate()
    //             ->first();

    //         if (! $nilai_mahasiswa) {
    //             $nilai_mahasiswa = new NilaiMahasiswa();
    //             $nilai_mahasiswa->mahasiswa_id   = $mahasiswa->id;
    //             $nilai_mahasiswa->ganjil_genap   = (string) $jadwal->ganjil_genap;
    //             $nilai_mahasiswa->tahun_akademik = (string) $jadwal->tahun_akademik;
    //         }

    //         $nilai_mahasiswa->rps_id = $jadwal->kelas_rel->rps_id;

    //         $nilaiArray = $nilai_mahasiswa->nilai_array ?? [];
    //         $bobotArray = $nilai_mahasiswa->bobot_array ?? [];

    //         $sesis = $this->getSesiImportNilai($jadwal->id);
    //         $mapScpmk = [];

    //         foreach ($sesis as $index => $sesi) {
    //             $kodeSCPMK = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 $sesi->scpmk_atr?->kode
    //                     ?? $sesi->scpmk_atr?->kode_scpmk
    //                     ?? ''
    //             );
    //             if ($kodeSCPMK !== '') {
    //                 $mapScpmk[$kodeSCPMK] = $index;
    //             }
    //         }

    //         foreach ($validated['sub_cpmk'] ?? [] as $sub) {
    //             $kodeSCPMK = preg_replace(
    //                 '/[^A-Za-z0-9]/',
    //                 '',
    //                 $sub['kode_scpmk'] ?? ''
    //             );

    //             if (empty($kodeSCPMK) || ! isset($mapScpmk[$kodeSCPMK])) {
    //                 continue;
    //             }

    //             $targetIndex = $mapScpmk[$kodeSCPMK];
    //             $nilaiArray[$targetIndex] = is_numeric($sub['nilai'] ?? null)
    //                 ? (float) $sub['nilai']
    //                 : null;
    //             $bobotArray[$targetIndex] = is_numeric($sub['bobot'] ?? null)
    //                 ? (float) $sub['bobot']
    //                 : 0;
    //         }

    //         $nilai_mahasiswa->kj_id = $jadwal->id;
    //         $nilai_mahasiswa->nilai_array = $nilaiArray;
    //         $nilai_mahasiswa->bobot_array = $bobotArray;
    //         $nilai_mahasiswa->nilai = $validated['nilai_angka'];

    //         $nilai_mahasiswa->save();
    //     });
    // }

    private function inputModalNilai($data)
    {
        $rules = [
            'kode_rps' => 'required|string',
            'kode_jadwal' => 'required|string',
            'nim' => 'required|string',
            'nama' => 'required|string|max:255',
            'nilai_angka' => 'required|numeric|min:0|max:100',
            'nilai_index' => 'nullable|numeric',
            'nilai_mutu' => 'nullable|string|max:2',

            'sub_cpmk' => 'nullable|array',
            'sub_cpmk.*.kode_scpmk' => 'nullable|string',
            'sub_cpmk.*.nilai' => 'nullable|numeric|min:0|max:100',
            'sub_cpmk.*.bobot' => 'nullable|numeric',
        ];

        return Validator::make(
            $data,
            $rules, [
                'kode_rps.required' => 'Kode RPS wajib diisi!',
                'kode_rps.string' => 'Kode RPS harus berupa teks!',
                'kode_jadwal.required' => 'Kode Kelas wajib diisi!',
                'kode_jadwal.string' => 'Kode Kelas harus berupa teks!',
                'nim.required' => 'NIM mahasiswa wajib diisi!',
                'nama.required' => 'Nama mahasiswa wajib diisi!',
                'nilai_angka.required' => 'Nilai angka wajib diisi!',
                'nilai_angka.numeric' => 'Nilai harus berupa angka!',
                'nilai_angka.min' => 'Nilai minimal adalah 0!',
                'nilai_angka.max' => 'Nilai maksimal adalah 100!',

                'sub_cpmk.array' => 'Format data Sub-CPMK tidak valid!',
                'sub_cpmk.*.kode_scpmk.string' => 'Kode Sub-CPMK harus berupa teks!',
                'sub_cpmk.*.nilai.numerik' => 'Nilai Sub-CPMK harus berupa angka!',
                'sub_cpmk.*.nilai.min' => 'Nilai Sub-CPMK minimal adalah 0!',
                'sub_cpmk.*.nilai.max' => 'Nilai Sub-CPMK maksimal adalah 100!',
                'sub_cpmk.*.bobot.numerik' => 'Bobot Sub-CPMK harus berupa teks!!',
            ]
        )->validate();
    }

    // protected function getRPSInfoByKode(?string $kodeRPS): array
    // {
    //     $rps = $this->findRPSByKode($kodeRPS);

    //     if (! $rps) {
    //         return [
    //             'rps' => null,
    //             'ganjil_genap' => null,
    //             'tahun_akademik' => null,
    //         ];
    //     }

    //     $kelas = Kelas::query()
    //         ->where('rps_id', $rps->id)
    //         ->first();

    //     return [
    //         'rps' => $rps,
    //         'ganjil_genap' => $this->getRPSGanjilGenap($rps),
    //         'tahun_akademik' => $this->getRPSTahunAkademik($kelas),
    //     ];
    // }

    // protected function findRPSByKode(?string $kodeRPS): ?RPS
    // {
    //     $kodeRPS = strtolower(preg_replace(
    //         '/[^a-z0-9]/',
    //         '',
    //         trim($kodeRPS ?? '')
    //     )
    //     );

    //     if (empty($kodeRPS)) {
    //         return null;
    //     }

    //     return $this->inputRPSSearch()->get()->first(function ($rps) use ($kodeRPS) {
    //         $kode = strtolower(
    //             preg_replace(
    //                 '/[^a-z0-9]/',
    //                 '',
    //                 $rps->kode ?? ''
    //             )
    //         );

    //         return $kode === $kodeRPS;
    //     });
    // }

    // protected function getRPSGanjilGenap(?RPS $rps): ?string
    // {
    //     if (! $rps) {
    //         return null;
    //     }

    //     return ((int) $rps->semester % 2 === 0)
    //         ? 'Genap'
    //         : 'Ganjil';
    // }

    // protected function getRPSTahunAkademik(?Kelas $kelas): ?string
    // {
    //     if (! $kelas?->tanggal_mulai) {
    //         return null;
    //     }

    //     $tahun = Carbon::parse(
    //         $kelas->tanggal_mulai
    //     )->year;

    //     return $tahun.'/'.($tahun + 1);
    // }

    public function loadingNilaiExcel() {}
}
