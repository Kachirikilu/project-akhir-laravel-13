<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Auth\UserExcelQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

#[Signature('app:purge-old-excel-imports-command')]
#[Description('Command description')]
class PurgeOldExcelImportsCommand extends Command
{
protected $signature = 'excel:purge-old {--months=6 : Durasi umur file dalam bulan}';

    protected $description = 'Menghapus file Excel dan rekaman antrean yang usianya lebih dari 6 bulan';

    public function handle()
    {
        $months = (int) $this->option('months');
        $cutoffDate = now()->subMonths($months);

        $oldRecords = UserExcelQueue::where('created_at', '<', $cutoffDate)->get();

        $deletedFilesCount = 0;
        $deletedRecordsCount = 0;

        foreach ($oldRecords as $record) {
            if ($record->file_path && Storage::disk('local')->exists($record->file_path)) {
                Storage::disk('local')->delete($record->file_path);
                $deletedFilesCount++;
            }

            $record->delete();
            $deletedRecordsCount++;
        }

        $message = "Purge Excel Selesai: {$deletedFilesCount} file fisik dan {$deletedRecordsCount} record database (berusia > {$months} bulan) berhasil dihapus.";
        
        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }
}
