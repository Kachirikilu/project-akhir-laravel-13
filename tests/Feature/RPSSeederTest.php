<?php

use App\Models\Akademik\CPL;
use App\Models\ProgramStudi\Prodi;
use Database\Seeders\RPSSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('attaches generated CPL records to prodi so seeding has valid mapping', function () {
    $prodi = Prodi::factory()->create();
    $cpl = CPL::create([
        'kode_cpl' => 'CPL01',
        'deskripsi' => 'CPL test',
    ]);

    $seeder = new RPSSeeder();
    $seeder->attachCplsToProdi([$cpl]);

    expect($prodi->fresh()->cpls()->count())->toBe(1)
        ->and($cpl->fresh()->prodis()->count())->toBe(1);
});
