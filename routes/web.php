<?php

use App\Http\Middleware\EnsureTeamMembership;
use App\Livewire\Pages\Teams\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Kelas\KelasSesi;
use App\Jobs\SendClassReminderJob;
use App\Livewire\Staff\ObeManagement\RpsManagement\iFrameRpsController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::middleware(['web', 'check.registration'])->group(function () {
    Route::get('/register', function () {
        return view('pages.auth.register');
    })->name('register');
});

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::livewire('invitations/{invitation}/accept', AcceptInvitation::class)
        ->name('invitations.accept');

    Route::view('dashboard', 'dashboard')->name('dashboard');


    Route::middleware(['is_admin'])->group(function () {
        Route::view('user-management/{switchTable?}', 'user-management')->name('user-management');
        Route::view('user-lite', 'user-lite')->name('user-lite');
        Route::view('program-studi-management/{switchTable?}', 'program-studi-management')->name('program-studi-management');
        Route::view('program-studi-management/kode/{kode_pr}/{switchTable?}', 'program-studi-management')->name('capaian-management');
        Route::view('program-studi-management/kode/{kode_pr}/cpl/{kode_cpl}', 'program-studi-management')->name('rps-capaian-management');
    });

    Route::middleware(['is_staff'])->group(function () {
        Route::view('mata-kuliah-management/{switchTable?}', 'mata-kuliah-management')->name('mata-kuliah-management');
        Route::view('obe-management/{switchTable?}', 'obe-management')->name('obe-management');
        Route::view('rps-management/{switchTable?}', 'obe-management')->name('obe-management');
        // Route::get('/download-nilai/{jadwal}', DownloadNilaiController::class)->name('download.nilai');

        Route::view('nilai-management/{switchTable?}', 'nilai-management')->name('nilai-management');
        Route::view('nilai-management/nim/{nim}', 'nilai-management')->name('nilai-mahasiswa-management');
        Route::view('nilai-management/nim/{nim}/rps/{ganjil_genap}/{akademik}', 'nilai-management')->name('rps-mahasiswa-management');
        Route::view('nilai-management/rps/{kode_rps}', 'nilai-management')->name('capaian-mahasiswa-management');
    });
    Route::middleware(['is_dosen'])->group(function () {
        Route::view('program-studi/{switchTable?}', 'program-studi-dosen')->name('program-studi-dosen');
    });

    Route::middleware(['is_mahasiswa'])->group(function () {
        Route::view('jadwal-kelas/{switchTable?}', 'jadwal-mahasiswa')->name('jadwal-mahasiswa');
        Route::view('jadwal-kelas/{kode_kelas}/jadwal/{kode_jadwal_short}/{switchTable?}', 'jadwal-mahasiswa')->name('sesi-mahasiswa');
        // Route::view('nilai-mahasiswa', 'nilai-mahasiswa')->name('nilai-mahasiswa');
        // Route::view('nilai-mahasiswa/rps/{ganjil_genap}/{akademik}', 'nilai-mahasiswa')->name('nilai-rps-mahasiswa');

        Route::view('nilai-mahasiswa', 'nilai-mahasiswa')->name('nilai-mahasiswa');
        Route::view('nilai-mahasiswa/rps/{ganjil_genap}/{akademik}', 'nilai-mahasiswa')->name('rps-mahasiswa');
    });

    Route::view('kelas-management/{switchTable2?}/{switchTable?}', 'kelas-management')->name('kelas-management');
    Route::view('kelas-management/kelas/{kode_kelas}/jadwal/{switchTable?}', 'kelas-management')->name('jadwal-management');
    Route::view('kelas-management/kelas/{kode_kelas}/jadwal/{kode_jadwal_short}/sesi/{switchTable?}', 'kelas-management')->name('sesi-management');

    Route::get('/rps/pdf-preview/{rps_id}/{pr_id?}', [iFrameRpsController::class, 'preview'])->name('rps.pdf.preview');
    
    // Route::middleware('kelas.access')->group(function () {
    // });
    Route::redirect('settings', 'settings/profile');
});

Route::get('/user-wallpaper/{id}', function ($id) {
    $wallpaper = Wallpaper::findOrFail($id);
    if ($wallpaper->user_id !== auth()->id()) abort(403);
    return Storage::disk('private')->response($wallpaper->path);
})->middleware('auth');


Route::get('/test-wa/{sesi_id}', function ($sesi_id) {
    $sesi = KelasSesi::findOrFail($sesi_id);
    SendClassReminderJob::dispatchSync($sesi); 
    
    return "Test perkuliahan sesi " . $sesi->id . " berhasil ditembak ke Fonnte!";
});

Route::get('/database-stats', function () {
    $tables = Schema::getTables();
    $totalRows = 0;

    foreach ($tables as $table) {
        $tableName = is_array($table) ? ($table['name'] ?? null) : (is_object($table) ? ($table->name ?? null) : $table);
        if (! $tableName) {
            continue;
        }
        if (in_array($tableName, ['migrations', 'failed_jobs', 'personal_access_tokens'])) {
            continue;
        }
        if (Schema::hasTable($tableName)) {
            $totalRows += DB::table($tableName)->count();
        }
    }

    return view('database-stats', compact('totalRows'));
})->name('database-stats');

// Route::prefix('{current_team}')
//     ->middleware(['auth', 'verified', EnsureTeamMembership::class])
//     ->group(function () {});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
