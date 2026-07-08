<?php

namespace App\Livewire\Admin\UserManagement;

use App\Exports\UserExport;
use App\Livewire\Global\HasToast;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
// use App\Models\Auth\Membership;
// use App\Models\Auth\Team;
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

    public $excel_user_file;

    public array $parsedUserRows = [];

    public array $rowUserErrors = [];

    public $excelUserPerPage = 20;

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

        if ($this->switchTable == 'mahasiswa') {
            if (empty($this->filterAngkatan)) {
                if (! empty($this->searchAngkatan)) {
                    $tag .= '_Angkatan '.$this->searchAngkatan;
                    $TAG .= ' ANGKATAN '.$this->searchAngkatan;
                }
            } else {
                $tag .= '_Angkatan '.$this->filterAngkatan;
                $TAG .= ' ANGKATAN '.$this->filterAngkatan;
            }
        }

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
        $fileNameSafe = str_replace('/', '-', $fileName);
        $title = 'DATA '.$TAG.' '.$sINPUT.$UNIV;

        if ($this->searchMode == 'full') {
            $users = $this->searchOutputUser($queryUser, $this->search, $this->searchAngkatan, null, $this->sortField, $this->sortDirection);
        } else {
            $users = $queryUser;
        }

        return Excel::download(new UserExport($users, $this->switchTable, $title), $fileNameSafe);
    }

    public function getPaginatedUserRowsProperty()
    {
        $items = collect($this->parsedUserRows)
            ->map(function ($row, $index) {

                return array_merge($row, [
                    '_index' => $index,
                ]);
            });

        $page = $this->getPage('excelPage');

        return new LengthAwarePaginator(
            $items->forPage($page, $this->excelUserPerPage)->values()->toArray(),
            $items->count(),
            $this->excelUserPerPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'excelPage',
            ]
        );
    }

    public function importUserExcel()
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ($this->roleType !== 'excel') {
            return;
        }

        $this->reset(['parsedUserRows', 'rowUserErrors']);
        if (method_exists($this, 'setPage')) {
            $this->setPage(1, 'excelPage');
        }
        $this->validate([
            'excel_user_file' => 'required|array',
            'excel_user_file.*' => 'file|mimes:xlsx,xls|max:10240',
        ]);

        $this->parsedUserRows = [];
        $totalSheetsProcessed = 0;

        // 1. LOOP UTAMA: Mengulang setiap file berkas yang diunggah
        foreach ($this->excel_user_file as $singleFile) {

            $spreadsheet = IOFactory::load($singleFile->getRealPath());
            $allSheets = $spreadsheet->getAllSheets();

            // 2. LOOP KEDUA: Mengulang setiap Halaman (Sheet) di dalam berkas saat ini
            foreach ($allSheets as $worksheet) {
                $allData = $worksheet->toArray();
                if (empty($allData)) {
                    continue;
                }

                /** ===============================
                 * CARI HEADER (Robust Search)
                 * =============================== */
                $headerRowIndex = null;
                $secondHeaderRowIndex = null;

                foreach ($allData as $i => $row) {
                    $rowValues = collect($row)->map(fn ($v) => Str::lower(trim((string) $v)))->filter()->values();

                    if ($rowValues->contains('email') || $rowValues->contains('role') || $rowValues->contains('nama')) {
                        $headerRowIndex = $i;
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
                    continue;
                }

                $totalSheetsProcessed++;

                $rawHeader1 = $allData[$headerRowIndex];
                $rawHeader2 = $secondHeaderRowIndex !== null ? $allData[$secondHeaderRowIndex] : [];

                $headers = [];
                foreach ($rawHeader1 as $idx => $value) {
                    $val1 = Str::lower(trim((string) $value));
                    $val2 = isset($rawHeader2[$idx]) ? Str::lower(trim((string) $rawHeader2[$idx])) : '';

                    $finalHeader = $val1;
                    if ($val2 !== '' && ($val1 === '' || $val1 === 'identitas (id)' || str_contains($val1, 'pendidikan') || str_contains($val1, 'pangkat'))) {
                        $finalHeader = $val2;
                    }

                    if ($finalHeader !== '') {
                        $headers[$idx] = $finalHeader;
                    }
                }

                /** ===============================
                 * PARSE DATA KE TABLE PREVIEW
                 * =============================== */
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

                    $this->parsedUserRows[] = [
                        'email' => $data['email'] ?? '',
                        'password' => $data['password'] ?? '',
                        'name' => $data['name'] ?? $data['nama'] ?? '',
                        'nip' => $data['nip'] ?? '',
                        'nitk' => $data['nitk'] ?? '',
                        'nidn' => $data['nidn'] ?? '',
                        'nidk' => $data['nidk'] ?? '',
                        'nim' => $data['nim'] ?? '',
                        'nik' => $data['nik'] ?? '',
                        'no_hp' => $data['no. hp'] ?? $data['no hp'] ?? $data['no telepon'] ?? $data['telepon'] ?? $data['wa'] ?? $data['no wa'] ?? '',
                        'agama' => $data['agama'] ?? $data['kepercayaan'] ?? '',
                        'jenis_kelamin' => $data['jenis kelamin'] ?? $data['gender'] ?? '',
                        'tempat_lahir' => $data['tempat lahir'] ?? $data['tmt lahir'] ?? '',
                        'tanggal_lahir' => $data['tanggal lahir'] ?? $data['tgl lahir'] ?? '',
                        'kode_wilayah' => strtoupper(($data['kode wilayah'] ?? $data['kode kampus'] ?? '')),
                        'angkatan' => $data['tahun angkatan'] ?? $data['angkatan'] ?? '',
                        'role' => ucfirst($data['role'] ?? ''),
                    ];
                }
            } // 🌟 Akhir loop sheet
        } // Akhir loop file

        $this->toast(
            text: 'Berhasil memuat '.count($this->excel_user_file).' file ('.$totalSheetsProcessed.' sheet). Silakan periksa data!'
        );
    }

    public function clearUserExcelFile()
    {
        $this->excel_user_file = null;
        $this->reset([
            'parsedUserRows',
            'rowUserErrors',
        ]);
        if (method_exists($this, 'setPage')) {
            $this->setPage(1, 'excelPage');
        }
        $this->dispatch('reset-file-input', id: 'excel_user_file');

        $this->toast(type: 'info', text: 'File berkas user berhasil dihapus.');
    }

    public function updatedExcelUserFile()
    {
        if ($this->roleType !== 'excel') {
            return;
        }

        if (! $this->excel_user_file) {
            return;
        }

        try {
            $this->importUserExcel();
        } catch (\Throwable $e) {
            $this->toast(text: $e->getMessage(), variant: 'danger');

        }
    }

    public function removeParsedUserRow($index)
    {
        if (isset($this->parsedUserRows[$index])) {
            unset($this->parsedUserRows[$index]);
            $this->parsedUserRows = array_values($this->parsedUserRows);
            $this->toast(text: 'Baris dihapus!');
        }
    }

    public function saveUserExcel()
    {
        $rules = [
            'excel_user_file' => 'required|array',
            'excel_user_file.*' => 'file|mimes:xlsx,xls|max:27648',
            'pr_id' => 'required|exists:prodis,id',
        ];
        $this->validate($rules, $this->validationMessagesUser());

        if (empty($this->parsedUserRows)) {
            $this->toast(text: 'Tidak ada data untuk disimpan!', variant: 'warning');

            return;
        }

        try {
            $this->stream('import-progress', 'Inisialisasi pemrosesan...');
            $this->procesImportUserExcel();
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: '❌ '.$e->getMessage());
        }
    }

    public function procesImportUserExcel()
    {
        $successCount = 0;
        $this->rowUserErrors = [];
        $successfulIndices = [];
        $originalRoleType = $this->roleType;

        $total = count($this->parsedUserRows);

        LazyCollection::make($this->parsedUserRows)
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

                        $validatedData = $this->inputModalUser(false, $dataToValidate, strtolower($row['role']));
                        $this->saveUserFromExcel($validatedData, strtolower($row['role']));

                        $successfulIndices[] = $index;
                        $successCount++;
                    } catch (ValidationException $e) {
                        $this->rowUserErrors[$index] = $e->errors();
                    } catch (\Throwable $e) {
                        $this->rowUserErrors[$index] = ['general' => [$e->getMessage()]];
                    }
                }
                $message = "Sedang memproses... $successCount dari $total data berhasil masuk!";
                $this->stream(
                    to: 'import-progress',
                    content: $message,
                    replace: true
                );

            });

        $this->roleType = $originalRoleType;

        foreach (array_reverse($successfulIndices) as $idx) {
            unset($this->parsedUserRows[$idx]);
            unset($this->rowUserErrors[$idx]);
        }

        $this->parsedUserRows = array_values($this->parsedUserRows);

        $newRowErrors = [];
        $i = 0;
        foreach ($this->rowUserErrors as $oldIdx => $errors) {
            $newRowErrors[$i] = $errors;
            $i++;
        }
        $this->rowUserErrors = $newRowErrors;

        $failCount = count($this->parsedUserRows);
        $messageText = "Import selesai | Berhasil: $successCount | Gagal: $failCount";

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_user_file');
            $this->resetInputUser();
            $this->showUserExcelModal = false;
        } else {
            $this->toast(text: $messageText, variant: 'warning');

        }
        $this->dispatch('refresh-data-user');
        $this->dispatch('refresh-stats-user');
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
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],

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
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],

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
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],

                    'status' => $validated['status'],
                ]);
            }

            // $team = Team::forceCreate([
            //     'id' => $user->id,
            //     'name' => explode(' ', $validated['name'])[0]."'s Team",
            //     'is_personal' => true,
            // ]);

            // Membership::create([
            //     'team_id' => $team->id,
            //     'user_id' => $user->id,
            //     'role' => 'owner',
            // ]);

            // $user->forceFill(['current_team_id' => $team->id])->save();
        });
    }

    public function loadingUserExcel() {}
}
