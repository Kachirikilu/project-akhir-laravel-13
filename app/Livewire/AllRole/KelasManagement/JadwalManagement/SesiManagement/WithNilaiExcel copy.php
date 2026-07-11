<?php

namespace App\Livewire\AllRole\KelasManagement\JadwalManagement\SesiManagement;

use App\Exports\NilaiExport;
use App\Livewire\Global\HasToast;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $spreadsheet = IOFactory::load($this->excel_nilai_file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $allData = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong!');
        }

        /** ===============================
         *  CARI HEADER (Robust Search)
         *  =============================== */
        $headerRowIndex = null;
        $secondHeaderRowIndex = null;

        foreach ($allData as $i => $row) {
            $rowValues = collect($row)->map(fn ($v) => Str::lower(trim((string) $v)))->filter()->values();

            // Cari baris yang mengandung kata kunci utama (Email/Role/Nama)
            // Lewati judul (biasanya cuma 1 cell besar)
            if ($rowValues->contains('email') || $rowValues->contains('role') || $rowValues->contains('nama')) {
                $headerRowIndex = $i;
                // Jika baris berikutnya juga punya konten (untuk handle double header)
                if (isset($allData[$i + 1])) {
                    $nextRow = collect($allData[$i + 1])->filter(fn ($v) => trim((string) $v) !== '');
                    if ($nextRow->count() > 0) {
                        $secondHeaderRowIndex = $i + 1;
                    }
                }
                break;
            }
        }

        if ($headerRowIndex === null) {
            throw new \Exception('Header tidak ditemukan. Pastikan file memiliki kolom Email/Nama/Role!');
        }

        $rawHeader1 = $allData[$headerRowIndex];
        $rawHeader2 = $secondHeaderRowIndex !== null ? $allData[$secondHeaderRowIndex] : [];

        $headers = [];
        foreach ($rawHeader1 as $idx => $value) {
            $val1 = Str::lower(trim((string) $value));
            $val2 = isset($rawHeader2[$idx]) ? Str::lower(trim((string) $rawHeader2[$idx])) : '';

            // Gabungkan atau pilih header yang lebih spesifik
            $finalHeader = $val1;
            if ($val2 !== '' && ($val1 === '' || $val1 === 'identitas (id)' || str_contains($val1, 'pendidikan') || str_contains($val1, 'pangkat'))) {
                $finalHeader = $val2;
            }

            if ($finalHeader !== '') {
                $headers[$idx] = $finalHeader;
            }
        }

        /** ===============================
         *  PARSE DATA KE TABLE PREVIEW
         *  =============================== */
        $startDataIndex = ($secondHeaderRowIndex ?? $headerRowIndex) + 1;
        $dataRows = array_slice($allData, $startDataIndex);

        foreach ($dataRows as $excelIndex => $row) {
            if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->count() === 0) {
                continue;
            }

            $data = [];
            foreach ($headers as $col => $header) {
                $data[$header] = trim((string) ($row[$col] ?? ''));
            }

            // Mapping yang lebih fleksibel untuk mendukung berbagai format header
            $this->parsedNilaiRows[] = [
                'email' => $data['email'] ?? '',
                'password' => $data['password'] ?? '12345678',
                'name' => $data['name'] ?? $data['nama'] ?? '',
                'nip' => $data['nip'] ?? '',
                'nitk' => $data['nitk'] ?? '',
                'nidn' => $data['nidn'] ?? '',
                'nidk' => $data['nidk'] ?? '',
                'nim' => $data['nim'] ?? '',
                'nik' => $data['nik'] ?? '',
                'kode_wilayah' => strtoupper(($data['kode wilayah'] ?? $data['kode kampus']) ?? 'IDL'),
                'angkatan' => $data['tahun angkatan'] ?? $data['angkatan'] ?? '',
                'role' => strtolower($data['role'] ?? ''),
            ];
        }

        $this->toast(text: 'File Excel berhasil dimuat. Silakan periksa data!');
    }

    public function updatedExcelNilaiFile()
    {
        if ($this->roleType !== 'excel') {
            return;
        }

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
            $this->toast(text: 'Baris dihapus!');
        }
    }

    public function saveNilaiExcel()
    {
        $rules = [
            'excel_nilai_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'pr_id' => 'required|exists:prodis,id',
        ];
        $this->validate($rules, $this->validationMessagesNilai());

        if (empty($this->parsedNilaiRows)) {
            $this->toast(text: 'Tidak ada data untuk disimpan!', variant: 'warning');

            return;
        }

        try {
            $this->stream('import-progress', 'Inisialisasi pemrosesan...');
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
        $originalRoleType = $this->roleType;

        $total = count($this->parsedNilaiRows);

        LazyCollection::make($this->parsedNilaiRows)
            ->chunk(20)
            ->each(function ($chunk) use (&$successCount, &$successfulIndices, $total) {
                foreach ($chunk as $index => $row) {
                    try {
                        $this->roleType = $row['role'];
                        $this->selected_id_user = null;

                        $dataToValidate = $row;
                        $dataToValidate['pr_id'] = $this->pr_id;
                        if (empty($dataToValidate['status'])) {
                            $dataToValidate['status'] = 'Aktif';
                        }

                        $validatedData = $this->inputModalNilai(false, $dataToValidate);
                        $this->saveNilaiFromExcel($validatedData, $row['role']);

                        $successfulIndices[] = $index;
                        $successCount++;
                    } catch (ValidationException $e) {
                        $this->rowNilaiErrors[$index] = $e->errors();
                    } catch (\Throwable $e) {
                        $this->rowNilaiErrors[$index] = ['general' => [$e->getMessage()]];
                    }
                }
                $message = "Sedang memproses... $successCount dari $total data berhasil masuk.";
                $this->stream(
                    to: 'import-progress',
                    content: $message,
                    replace: true
                );

            });

        $this->roleType = $originalRoleType;

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
        $messageText = "Import selesai | Berhasil: $successCount | Gagal: $failCount";

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_nilai_file');
            $this->resetInputUser();
            $this->dispatch('refresh-data-user');
            $this->showUserExcelModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');
        }

        $this->dispatch('refresh-data-user');
    }

    private function saveNilaiFromExcel($validated, $role)
    {
        DB::transaction(function () use ($validated, $role) {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if ($role === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nip' => $validated['nip'],
                    'nitk' => $validated['nitk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'status' => $validated['status'],
                ]);
            }

            $team = Team::forceCreate([
                'id' => $user->id,
                'name' => explode(' ', $validated['name'])[0]."'s Team",
                'is_personal' => true,
            ]);

            Membership::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'role' => 'owner',
            ]);

            $user->forceFill(['current_team_id' => $team->id])->save();
        });
    }

    public function loadingNilaiExcel() {}

    private function inputModalNilai($isEditingNilai, $data)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->selected_id_user),
            ],
            'password' => $isEditingNilai ? 'nullable|min:8' : 'required|min:8',
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
        ];

        /* ===================== ADMIN ===================== */
        if ($this->roleType === 'admin') {
            $rules['kode_wilayah'] = [
                'required',
                Rule::in(['IDL', 'PLG']),
            ];

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Produktif)
                    'Tugas Belajar',          // Kuning (Transisi/Sementara)
                    'Mutasi',                 // Kuning (Transisi/Sementara)
                    'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                    'Resign',                 // Orange (Keluar Prosedural)
                    'Pensiun',                // Orange (Keluar Prosedural)
                    'Diberhentikan',          // Merah (Masalah/Sanksi)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        $rules['pr_id'] = 'required|exists:prodis,id';

        $validator = Validator::make($data, $rules, $this->validationMessagesNilai());

        $validator->after(function ($validator) {});

        return $validator->validate();
    }

    public function validationMessagesNilai()
    {
        return [
            'email.required' => 'Alamat Email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email ini sudah terdaftar di sistem!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
            'name.required' => 'Nama lengkap wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
        ];
    }
}
