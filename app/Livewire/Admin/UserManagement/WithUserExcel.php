<?php

namespace App\Livewire\Admin\UserManagement;

use App\Exports\UserExport;
use App\Livewire\Global\HasToast;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\Team;
use App\Models\Auth\Membership;
use App\Models\Auth\User;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
// use Illuminate\Support\LazyCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait WithUserExcel
{
    use HasToast;
    use WithFileUploads;

    public $excel_file;

    public array $parsedRows = [];

    public array $rowErrors = [];

    public $excelPerPage = 20;

    public function exportUserExcel()
    {
        $queryUser = $this->inputUserSearch();
        $this->buttonUserFilter($queryUser);

        $queryUser->with(['pendidikans' => fn ($q) => $q->orderByJenjang()]);
        if (! empty($this->switchTable)) {
            $queryUser->whereHas($this->switchTable);
        }

        $univ = env('UNIVERSITAS');
        $UNIV = strtoupper($univ);

        $filter = '';
        if ($this->filterStatus == 'user-aktif') {
            $filter = ' Aktif';
        } elseif ($this->filterStatus == 'user-non-aktif') {
            $filter = ' Tidak Aktif';
        }

        $tag = ucwords(empty($this->switchTable) ? 'Pengguna' : $this->switchTable).$filter;
        $TAG = strtoupper($tag);

        $sInput = '';
        $sINPUT = '';
        if ($this->filterStatus !== '') {
            if ($this->selectedFkId) {
                $fk = Fakultas::find($this->selectedFkId);
                $sInput = $fk->fakultas_fk.'_';
                $sINPUT = strtoupper($fk->fakultas_fk.' ');
            } elseif ($this->selectedDpId) {
                $dp = Departemen::find($this->selectedDpId);
                $sInput = $dp->departemen_dp.'_';
                $sINPUT = strtoupper($dp->departemen_dp.' ');
            } elseif ($this->selectedPrId) {
                $pr = Prodi::find($this->selectedPrId);
                $sInput = $pr->prodi.'_';
                $sINPUT = strtoupper($pr->prodi_pr.' ');
            }
        } else {
            $sInput = Auth::user()->prodi.'_';
            $sINPUT = strtoupper(Auth::user()->prodi_pr.' ');
        }

        $fileName = 'Data_'.$tag.'_'.$sInput.$univ.'_'.now()->format('Y-m-d').'.xlsx';
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        return Excel::download(new UserExport($queryUser, $this->switchTable, $title), $fileName);
    }

    // public function getPaginatedRowsProperty()
    // {
    //     return collect($this->parsedRows)
    //         ->slice(($this->excelPage - 1) * $this->excelPerPage, $this->excelPerPage)
    //         ->all();
    // }

    public function getPaginatedRowsProperty()
    {
        $items = collect($this->parsedRows)
            ->map(function ($row, $index) {

                return array_merge($row, [
                    '_index' => $index,
                ]);
            });

        $page = $this->getPage('excelPage');

        return new LengthAwarePaginator(
            $items->forPage($page, $this->excelPerPage)->values()->toArray(),
            $items->count(),
            $this->excelPerPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'excelPage',
            ]
        );
    }

    // public function getExcelTotalPagesProperty()
    // {
    //     return ceil(count($this->parsedRows) / $this->excelPerPage);
    // }

    // public function setExcelPage($page)
    // {
    //     $this->excelPage = max(1, min($page, $this->excelTotalPages));
    // }

    // public function getExcelPaginationElementsProperty()
    // {
    //     $total = $this->excelTotalPages;
    //     $current = $this->excelPage;
    //     $onEachSide = 1;

    //     if ($total <= 7) {
    //         return [range(1, $total)];
    //     }

    //     $elements = [];

    //     if ($current <= $onEachSide + 4) {
    //         $elements[] = range(1, $onEachSide + 4);
    //         $elements[] = '...';
    //         $elements[] = [$total - 1, $total];
    //     } elseif ($current > $total - ($onEachSide + 4)) {
    //         $elements[] = [1, 2];
    //         $elements[] = '...';
    //         $elements[] = range($total - ($onEachSide + 4), $total);
    //     } else {
    //         $elements[] = [1, 2];
    //         $elements[] = '...';
    //         $elements[] = range($current - $onEachSide, $current + $onEachSide);
    //         $elements[] = '...';
    //         $elements[] = [$total - 1, $total];
    //     }

    //     return $elements;
    // }

    public function importUserExcel()
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ($this->roleType !== 'file') {
            return;
        }

        $this->reset(['parsedRows', 'rowErrors']);

        $this->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $spreadsheet = IOFactory::load($this->excel_file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $allData = $worksheet->toArray();

        if (empty($allData)) {
            throw new \Exception('File Excel kosong');
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
            throw new \Exception('Header tidak ditemukan. Pastikan file memiliki kolom Email/Nama/Role.');
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
            $this->parsedRows[] = [
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

    public function updatedExcelFile()
    {
        if ($this->roleType !== 'file') {
            return;
        }

        if (! $this->excel_file) {
            return;
        }

        try {
            $this->importUserExcel();
        } catch (\Throwable $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');

        }
    }

    public function removeParsedRow($index)
    {
        if (isset($this->parsedRows[$index])) {
            unset($this->parsedRows[$index]);
            $this->parsedRows = array_values($this->parsedRows);
            $this->toast(text: 'Baris dihapus!');
        }
    }

    public function saveAllRows()
    {
        $rules = [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'pr_id' => 'required|exists:prodis,id',
        ];
        $this->validate($rules, $this->validationMessagesUser());

        if (empty($this->parsedRows)) {
            $this->toast(text: 'Tidak ada data untuk disimpan!', variant: 'warning');

            return;
        }

        try {
            $this->stream('import-progress', 'Inisialisasi pemrosesan...');
            $this->processImport();
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ '.$e->getMessage());
        }
    }

    public function processImport()
    {
        $successCount = 0;
        $this->rowErrors = [];
        $successfulIndices = [];
        $originalRoleType = $this->roleType;

        $total = count($this->parsedRows);

        LazyCollection::make($this->parsedRows)
            ->chunk(50)
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

                        $validatedData = $this->inputModalUser(false, $dataToValidate);
                        $this->saveUserFromExcel($validatedData, $row['role']);

                        $successfulIndices[] = $index;
                        $successCount++;
                    } catch (ValidationException $e) {
                        $this->rowErrors[$index] = $e->errors();
                    } catch (\Throwable $e) {
                        $this->rowErrors[$index] = ['general' => [$e->getMessage()]];
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
            unset($this->parsedRows[$idx]);
            unset($this->rowErrors[$idx]);
        }

        $this->parsedRows = array_values($this->parsedRows);

        $newRowErrors = [];
        $i = 0;
        foreach ($this->rowErrors as $oldIdx => $errors) {
            $newRowErrors[$i] = $errors;
            $i++;
        }
        $this->rowErrors = $newRowErrors;

        $failCount = count($this->parsedRows);
        $messageText = "Import selesai | Berhasil: $successCount | Gagal: $failCount";

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_file');
            $this->resetInputUser();
            $this->dispatch('refresh-data-user');
            $this->showUserExcelModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');
        }

        $this->dispatch('refresh-data-user');
    }

    private function saveUserFromExcel($validated, $role)
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
            } elseif ($role === 'dosen') {
                Dosen::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nip' => $validated['nip'],
                    'nidn' => $validated['nidn'] ?? null,
                    'nidk' => $validated['nidk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                ]);
            } elseif ($role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nim' => $validated['nim'],
                    'nik' => $validated['nik'],
                    'angkatan' => $validated['angkatan'],
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

    public function loadingUserExcel() {}
}
