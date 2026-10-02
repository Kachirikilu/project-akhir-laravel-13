<?php


$nAdm = env('SEEDER_PERSEN_ADMIN') ?? 10;
$nDsn = env('SEEDER_PERSEN_DOSEN') ?? 30;
$nMhs = env('SEEDER_PERSEN_MAHASISWA') ?? 60;

$nTtl = $nAdm + $nDsn + $nMhs;

$pAdm = $nAdm / $nTtl;
$pDsn = $nDsn / $nTtl;
$pMhs = $nMhs / $nTtl;

$ang_min = env('SEEDER_MAHASISWA_ANGKATAN_MIN') ?? 2012;
$ang_max = env('SEEDER_MAHASISWA_ANGKATAN_MAX') ?? 2026;

if ($ang_min > $ang_max) {
    $ang_min = $ang_max;
}

return [
    'count_user' => env('SEEDER_USER') ?? 1024,
    'batch_user' => env('SEEDER_BATCH_USER') ?? 512,
    'persen_admin' => $pAdm,
    'persen_dosen' => $pDsn,
    'persen_mahasiswa' => $pMhs,
    'angkatan_mahasiswa_min' => $ang_min ?? 2012,
    'angkatan_mahasiswa_max' => $ang_max ?? 2026,

    'count_rps' => env('SEEDER_RPS') ?? 32,
    'batch_rps' => env('SEEDER_BATCH_RPS') ?? 16,

    'count_kelas_per_rps' => env('SEEDER_KELAS_PER_RPS') ?? 3,
    'batch_kelas' => env('SEEDER_BATCH_KELAS') ?? 128,

    'count_nilai_per_rps' => env('SEEDER_NILAI_PER_RPS') ?? 256,
    'count_nilai_per_prodi_rps' => env('SEEDER_NILAI_PER_PRODI_RPS') ?? 32,
    'batch_nilai' => env('SEEDER_BATCH_NILAI') ?? 128,
];
