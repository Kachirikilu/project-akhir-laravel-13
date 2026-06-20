<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\NilaiExport;
use App\Livewire\AllRole\KelasManagement\JadwalManagement\WithJadwalFilters;
use App\Livewire\Global\HasToast;
use App\Livewire\Staff\OBEManagement\RPSManagement\WithRPSFilters;
use App\Models\Akademik\RPS;
use App\Models\Auth\Mahasiswa;
use App\Models\Kelas\Kelas;
use App\Models\Kelas\KelasJadwal;
// use Illuminate\Support\LazyCollection;
use App\Models\Penilaian\NilaiMahasiswa;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    use WithFileUploads;
    use WithJadwalFilters;
    use WithRPSFilters;
    use WithSesiFilters;

    public $showNilaiExcelModal;

    public $excel_nilai_file;

    public array $parsedNilaiRows = [];

    public array $rowNilaiErrors = [];

    public $excelNilaiPerPage = 30;

    public array $parsedNilaiHeaders = [];

    public array $jadwalQueue = [];

    public function exportNilaiExcel()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }

        if (! (Auth::user()->admin || Auth::user()->dosen)) {
            return;
        }

        if (! empty($this->jadwal)) {
            return $this->prosesDownloadTunggal($this->jadwal);
        }

        if (! empty($this->kelas)) {
            $jadwals = $this->kelas->jadwals()->get();
            if ($jadwals->count() === 1) {
                return $this->prosesDownloadTunggal($jadwals->first());
            }

            return $this->prosesDownloadZip($jadwals);
        }
    }

    private function prosesDownloadZip($jadwals)
    {
        $kode = $this->kelas->kode;
        $rps = $this->kelas->kode_rps;
        $mk = $this->kelas->mk;

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
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function prosesDownloadTunggal($jadwalModel)
    {
        $mk = $this->kelas->mk;
        $nowStr = now()->format('Y-m-d');

        $fileName = $jadwalModel->kode.'_'.$jadwalModel->kode_rps.'_'.$mk.'_'.$nowStr.'.xlsx';
        $fileNameSafe = str_replace('/', '-', $fileName);

        return Excel::download(new NilaiExport($jadwalModel->id), $fileNameSafe);
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
    //         return $this->prosesDownloadTunggal($this->jadwal);
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

    //         return $this->prosesDownloadTunggal($j);
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

        // 1. PERBAIKAN VALIDASI: Tambahkan asterisk (*) untuk memvalidasi setiap file di dalam array
        $this->validate([
            'excel_nilai_file' => 'required|array',
            'excel_nilai_file.*' => 'file|mimes:xlsx,xls|max:10240',
        ]);

        // Pastikan properti parsedNilaiRows siap menampung data dari semua file
        $this->parsedNilaiRows = [];

        // 2. PERBAIKAN UTAMA: Looping array file-file yang diunggah
        foreach ($this->excel_nilai_file as $singleFile) {

            // Membaca path file saat ini dari loop
            $spreadsheet = IOFactory::load($singleFile->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();

            $allData = $worksheet->toArray(null, true, false, false);

            if (empty($allData)) {
                // Kita gunakan continue alih-alih throw Exception agar file kosong dilewati,
                // dan file valid lainnya tetap diproses.
                continue;
            }

            // ==================================================
            // HEADER FIXED
            // ==================================================
            $header1 = $allData[0] ?? []; // CPMK
            $header2 = $allData[1] ?? []; // SCPMK
            $header3 = $allData[2] ?? []; // Bobot

            $startRPSIndex = 0; // A
            $startJadwalKelasIndex = 2; // C
            $startNilaiIndex = 6; // G

            $nilaiRPSIndex = collect($header1)->search(function ($v) {
                $value = Str::lower(trim((string) $v));

                return in_array($value, ['kode rps', 'rps']);
            });

            $nilaiJadwalKelasIndex = collect($header1)->search(function ($v) {
                $value = Str::lower(trim((string) $v));

                return in_array($value, ['nama kelas', 'kode kelas', 'kode jadwal', 'keals jadwal']);
            });

            $nilaiAngkaIndex = collect($header1)->search(fn ($v) => Str::lower(trim((string) $v)) === 'nilai angka');

            // Jika struktur file salah, lewati file ini dan lanjut ke file berikutnya
            if ($nilaiRPSIndex === false || $nilaiJadwalKelasIndex === false || $nilaiAngkaIndex === false) {
                continue;
            }

            // ==================================================
            // PARSE SUB CPMK
            // ==================================================
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

            // Catatan: Properti ini akan menyimpan header dari file terakhir yang dibaca sukses.
            $this->parsedNilaiHeaders = collect($subCpmkColumns)
                ->map(fn ($item) => [
                    'cpmk' => $item['cpmk'],
                    'sub_cpmk' => $item['kode_scpmk'],
                    'pertemuan' => $item['pertemuan'],
                    'bobot' => $item['bobot'],
                ])
                ->values()
                ->toArray();

            // ==================================================
            // KOLOM FINAL
            // ==================================================
            $nilaiIndexIndex = $nilaiAngkaIndex + 1;
            $nilaiMutuIndex = $nilaiAngkaIndex + 2;
            $dataRows = array_slice($allData, 3);

            foreach ($dataRows as $rowIndex => $row) {
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

                // Gabungkan semua baris data dari semua file ke dalam satu properti global
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
        } // Akhir dari foreach file excel_nilai_file

        $this->toast(
            text: 'Semua file Excel ('.count($this->excel_nilai_file).' file) berhasil dimuat. Silakan periksa nilai mahasiswa!'
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
            ->chunk(50)
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

            $nilai_mahasiswa->nilai_array = $nilaiArray;
            $nilai_mahasiswa->bobot_array = $bobotArray;
            $nilai_mahasiswa->nilai = $validated['nilai_angka'];

            $nilai_mahasiswa->save();
        });
    }

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

    protected function getJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    {
        $jadwal = $this->findJadwalByKode($kodeJadwal);
        if (! $jadwal) {
            return null;
        }

        return $jadwal;
    }

    protected function findJadwalByKode(?string $kodeJadwal): ?KelasJadwal
    {
        if (blank($kodeJadwal)) {
            return null;
        }
        $search = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            strtolower(trim($kodeJadwal))
        );

        return $this->inputJadwalSearch()
            ->get()
            ->first(function ($j) use ($search) {
                $kode = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    strtolower($j->kode)
                );

                return $kode === $search;
            });
    }

    protected function getRPSByKode(?string $kodeRPS): ?RPS
    {
        $rps = $this->findRPSByKode($kodeRPS);
        if (! $rps) {
            return null;
        }

        return $rps;
    }

    protected function findRPSByKode(?string $kodeRPS): ?RPS
    {
        if (blank($kodeRPS)) {
            return null;
        }
        $search = preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            strtolower(trim($kodeRPS))
        );

        return $this->inputRPSSearch()
            ->get()
            ->first(function ($r) use ($search) {
                $kode = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    strtolower($r->kode)
                );

                return $kode === $search;
            });
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
