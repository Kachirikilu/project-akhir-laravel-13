<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\NilaiExport;
use App\Livewire\Global\HasToast;
use App\Models\Auth\Mahasiswa;
use App\Models\Penilaian\NilaiMahasiswa;
// use Illuminate\Support\LazyCollection;
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

trait WithNilaiExcel
{
    use HasToast;
    use WithFileUploads;

    public $showNilaiExcelModal;

    public $excel_nilai_file;

    public array $parsedNilaiRows = [];

    public array $rowNilaiErrors = [];

    public $excelNilaiPerPage = 30;

    public array $parsedNilaiHeaders = [];

    public function exportNilaiExcel()
    {
        if (! $this->AuthCheck('staff')) {
            return;
        }
        if (Auth::user()->admin || Auth::user()->dosen) {
            $kelas = $this->kelas->kode;
            $kode_mk = $this->kelas->kode_mk;
            $mk = $this->kelas->mk;
            $jadwal = $this->jadwal->kode_jadwal;

            $fileName = $kelas.'_'.$kode_mk.'_'.$mk.'_'.$jadwal.'_'.now()->format('Y-m-d').'.xlsx';
            $fileNameSafe = str_replace('/', '-', $fileName);

            return Excel::download(new NilaiExport($this->jadwal_id), $fileNameSafe);
        }
    }

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

        $this->validate([
            'excel_nilai_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $spreadsheet = IOFactory::load(
            $this->excel_nilai_file->getRealPath()
        );

        $worksheet = $spreadsheet->getActiveSheet();

        $allData = $worksheet->toArray(
            null,
            true,
            false,
            false
        );

        if (empty($allData)) {
            throw new \Exception('File Excel kosong!');
        }

        // ==================================================
        // HEADER FIXED
        // ==================================================
        $header1 = $allData[0] ?? []; // CPMK
        $header2 = $allData[1] ?? []; // SCPMK
        $header3 = $allData[2] ?? []; // Bobot

        $startNilaiIndex = 6; // G

        // Cari kolom Nilai Angka
        $nilaiAngkaIndex = collect($header1)
            ->search(fn ($v) => Str::lower(trim((string) $v))
                === 'nilai angka'
            );

        if ($nilaiAngkaIndex === false) {
            throw new \Exception(
                'Kolom Nilai Angka tidak ditemukan!'
            );
        }

        // ==================================================
        // PARSE SUB CPMK
        // ==================================================
        $subCpmkColumns = [];

        $currentCpmk = null;

        for ($col = $startNilaiIndex; $col < $nilaiAngkaIndex; $col++) {

            // ==========================
            // CPMK (forward fill merge)
            // ==========================
            $rawCpmk = trim(
                (string) ($header1[$col] ?? '')
            );

            if ($rawCpmk !== '') {
                $currentCpmk = $rawCpmk;
            }

            // ==========================
            // SCPMK
            // ==========================
            $rawSub = trim(
                (string) ($header2[$col] ?? '')
            );

            if ($rawSub === '') {
                continue;
            }

            preg_match(
                '/([A-Za-z0-9\-]+)\s*\(P\-(\d+)\)/i',
                $rawSub,
                $matches
            );

            $kodeScpmk = $matches[1] ?? $rawSub;
            $pertemuan = $matches[2] ?? null;

            // $kodeScpmk = preg_replace(
            //     '/[^A-Za-z0-9]/',
            //     '',
            //     $kodeScpmk
            // );

            // bobot dari excel persen
            $bobot = (float) ($header3[$col] ?? 0);

            if ($bobot > 1) {
                $bobot /= 100;
            }

            $subCpmkColumns[$col] = [
                'cpmk' => $currentCpmk, // ← penting
                'kode_scpmk' => $kodeScpmk,
                'pertemuan' => $pertemuan,
                'bobot' => $bobot,
            ];
        }

        // ================================
        // INI YANG KURANG
        // ================================
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
        $nilaiHurufIndex = $nilaiAngkaIndex + 2;

        // ==================================================
        // DATA MAHASISWA
        // ==================================================
        $dataRows = array_slice($allData, 3);

        foreach ($dataRows as $rowIndex => $row) {

            if (
                collect($row)
                    ->filter(fn ($v) => trim((string) $v) !== ''
                    )
                    ->isEmpty()
            ) {
                continue;
            }

            $subCpmk = [];

            foreach (
                $subCpmkColumns as $col => $meta
            ) {

                $nilaiRaw = $row[$col] ?? '';
                $subCpmk[] = [
                    'cpmk' => $meta['cpmk'],
                    'kode_scpmk' => $meta['kode_scpmk'],
                    'pertemuan' => $meta['pertemuan'],
                    'bobot' => $meta['bobot'],
                    'nilai' => is_numeric($nilaiRaw)
                        ? (float) $nilaiRaw
                        : null,
                ];
            }

            $this->parsedNilaiRows[] = [
                '_index' => count(
                    $this->parsedNilaiRows
                ),

                'kode_mk' => trim(
                    (string) ($row[0] ?? '')
                ),

                'nama_mk' => trim(
                    (string) ($row[1] ?? '')
                ),

                'kelas_kuliah' => trim(
                    (string) ($row[2] ?? '')
                ),

                'nim' => trim(
                    (string) ($row[3] ?? '')
                ),

                'nama' => trim(
                    (string) ($row[4] ?? '')
                ),

                'angkatan' => trim(
                    (string) ($row[5] ?? '')
                ),

                'sub_cpmk' => $subCpmk,

                'nilai_angka' => is_numeric(
                    $row[$nilaiAngkaIndex] ?? null
                )
                    ? (float)
                        $row[$nilaiAngkaIndex]
                    : 0,

                'nilai_index' => is_numeric(
                    $row[$nilaiIndexIndex] ?? null
                )
                    ? (float)
                        $row[$nilaiIndexIndex]
                    : null,

                'nilai_huruf' => strtoupper(
                    trim(
                        (string)
                        ($row[$nilaiHurufIndex]
                        ?? '')
                    )
                ),

                'role' => 'mahasiswa',
            ];
        }

        $this->toast(
            text: 'File Excel berhasil dimuat. Silakan periksa nilai mahasiswa!'
        );
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

    public function updatedParsedNilaiRows($value, $key)
    {
        if (preg_match('/^(\d+)\.sub_cpmk\.(\d+)\.nilai$/', $key, $matches)) {
            $rowIndex = (int) $matches[1];
            $this->recalculateRowNilai($rowIndex);
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
            $row['nilai_huruf'] = 'A';
        } elseif ($average >= 71) {
            $row['nilai_index'] = 3;
            $row['nilai_huruf'] = 'B';
        } elseif ($average >= 56) {
            $row['nilai_index'] = 2;
            $row['nilai_huruf'] = 'C';
        } elseif ($average >= 41) {
            $row['nilai_index'] = 1;
            $row['nilai_huruf'] = 'D';
        } else {
            $row['nilai_index'] = 0;
            $row['nilai_huruf'] = 'E';
        }
    }

    public function saveNilaiExcel()
    {
        if (empty($this->parsedNilaiRows)) {
            $this->toast(text: 'Tidak ada data nilai untuk disimpan!', variant: 'warning');

            return;
        }

        try {
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
                        // Validasi data per row sebelum insert/update database
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

                $message = "Memproses nilai... $successCount dari $total mahasiswa berhasil disimpan.";
                $this->stream(to: 'import-progress', content: $message, replace: true);
            });

        // Hapus baris yang sukses dari array preview
        foreach (array_reverse($successfulIndices) as $idx) {
            unset($this->parsedNilaiRows[$idx]);
            unset($this->rowNilaiErrors[$idx]);
        }
        $this->parsedNilaiRows = array_values($this->parsedNilaiRows);

        // Re-index sisa error jika ada yang gagal
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

    private function getSesiImportNilai()
    {
        $idJadwal = $this->jadwal_id ?? $this->jadwal?->id;

        return $this->inputSesiSearch($idJadwal)->get()->sortBy('pertemuan_ke')->values();
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

            $nilaiMahasiswa = NilaiMahasiswa::query()
                ->firstOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'kj_id' => $this->jadwal_id,
                    ],
                    [
                        'nilai_array' => [],
                    ]
                );

