<?php

namespace App\Jobs;

use App\Http\Services\UserService;
use App\Livewire\Global\HasStats;
use App\Models\Auth\UserExcelQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProcessUserExcelQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasStats;

    public int $timeout = 1800;

    public function __construct(public int $importId) {}

    public function handle(UserService $importService): void
    {
        $import = UserExcelQueue::find($this->importId);

        if (! $import) {
            return;
        }

        $fullPath = Storage::path($import->file_path);

        if (! file_exists($fullPath)) {
            $fullPath = storage_path('app/private/'.$import->file_path);
        }

        if (! file_exists($fullPath)) {
            $import->update([
                'status' => 'failed',
                'row_errors' => ['file' => ["File fisik Excel tidak ditemukan di storage server: {$fullPath}"]],
            ]);

            return;
        }

        $import->update(['status' => 'processing']);

        $rowErrors = [];
        $successCount = 0;
        $failCount = 0;

        try {
            $parsedUserRows = $importService->parseSingleExcelFile($fullPath);
            $totalRows = count($parsedUserRows);

            $import->update(['total_rows' => $totalRows]);

            $parameters = $import->parameters ?? [];
            $prId = $parameters['pr_id'] ?? null;
            $defaultRole = $parameters['role'] ?? null;
            $isUpdateOrCreate = filter_var($parameters['update_or_create_mode'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $updateBy = $parameters['update_or_create'] ?? 'identity1';

            $authUserId = $parameters['auth_user_id'] ?? $import->user_id;
            $authUserTingkat = $parameters['auth_user_tingkat'] ?? 4;

            foreach ($parsedUserRows as $index => $row) {
                try {
                    $rawRole = $row['role'] ?? null;
                    $role = ! empty($rawRole) ? ucfirst(trim($rawRole)) : null;

                    if (strtolower((string) $role) === 'none' || empty($role)) {
                        $role = ! empty($defaultRole) && strtolower((string) $defaultRole) !== 'none'
                                ? ucfirst(trim($defaultRole))
                                : null;
                    }

                    if (empty($role)) {
                        throw ValidationException::withMessages([
                            'role' => ['Role tidak boleh kosong. Silakan atur role di file atau role utama antrean!'],
                        ]);
                    }

                    $row['pr_id'] = $prId;
                    if (empty($row['status'])) {
                        $row['status'] = 'Aktif';
                    }

                    if ($isUpdateOrCreate) {
                        $existingUserId = $importService->resolveExistingUserId($row, strtolower($role), $updateBy);

                        $validatedData = $importService->inputModalUser(
                            isEditingUser: true,
                            data: $row,
                            role: strtolower($role),
                            selectedUserId: $existingUserId,
                            tingkatType: $row['tingkat_target'] ?? null,
                            authUserId: $authUserId,
                            authUserTingkat: $authUserTingkat
                        );

                        $importService->saveUserFromExcelUpdateOrCreate($validatedData, strtolower($role), $existingUserId, $updateBy);
                    } else {
                        $validatedData = $importService->inputModalUser(false, $row, strtolower($role));
                        $importService->saveUserFromExcel($validatedData, strtolower($role));
                    }

                    $successCount++;

                } catch (ValidationException $e) {
                    $rowErrors[$index] = [
                        'baris_excel' => $index + 2,
                        'email' => $row['email'] ?? '-',
                        'name' => $row['name'] ?? ($row['nama'] ?? '-'),
                        'row_data' => $row,
                        'errors' => $e->errors(),
                    ];
                    $failCount++;

                } catch (Throwable $e) {
                    $rowErrors[$index] = [
                        'baris_excel' => $index + 2,
                        'email' => $row['email'] ?? '-',
                        'name' => $row['name'] ?? ($row['nama'] ?? '-'),
                        'row_data' => $row,
                        'errors' => [
                            'general' => [$e->getMessage()],
                            'file' => $e->getFile().':'.$e->getLine(),
                        ],
                    ];
                    $failCount++;
                }
            }

            $import->update([
                'status' => $failCount === 0 ? 'completed' : 'failed',
                'success_rows' => $successCount,
                'failed_rows' => $failCount,
                'row_errors' => $rowErrors,
            ]);

            if ($successCount > 0) {
                $this->clearUserStatsCache();
                $this->clearObeStatsCache();
                $this->clearDosenStatsCache();
                $this->clearMahasiswaStatsCache();
            }

        } catch (Throwable $e) {
            $import->update([
                'status' => 'failed',
                'row_errors' => ['system' => [$e->getMessage()]],
            ]);
            if ($successCount > 0) {
                $this->clearUserStatsCache();
                $this->clearObeStatsCache();
                $this->clearDosenStatsCache();
                $this->clearMahasiswaStatsCache();
            }
        }
        // finally {
        //     if (Storage::exists($import->file_path)) {
        //         Storage::delete($import->file_path);
        //     }
        // }
    }
}
