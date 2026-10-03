<?php

namespace App\Livewire\Admin\UserManagement;

use App\Http\Services\UserService;
use App\Exports\UserExport;
use App\Jobs\ProcessUserExcelQueueJob;
use App\Livewire\Global\HasToast;
use App\Models\Auth\Admin;
// use App\Models\Auth\Membership;
// use App\Models\Auth\Team;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use App\Models\Auth\UserExcelQueue;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

trait WithUserExcel
{
    use HasToast;
    use WithFileUploads;

    public $excel_user_file;

    public array $parsedUserRows = [];

    public array $rowUserErrors = [];

    public $excelUserPerPage = 20;

    public $noPreview = true;

    public $uploadedFileNames = [];

    public $update_or_create_mode = false;

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

        if ($this->searchMode == 'complex') {
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
        if (! $this->AuthCheck() || $this->roleType !== 'excel') {
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

        $service = app(UserService::class);
        $this->parsedUserRows = [];

        foreach ($this->excel_user_file as $singleFile) {
            // Panggil service untuk membaca real path file temporary Livewire
            $rows = $service->parseExcelToArray($singleFile->getRealPath());
            $this->parsedUserRows = array_merge($this->parsedUserRows, $rows);
        }

        $this->toast(
            text: 'Berhasil memuat '.count($this->excel_user_file).' file ('.count($this->parsedUserRows).' baris data). Silakan periksa data!'
        );
    }

    public function togglePreview()
    {
        $this->noPreview = ! $this->noPreview;
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
        $files = is_array($this->excel_user_file) ? $this->excel_user_file : [$this->excel_user_file];
        foreach ($files as $file) {
            $fileName = $file->getClientOriginalName();
            if (! in_array($fileName, $this->uploadedFileNames)) {
                $this->uploadedFileNames[] = $fileName;

                try {
                    $this->importUserExcel($file);
                } catch (\Throwable $e) {
                    $this->toast(text: "Gagal memproses {$fileName}: ".$e->getMessage(), variant: 'danger');
                }
            }
        }
    }

    public function clearUserExcelFile()
    {
        $this->excel_user_file = null;
        $this->reset([
            'parsedUserRows',
            'rowUserErrors',
            'uploadedFileNames',
        ]);
        if (method_exists($this, 'setPage')) {
            $this->setPage(1, 'excelPage');
        }
        $this->dispatch('reset-file-input', id: 'excel_user_file');

        $this->toast(type: 'info', text: 'File berkas user berhasil dihapus.');
    }

    public function removeParsedUserRow($index)
    {
        if (isset($this->parsedUserRows[$index])) {
            unset($this->parsedUserRows[$index]);
            $this->parsedUserRows = array_values($this->parsedUserRows);
            $this->toast(text: 'Baris dihapus!');
        }
    }

    protected function rulesUserExcelImport(): array
    {
        return [
            'excel_user_file' => 'required|array|min:1',
            'excel_user_file.*' => 'file|mimes:xlsx,xls|max:27648',
            'pr_id' => 'required|exists:prodis,id',
            'user_input.role' => 'nullable|in:Admin,Dosen,Mahasiswa',
            'user_input.update_or_create_mode' => 'boolean',
            'user_input.update_or_create' => 'nullable|required_if:update_or_create_mode,1,true|in:identity1,nik,email',
        ];
    }

    public function saveUserExcel()
    {
        $this->validate(
            $this->rulesUserExcelImport(),
            $this->validationMessagesUser()
        );

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

    public function saveUserAntrian()
    {
        $this->validate(
            $this->rulesUserExcelImport(),
            $this->validationMessagesUser()
        );

        try {
            $files = $this->excel_user_file;
            $prId = $this->pr_id;

            $role = ! empty($this->user_input['role']) ? $this->user_input['role'] : null;
            $isUpdateOrCreate = ! empty($this->user_input['update_or_create_mode']);
            $updateBy = $isUpdateOrCreate ? ($this->user_input['update_or_create'] ?? 'identity1') : null;

            $dispatchedCount = 0;

            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();

                // 1. Simpan file fisik ke storage
                $storedPath = $file->storeAs(
                    'imports/users/temp',
                    time().'_'.uniqid().'_'.Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension()
                );

                // 2. Buat Record Tracking di Database
                $importRecord = UserExcelQueue::create([
                    'user_id' => auth()->id(),
                    'type' => 'user',
                    'original_name' => $originalName,
                    'file_path' => $storedPath,
                    'parameters' => [
                        'pr_id' => $prId,
                        'role' => $role,
                        'update_or_create_mode' => $isUpdateOrCreate,
                        'update_or_create' => $updateBy,
                    ],
                    'status' => 'pending',
                ]);

                ProcessUserExcelQueueJob::dispatch($importRecord->id);

                $dispatchedCount++;
            }

            $this->resetInputUser();

            $this->toast(
                text: "Berhasil memasukkan {$dispatchedCount} file ke dalam antrean sistem!",
                variant: 'success'
            );

            $this->dispatch('refresh-table');
            $this->dispatch('refresh-stats-user');
            $this->dispatch('next-antrian-step');

        } catch (\Throwable $e) {
            $this->toast(text: '❌ Gagal memproses antrean: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function downloadFile($importId)
    {
        $import = UserExcelQueue::findOrFail($importId);

        if (Storage::exists($import->file_path)) {
            return Storage::download($import->file_path, $import->original_name);
        }

        $this->toast(text: 'File fisik sudah tidak tersedia di server!', variant: 'danger');
    }

    public $selectedErrorLogs = [];

    public $showErrorModal = false;

    public function showErrorDetail($importId)
    {
        $import = UserExcelQueue::findOrFail($importId);
        $this->selectedErrorLogs = $import->row_errors ?? [];
        $this->showErrorModal = true;
    }

    private function resolveExistingUserId(array $row, string $role): ?int
    {
        $mode = $this->user_input['update_or_create'] ?? 'identity1';
        $lowerRole = strtolower($role);

        if ($mode === 'email') {
            $email = $row['email'] ?? null;
            if (! empty($email)) {
                $user = User::where('email', $email)->first();

                return $user ? $user->id : null;
            }

            return null;
        }

        $modelClass = match ($lowerRole) {
            'admin' => Admin::class,
            'dosen' => Dosen::class,
            'mahasiswa' => Mahasiswa::class,
            default => null,
        };

        if (! $modelClass) {
            return null;
        }

        $query = $modelClass::query();

        if ($mode === 'identity1') {
            $identityColumn = ($lowerRole === 'mahasiswa') ? 'nim' : 'nip';
            $searchValue = $row[$identityColumn] ?? null;

            if ($searchValue) {
                $record = $query->where($identityColumn, $searchValue)->first();

                return $record ? $record->user_id : null;
            }
        } elseif ($mode === 'nik') {
            $searchValue = $row['nik'] ?? null;

            if ($searchValue) {
                $record = $query->where('nik', $searchValue)->first();

                return $record ? $record->user_id : null;
            }
        }

        return null;
    }

    public function procesImportUserExcel()
    {
        $successCount = 0;
        $this->rowUserErrors = [];
        $successfulIndices = [];
        $originalRoleType = $this->roleType;

        $total = count($this->parsedUserRows);

        // if ($this->update_or_create_mode) {
        // }

        LazyCollection::make($this->parsedUserRows)
            ->chunk(20)
            ->each(function ($chunk) use (&$successCount, &$successfulIndices, $total) {
                foreach ($chunk as $index => $row) {
                    try {
                        $rawRole = $row['role'] ?? null;
                        $role = ! empty($rawRole) ? ucfirst(trim($rawRole)) : null;
                        if (strtolower($role) === 'none') {
                            $role = null;
                        }
                        if (empty($role)) {
                            $role = ! empty($this->user_input['role']) && strtolower($this->user_input['role']) !== 'none'
                                    ? ucfirst(trim($this->user_input['role']))
                                    : null;
                        }
                        if (empty($role)) {
                            throw ValidationException::withMessages([
                                'role' => [
                                    'Role tidak boleh kosong. Silakan pilih role yang valid atau gunakan input role utama!',
                                ],
                            ]);
                        }
                        $this->roleType = $role;
                        $this->selected_id_user = null;

                        $row['pr_id'] = $this->pr_id;
                        if (empty($row['status'])) {
                            $row['status'] = 'Aktif';
                        }

                        if ($this->update_or_create_mode) {
                            $this->tingkatType = $row['tingkat_target'];
                            $this->selected_id_user = $this->resolveExistingUserId($row, $role);
                            $validatedData = $this->inputModalUser(true, $row, strtolower($role));
                            $this->saveUserFromExcelUpdateOrCreate($validatedData, strtolower($role));
                        } else {
                            $validatedData = $this->inputModalUser(false, $row, strtolower($role));
                            $this->saveUserFromExcel($validatedData, strtolower($role));
                        }

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
        $messageText = "Import Data Pengguna Selesai | Sukses: $successCount | Gagal: $failCount";
        $this->uploadedFileNames = [];

        if ($failCount === 0) {
            $this->toast(text: $messageText);
            $this->reset('excel_user_file');
            $this->resetInputUser();
            $this->showUserExcelModal = false;
            $this->dispatch('refresh-data-user');
        } else {
            $this->toast(text: $messageText, variant: 'warning');

        }
        $this->dispatch('refresh-table');
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
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
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
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
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
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
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
        });
    }

    private function saveUserFromExcelUpdateOrCreate($validated, $role)
    {
        DB::transaction(function () use ($validated, $role) {
            $userMatchAttributes = $this->selected_id_user
                ? ['id' => $this->selected_id_user]
                : ['email' => $validated['email']];

            $userData = [
                'email' => $validated['email'],
            ];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            } else {
                if (! $this->selected_id_user) {
                    $defaultPass = $validated['nip'] ?? $validated['nim'] ?? $validated['nik'] ?? $validated['email'] ?? 'defaultpassword';
                    $userData['password'] = Hash::make($defaultPass);
                }
            }

            $user = User::updateOrCreate(
                $userMatchAttributes,
                $userData
            );

            $mode = $this->user_input['update_or_create'] ?? 'identity1';

            if ($role === 'admin') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nip',
                };

                $adminMatchAttributes = $this->selected_id_user
                    ? ['user_id' => $this->selected_id_user]
                    : [$matchField => $validated[$matchField]];

                Admin::updateOrCreate(
                    $adminMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
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
                    ]
                );
            } elseif ($role === 'dosen') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nip',
                };

                $dosenMatchAttributes = $this->selected_id_user
                    ? ['user_id' => $this->selected_id_user]
                    : [$matchField => $validated[$matchField]];

                Dosen::updateOrCreate(
                    $dosenMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
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
                    ]
                );
            } elseif ($role === 'mahasiswa') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nim',
                };

                $mahasiswaMatchAttributes = $this->selected_id_user
                    ? ['user_id' => $this->selected_id_user]
                    : [$matchField => $validated[$matchField]];

                Mahasiswa::updateOrCreate(
                    $mahasiswaMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
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
                    ]
                );
            }
        });
    }

    public function loadingUserExcel() {}
}