            $nilaiArray = $nilaiMahasiswa->nilai_array ?? [];
            $bobotArray = $nilaiMahasiswa->bobot_array ?? [];

            // =====================================
            // ambil sesi dari jadwal
            // =====================================
            $sesis = $this->getSesiImportNilai();

            $mapScpmk = [];

            foreach ($sesis as $index => $sesi) {

                $kodeScpmk = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    $sesi->scpmk_atr?->kode
                        ?? $sesi->scpmk_atr?->kode_scpmk
                        ?? ''
                );

                if ($kodeScpmk !== '') {
                    $mapScpmk[$kodeScpmk] = $index;
                }
            }

            // =====================================
            // inject nilai dari excel
            // =====================================
            foreach (
                $validated['sub_cpmk'] ?? [] as $sub
            ) {

                $kodeScpmk = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    $sub['kode_scpmk'] ?? ''
                );

                if (
                    empty($kodeScpmk)
                    || ! isset(
                        $mapScpmk[$kodeScpmk]
                    )
                ) {
                    continue;
                }

                $targetIndex =
                    $mapScpmk[$kodeScpmk];

                $nilaiArray[$targetIndex] =
                    is_numeric(
                        $sub['nilai'] ?? null
                    )
                    ? (float) $sub['nilai']
                    : null;

                $bobotArray[$targetIndex] =
                    is_numeric(
                        $sub['bobot'] ?? null
                    )
                    ? (float) $sub['bobot']
                    : 0;
            }

            $nilaiMahasiswa->update([
                'nilai_array' => $nilaiArray,
                'bobot_array' => $bobotArray,
                'nilai' => $validated['nilai_angka'],
            ]);
        });
    }

    private function inputModalNilai($data)
    {
        $rules = [
            'nim' => 'required|string',
            'nama' => 'required|string|max:255',

            'nilai_angka' => 'required|numeric|min:0|max:100',

            'nilai_index' => 'nullable|numeric',

            'nilai_huruf' => 'nullable|string|max:2',

            // ==========================
            // SUB CPMK
            // ==========================
            'sub_cpmk' => 'nullable|array',

            'sub_cpmk.*.kode_scpmk' => 'nullable|string',

            'sub_cpmk.*.nilai' => 'nullable|numeric|min:0|max:100',

            'sub_cpmk.*.bobot' => 'nullable|numeric',
        ];

        return Validator::make(
            $data,
            $rules,
            [
                'nim.required' => 'NIM mahasiswa wajib diisi!',

                'nama.required' => 'Nama mahasiswa wajib diisi!',

                'nilai_angka.required' => 'Nilai angka wajib diisi!',

                'nilai_angka.numeric' => 'Nilai harus berupa angka!',

                'nilai_angka.min' => 'Nilai minimal 0!',

                'nilai_angka.max' => 'Nilai maksimal 100!',
            ]
        )->validate();
    }

    public function loadingNilaiExcel() {}
}
