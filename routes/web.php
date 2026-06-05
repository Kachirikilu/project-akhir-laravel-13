<?php

use App\Http\Middleware\EnsureTeamMembership;
use App\Livewire\Pages\Teams\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
    });

    Route::middleware(['is_staff'])->group(function () {
        Route::view('mata-kuliah-management/{switchTable?}', 'mata-kuliah-management')->name('mata-kuliah-management');
        Route::view('obe-management/{switchTable?}', 'rps-management')->name('rps-management');
        Route::view('rps-management/{switchTable?}', 'rps-management')->name('rps-management');
    });

    Route::middleware(['is_mahasiswa'])->group(function () {
        Route::view('jadwal-kelas/{switchTable?}', 'jadwal-mahasiswa')->name('jadwal-mahasiswa');
        Route::view('jadwal-kelas/{kode}/jadwal/{kode_jadwal}/{switchTable?}', 'jadwal-mahasiswa')->name('sesi-mahasiswa');
        Route::view('nilai-mahasiswa/{switchTable?}', 'nilai-mahasiswa')->name('nilai-mahasiswa');
    });

    Route::view('kelas-management/{switchTable?}', 'kelas-management')->name('kelas-management');
    Route::view('kelas-management/kelas/{kode}/{switchTable?}', 'kelas-management')->name('jadwal-management');
    Route::view('kelas-management/kelas/{kode}/jadwal/{kode_jadwal}/{switchTable?}', 'kelas-management')->name('sesi-management');

    // Route::middleware('kelas.access')->group(function () {
    // });
    Route::redirect('settings', 'settings/profile');
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

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
