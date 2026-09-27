<?php

$getPrioritizedConfig = function ($waktuEnv, $faktorEnv, $defaultWaktu, $defaultFaktor) {
    $waktuVal = env($waktuEnv);
    $faktorVal = env($faktorEnv);

    $waktu = ($waktuVal !== null && $waktuVal !== '') ? (int) $waktuVal : null;
    $faktor = ($faktorVal !== null && $faktorVal !== '') ? (int) $faktorVal : null;

    if (! empty($waktu) && ! empty($faktor)) {
        return ['waktu' => $waktu, 'faktor' => 0];
    }
    if (! empty($waktu)) {
        return ['waktu' => $waktu, 'faktor' => 0];
    }
    if (! empty($faktor)) {
        return ['waktu' => 0, 'faktor' => $faktor];
    }

    return ['waktu' => $defaultWaktu, 'faktor' => 0];
};

$telat = $getPrioritizedConfig('WAKTU_TELAT', 'FAKTOR_TELAT', 15, 30);
$dispensi = $getPrioritizedConfig('WAKTU_DISPENSI', 'FAKTOR_DISPENSI', 150, 120);

return [
    'waktu_telat' => $telat['waktu'],
    'faktor_telat' => $telat['faktor'],
    'waktu_dispensi' => $dispensi['waktu'],
    'faktor_dispensi' => $dispensi['faktor'],
];
