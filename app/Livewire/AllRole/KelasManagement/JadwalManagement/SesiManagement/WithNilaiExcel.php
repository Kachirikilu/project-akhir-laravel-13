<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\MultiNilaiExport;
use App\Exports\NilaiExport;
use App\Livewire\Global\HasGetByKode;
use App\Livewire\Global\HasToast;
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
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

trait WithNilaiExcel
{
    use HasGetByKode;
    use HasToast;
    use WithFileUploads;
    use WithSesiFilters;
    use WithNilaiExcelImport;

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
            throw new \Exception('File ZIP gagal digenerate!');
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



    public function clearNilaiExcelFile()
    {
        $this->excel_nilai_file = null;
        $this->reset([
            'parsedNilaiRows',
            'rowNilaiErrors',
            'parsedNilaiHeaders',
            'uploadedFileNames'
        ]);
        if (method_exists($this, 'setPage')) {
            $this->setPage(1, 'excelPage');
        }
        $this->dispatch('reset-file-input', id: 'excel_nilai_file');
        $this->toast(type: 'info', text: 'File berhasil dihapus.');
    }


    public function removeParsedNilaiRow($index)
    {
        if (isset($this->parsedNilaiRows[$index])) {
            unset($this->parsedNilaiRows[$index]);
            $this->parsedNilaiRows = array_values($this->parsedNilaiRows);
            $this->toast(text: 'Baris mahasiswa dihapus!');
        }
    }

    public function updatedParsedNilaiRows($value): void
    {
        if (! is_array($value)) {
            return;
        }

        $normalizedRows = [];
        foreach ($value as $rowIndex => $row) {
            $normalizedRows[$rowIndex] = is_array($row)
                ? $this->normalizeParsedNilaiRow($row)
                : $row;
        }

        $this->parsedNilaiRows = $normalizedRows;
    }

    private function deriveNilaiGrade(float $average): array
    {
        if ($average >= 86) {
            return ['nilai_index' => 4, 'nilai_mutu' => 'A'];
        }

        if ($average >= 71) {
            return ['nilai_index' => 3, 'nilai_mutu' => 'B'];
        }

        if ($average >= 56) {
            return ['nilai_index' => 2, 'nilai_mutu' => 'C'];
        }

        if ($average >= 41) {
            return ['nilai_index' => 1, 'nilai_mutu' => 'D'];
        }

        return ['nilai_index' => 0, 'nilai_mutu' => 'E'];
    }

    private function normalizeParsedNilaiRows(): void
    {
        if (! is_array($this->parsedNilaiRows)) {
            return;
        }

        $normalizedRows = [];
        foreach ($this->parsedNilaiRows as $rowIndex => $row) {
            $normalizedRows[$rowIndex] = is_array($row)
                ? $this->normalizeParsedNilaiRow($row)
                : $row;
        }

        $this->parsedNilaiRows = $normalizedRows;
    }

    private function normalizeParsedNilaiRow(array $row): array
    {
        $subCpmks = $row['sub_cpmk'] ?? [];
        if (! is_array($subCpmks)) {
            $subCpmks = [];
        }

        $normalizedSubCpmks = collect($subCpmks)
            ->map(function ($sub) {
                if (! is_array($sub)) {
                    return $sub;
                }

                if (array_key_exists('nilai', $sub) && $sub['nilai'] !== '' && $sub['nilai'] !== null && ! is_numeric($sub['nilai'])) {
                    $sub['nilai'] = null;
                }

                return $sub;
            })
            ->values()
            ->all();

        $totalNilai = 0;
        $count = 0;
        foreach ($normalizedSubCpmks as $sub) {
            if (isset($sub['nilai']) && is_numeric($sub['nilai'])) {
                $totalNilai += (float) $sub['nilai'];
                $count++;
            }
        }

        $average = $count > 0 ? round($totalNilai / $count, 2) : 0;
        $gradeInfo = $this->deriveNilaiGrade($average);

        $row['sub_cpmk'] = $normalizedSubCpmks;
        $row['nilai_angka'] = $average;
        $row['nilai_index'] = $gradeInfo['nilai_index'];
        $row['nilai_mutu'] = $gradeInfo['nilai_mutu'];

        return $row;
    }

    public function recalculateRowNilai(int $rowIndex)
    {
        if (! isset($this->parsedNilaiRows[$rowIndex])) {
            return;
        }

        $this->parsedNilaiRows[$rowIndex] = $this->normalizeParsedNilaiRow($this->parsedNilaiRows[$rowIndex]);
    }

