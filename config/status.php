<?php

$adm = array_map('trim', explode(',', env('STATUS_ADM', 'Aktif,Tugas Belajar,Mutasi,Cuti Luar Tanggungan,Resign,Pensiun,Diberhentikan,Meninggal Dunia')));
$dsn = array_map('trim', explode(',', env('STATUS_DSN', 'Aktif,Tugas Belajar,Izin Belajar,Cuti Sabatika,Alih Tugas,Resign,Pensiun,Diberhentikan,Meninggal Dunia')));
$mhs = array_map('trim', explode(',', env('STATUS_MHS', 'Aktif,Lulus,Cuti,Pindah,Non-Aktif,Mengundurkan Diri,Drop Out,Hilang,Meninggal Dunia')));

return [
    'admin'     => $adm,
    'dosen'     => $dsn,
    'mahasiswa' => $mhs,
    'all'       => array_values(array_unique(array_merge($adm, $dsn, $mhs))),
];