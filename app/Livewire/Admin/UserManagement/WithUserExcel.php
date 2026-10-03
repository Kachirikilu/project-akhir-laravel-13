<?php

namespace App\Livewire\Admin\UserManagement;

use App\Exports\UserExport;
use App\Http\Services\UserService;
use App\Jobs\ProcessUserExcelQueueJob;
use App\Livewire\Global\HasToast;
// use App\Models\Auth\Membership;
// use App\Models\Auth\Team;
use App\Models\Auth\UserExcelQueue;
use App\Models\ProgramStudi\Departemen;
use App\Models\ProgramStudi\Fakultas;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
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

    // Pakau Service
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

        // Panggil UserService menggunakan Service Container Laravel (app()):
        $parsingService = app(UserService::class);

        $this->parsedUserRows = [];

        // Gunakan properti yang BENAR: $this->excel_user_file
        foreach ($this->excel_user_file as $singleFile) {
            $rows = $parsingService->parseSingleExcelFile($singleFile->getRealPath());
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
            'user_input.update_or_create' => 'nullable|required_if:update_or_create_mode,1,true|in:identity1,nik,email',
            'update_or_create_mode' => 'boolean',
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

    public function saveUserAntrean()
    {
        $currentUser = Auth::user();

        $this->validate(
            $this->rulesUserExcelImport(),
            $this->validationMessagesUser()
        );

        try {
            $files = $this->excel_user_file;
            $prId = $this->pr_id;

            $role = ! empty($this->user_input['role']) ? $this->user_input['role'] : null;
            $isUpdateOrCreate = ! empty($this->update_or_create_mode);
            $updateBy = $isUpdateOrCreate ? ($this->user_input['update_or_create'] ?? 'identity1') : null;
            $dispatchedCount = 0;

            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();

                $storedPath = $file->storeAs(
                    'imports/users/temp',
                    time().'_'.uniqid().'_'.\Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension()
                );

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

                        'auth_user_id' => $currentUser->id,
                        'auth_user_tingkat' => $currentUser->admin?->tingkat ?? 4,
                    ],
                    'status' => 'pending',
                ]);

                ProcessUserExcelQueueJob::dispatch($importRecord->id);
                $dispatchedCount++;
            }

            $this->resetInputUser();
            $this->clearUserExcelFile();
            $this->update_or_create_mode = false;
            $this->dispatch('refresh-data-user-antrean');

            $this->toast(
                text: "Berhasil memasukkan {$dispatchedCount} file ke dalam antrean sistem!",
                variant: 'success'
            );

            if (method_exists($this, 'dispatch')) {
                $this->dispatch('next-antrean-step');
                $this->dispatch('refresh-table');
                $this->dispatch('refresh-stats-user');
            }

        } catch (\Throwable $e) {
            $this->toast(text: '❌ Gagal memproses antrean: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function resolveExistingUserId(array $row, string $role): ?int
    {
        $mode = $this->user_input['update_or_create'] ?? 'identity1';

        return app(UserService::class)->resolveExistingUserId($row, $role, $mode);
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
        app(UserService::class)->saveUserFromExcel($validated, $role);
    }

    private function saveUserFromExcelUpdateOrCreate($validated, $role)
    {
        $mode = $this->user_input['update_or_create'] ?? 'identity1';
        app(UserService::class)->saveUserFromExcelUpdateOrCreate($validated, $role, $this->selected_id_user, $mode);
    }

    public function loadingUserExcel() {}

    public function downloadFile($importId)
    {
        $import = UserExcelQueue::findOrFail($importId);

        if (Storage::exists($import->file_path)) {
            return Storage::download($import->file_path, $import->original_name);
        }

        $this->toast(text: 'File fisik sudah tidak tersedia di server!', variant: 'danger');
    }

    // public $showErrorModal = false;

    // public array $selectedRowErrors = [];

    // public function showErrorDetail(int $importId)
    // {
    //     $importRecord = UserExcelQueue::find($importId);

    //     if (! $importRecord) {
    //         $this->toast(text: 'Data antrean tidak ditemukan!', variant: 'danger');

    //         return;
    //     }
    //     $errors = $importRecord->row_errors;

    //     if (is_string($errors)) {
    //         $errors = json_decode($errors, true) ?? [];
    //     }

    //     $this->selectedRowErrors = is_array($errors) ? $errors : [];

    //     // Buka Modal Flux UI
    //     $this->dispatch('modal-show', name: 'error-detail-modal');
    // }
}
