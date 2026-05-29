<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');

    Route::middleware(['is_admin'])->group(function () {
        Route::view('user-management', 'user-management')->name('user-management');
        Route::view('user-lite', 'user-lite')->name('user-lite');
        Route::view('program-studi-management', 'program-studi-management')->name('program-studi-management');
    });

    Route::middleware(['is_staff'])->group(function () {
        Route::view('mata-kuliah-management', 'mata-kuliah-management')->name('mata-kuliah-management');
        Route::view('rps-management', 'rps-management')->name('rps-management');
        Route::view('kelas-management', 'kelas-management')->name('kelas-management');
        Route::view('kelas-management/kelas/{kode}', 'kelas-management')->name('jadwal-management');
        Route::view('kelas-management/kelas/{kode}/jadwal/{kode_jadwal}/{jadwal_id}', 'kelas-management')->name('sesi-management');
    });

    Route::redirect('settings', 'settings/profile');
});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
