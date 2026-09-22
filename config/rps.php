<?php

$min = env('RPS_BOBOT_MIN');
$max = env('RPS_BOBOT_MAX');

$min = is_numeric($min) ? (float) $min : null;
$max = is_numeric($max) ? (float) $max : null;

if ($min !== null && $max === null) {
    $max = $min;
} elseif ($max !== null && $min === null) {
    $min = $max;
}

if (($min !== null && $min < 20) || ($max !== null && $max < 20)) {
    throw new \InvalidArgumentException(
        "Konfigurasi batas bobot RPS tidak valid: RPS_BOBOT_MIN dan RPS_BOBOT_MAX tidak boleh di bawah 20%."
    );
}

if ($min !== null && $max !== null && $max < $min) {
    throw new \InvalidArgumentException(
        "Konfigurasi batas bobot RPS tidak valid: RPS_BOBOT_MAX ({$max}%) tidak boleh lebih kecil dari RPS_BOBOT_MIN ({$min}%)."
    );
}

// --- MASTER LIST METODE YANG DIIZINKAN (SISTEM) ---
$masterAllowedMetode = [
    'Teori',
    'Aktivitas Partisipasif',
    'Tugas',
    'Mandiri',
    'UTS',
    'UAS',
    'Evaluasi Awal',
    'Evaluasi Akhir',
    'Laporan Akhir',
    'Hasil Proyek',
    'Kuis',
    'Skripsi',
    'Kerja Praktek',
    'Responsi',
    'Logbook',
    'Portofolio',
];

// --- PARSING METODE SCPMK DARI .ENV ---
$rawMetode = env('METODE_SCPMK');

if (! empty($rawMetode)) {
    $metodeList = array_values(array_filter(array_map('trim', explode(',', $rawMetode))));
} else {
    $metodeList = $masterAllowedMetode;
}

// 1. VALIDASI: Cek apakah ada metode yang TIDAK TERDAFTAR di Master List (seperti WDWD)
$invalidMetode = array_diff($metodeList, $masterAllowedMetode);

if (! empty($invalidMetode)) {
    $invalidItems = implode(', ', $invalidMetode);
    throw new \InvalidArgumentException(
        "Konfigurasi METODE_SCPMK tidak valid! Nilai berikut tidak terdaftar dalam sistem: [{$invalidItems}]."
    );
}

// Master Kategori UTS & UAS untuk Pengecekan
$masterUTS = ['UTS', 'Evaluasi Awal'];
$masterUAS = ['UAS', 'Evaluasi Akhir', 'Laporan Akhir', 'Hasil Proyek'];

$metodeUTS = array_values(array_intersect($metodeList, $masterUTS));
$metodeUAS = array_values(array_intersect($metodeList, $masterUAS));

// 2. VALIDASI: Memastikan elemen minimal UTS & UAS tetap terpenuhi
if (empty($metodeUTS)) {
    throw new \InvalidArgumentException(
        "Konfigurasi METODE_SCPMK tidak valid: Wajib mengandung minimal satu metode kategorisasi UTS (pilihan: " . implode(', ', $masterUTS) . ")."
    );
}

if (empty($metodeUAS)) {
    throw new \InvalidArgumentException(
        "Konfigurasi METODE_SCPMK tidak valid: Wajib mengandung minimal satu metode kategorisasi UAS (pilihan: " . implode(', ', $masterUAS) . ")."
    );
}

return [
    'bobot_min' => $min ?? 70.0,
    'bobot_max' => $max ?? 200.0,

    'metode' => $metodeList,
    'metode_uts' => $metodeUTS,
    'metode_uas' => $metodeUAS,

    'waktu_telat' => env('WAKTU_TELAT') ?? 15,
    'faktor_telat' => env('FAKTOR_TELAT') ?? 30,
    'waktu_dispensasi' => env('WAKTU_DISPENSI') ?? 150,
    'faktor_dispensasi' => env('FAKTOR_DISPENSI') ?? 120,
];