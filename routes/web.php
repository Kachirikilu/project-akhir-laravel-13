<?php

use App\Http\Middleware\EnsureTeamMembership;
use App\Livewire\Pages\Teams\AcceptInvitation;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

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
        Route::view('kelas-management/{switchTable?}', 'kelas-management')->name('kelas-management');
        Route::view('kelas-management/kelas/{kode}', 'kelas-management')->name('jadwal-management');
        Route::view('kelas-management/kelas/{kode}/jadwal/{kode_jadwal}/{id_jadwal}/{switchTable?}', 'kelas-management')->name('sesi-management');
    });

    Route::redirect('settings', 'settings/profile');
});

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