    public function updateParsedNilaiCell(int $rowIndex, int $subIndex, $value): void
    {
        if (! isset($this->parsedNilaiRows[$rowIndex]['sub_cpmk'][$subIndex])) {
            return;
        }

        $normalizedValue = trim((string) $value);
        $numericValue = $normalizedValue === '' ? null : (is_numeric($normalizedValue) ? (float) $normalizedValue : null);

        $rows = $this->parsedNilaiRows;
        data_set($rows, "$rowIndex.sub_cpmk.$subIndex.nilai", $numericValue);
        $this->parsedNilaiRows = $rows;
        $this->recalculateRowNilai($rowIndex);
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
            $this->normalizeParsedNilaiRows();
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

        $rows = $this->parsedNilaiRows;

        foreach (array_chunk($rows, 20, true) as $chunk) {
            foreach ($chunk as $rowIndex => $row) {
                try {
                    $validatedData = $this->inputModalNilai($row);
                    // dd($validatedData);
                    $this->saveNilaiFromExcel($validatedData);

                    $successfulIndices[] = $rowIndex;
                    $successCount++;
                } catch (ValidationException $e) {
                    $this->rowNilaiErrors[$rowIndex] = $e->errors();
                } catch (\Throwable $e) {
                    $this->rowNilaiErrors[$rowIndex] = ['general' => [$e->getMessage()]];
                }
            }

            $message = "Memproses nilai... $successCount dari $total mahasiswa berhasil disimpan!";
            $this->stream(to: 'import-progress', content: $message, replace: true);
        }

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
        $messageText = "Import Data Nilai Mahasiswa Selesai | Sukses: $successCount | Gagal: $failCount";
        $this->uploadedFileNames = [];
        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_nilai_file');
            $this->showNilaiExcelModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');
        }

        $this->dispatch('refresh-nilai-data');
        $this->dispatch('refresh-data-sesi');
    }

    private function getSesiImportNilai($jadwalId)
    {
        return $this->inputSesiSearch($jadwalId)->get()->sortBy('pertemuan_ke')->values();
    }

    private function saveNilaiFromExcel($validated)
    {
        $nim = trim($validated['nim']);
        $nilaiAkhir = $validated['nilai_angka'] ?? null;
        // \Log::info("DEBUG NIM: {$nim} | NilaiAkhir: {$nilaiAkhir} | SubCPMK Count: ".count($validated['sub_cpmk'] ?? []));
        DB::transaction(function () use ($validated) {
            $nim = trim($validated['nim']);
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();

            if (! $mahasiswa) {
                throw new \Exception("Mahasiswa dengan NIM {$nim} tidak ditemukan!");
            }

            $jadwal = $this->getJadwalByKode($validated['kode_jadwal']);
            $rps = $jadwal ? $jadwal->kelas_rel?->rps_rel : $this->getRPSByKode($validated['kode_rps']);

            if (! $rps) {
                throw new \Exception("RPS untuk NIM {$nim} tidak ditemukan.");
            }

            $ganjilGenap = $jadwal ? (string) $jadwal->ganjil_genap : ($now = now()->month >= 2 && now()->month <= 7 ? 'Genap' : 'Ganjil');
            $tahunAkademik = $jadwal ? (string) $jadwal->tahun_akademik : ($now = now()->month >= 2 && now()->month <= 7 ? (now()->year - 1).'/'.now()->year : now()->year.'/'.(now()->year + 1));
            $jadwalId = $jadwal?->id;

            $strukturRPS = $rps->scpmkAtr ?? [];
            $mapScpmk = [];
            foreach ($strukturRPS as $idx => $item) {
                $kode = preg_replace('/[^A-Za-z0-9]/', '', $item->kode ?? '');
                if ($kode !== '') {
                    $mapScpmk[$kode][] = $idx;
                }
            }

            $len = max(16, count($strukturRPS));
            $nArr = array_fill(0, $len, 0);
            $bArr = array_fill(0, $len, 0);

            foreach ($validated['sub_cpmk'] ?? [] as $sub) {
                $k = preg_replace('/[^A-Za-z0-9]/', '', $sub['kode_scpmk'] ?? '');
                if (isset($mapScpmk[$k])) {
                    foreach ($mapScpmk[$k] as $idx) {
                        $nArr[$idx] = (float) ($sub['nilai'] ?? 0);
                        $bArr[$idx] = (float) ($sub['bobot'] ?? 0);
                    }
                }
            }

            \DB::table('nilai_mahasiswa')->updateOrInsert(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'rps_id' => $rps->id,
                    'ganjil_genap' => $ganjilGenap,
                    'tahun_akademik' => $tahunAkademik,
                ],
                [
                    'kj_id' => $jadwalId,
                    'nilai_array' => json_encode($nArr),
                    'bobot_array' => json_encode($bArr),
                    'nilai' => round(is_numeric($validated['nilai_angka'] ?? null) ? $validated['nilai_angka'] : $this->calculateFinal($nArr, $bArr), 2),
                    'updated_at' => now(),
                ]
            );

        });
    }

    private function calculateFinal($nArr, $bArr)
    {
        $totalB = array_sum($bArr);
        if ($totalB <= 0) {
            return 0;
        }
        $totalN = 0;
        foreach ($nArr as $i => $v) {
            $totalN += ($v * ($bArr[$i] / $totalB));
        }

        return $totalN;
    }


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
