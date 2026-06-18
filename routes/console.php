<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\Kelas\KelasSesi;
use App\Jobs\SendClassReminderJob;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::call(function () {
    $sekarang = Carbon::now();
    $batasWaktuPengecekan = Carbon::now()->addMinutes(15); 

    $sesiHariIni = KelasSesi::where('tanggal', $sekarang->toDateString())
        ->where('reminder_sent', false)
        ->with(['jadwal_rel', 'override'])
        ->get();

    foreach ($sesiHariIni as $sesi) {
        $waktuSesi = Carbon::parse($sesi->waktu_pelaksanaan);

        if ($waktuSesi->between($sekarang, $batasWaktuPengecekan)) {
            $sesi->update(['reminder_sent' => true]);
            SendClassReminderJob::dispatch($sesi);
        }
    }
})->everyMinute();