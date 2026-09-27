{{--
    ============================================================
    ABSENSI STATS — Donut versi dalam-grid
    Dipakai di dalam grid bersama kolom info lain (kode, mk, prodi, dll).
    Setiap @foreach menghasilkan satu <div> kolom, bukan wrapper grid sendiri.
    ============================================================
--}}

@php
    $isStaff    = Auth::user()->admin || Auth::user()->dosen;
    $isMahasiswa = Auth::user()->mahasiswa;

    $maxStaff   = ((int) $stats['sesi']) * ((int) ($stats['mahasiswa'] ?? 0));
    $maxMhs     = $stats['sesi'] ?? 16;
    $denominator = $isStaff ? $maxStaff : $maxMhs;

    $dataSource = $isStaff ? $absensi : ($absensi['mahasiswa'] ?? []);

    $poinPct   = (float) ($dataSource['mhs_poin_absensi_percent'] ?? 0);
    $masuk     = (int)   ($dataSource['mhs_masuk']      ?? 0);
    $izinSakit = (int)   ($dataSource['mhs_izin']       ?? 0)
               + (int)   ($dataSource['mhs_sakit']      ?? 0);
    $tidakHdr  = (int)   ($dataSource['mhs_tidak_masuk'] ?? 0);
    $denom     = $denominator ?? 0;

    $r = 28; // radius lebih kecil agar proporsional dalam kolom grid
    $c = 2 * M_PI * $r;

    $statsCards = [
        [
            'title'     => $isStaff ? 'Total Poin Absensi' : 'Poin Absensi Saya',
            'sub'       => $isStaff ? 'Poin seluruh sesi' : 'Total Poin Kehadiran',
            'value'     => $poinPct,
            'max'       => 100,
            'display'   => $poinPct . '%',
            'accent'    => 'var(--focus-color)',
            'softBg'    => 'color-mix(in srgb, var(--focus-color) 14%, transparent)',
            'textColor' => 'var(--focus-color)',
            'icon'      => 'chart-bar',
        ],
        [
            'title'     => 'Masuk',
            'sub'       => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Kehadiran Saya',
            'value'     => $masuk,
            'max'       => $denom,
            'display'   => $masuk . '/' . $denom,
            'accent'    => '#10b981',
            'softBg'    => 'rgba(16,185,129,0.12)',
            'textColor' => '#10b981',
            'icon'      => 'check-circle',
        ],
        [
            'title'     => 'Izin / Sakit',
            'sub'       => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Izin & Sakit Saya',
            'value'     => $izinSakit,
            'max'       => $denom,
            'display'   => $izinSakit . '/' . $denom,
            'accent'    => '#f59e0b',
            'softBg'    => 'rgba(245,158,11,0.12)',
            'textColor' => '#f59e0b',
            'icon'      => 'document-text',
        ],
        [
            'title'     => 'Tidak Hadir',
            'sub'       => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Ketidakhadiran Saya',
            'value'     => $tidakHdr,
            'max'       => $denom,
            'display'   => $tidakHdr . '/' . $denom,
            'accent'    => '#ef4444',
            'softBg'    => 'rgba(239,68,68,0.12)',
            'textColor' => '#ef4444',
            'icon'      => 'x-circle',
        ],
    ];
@endphp

@if ($isStaff || $isMahasiswa)
    @foreach ($statsCards as $card)
        @php
            $pct    = $card['max'] > 0 ? min(100, round(($card['value'] / $card['max']) * 100)) : 0;
            $offset = $c - ($pct / 100) * $c;
        @endphp

    @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => $card['icon'],
        'title'     => $card['title'],
        'sub'       => $card['sub'],
        'value'     => $card['value'],
        'max'       => $card['max'],
        'display'   => $card['display'],
        'accent'    => $card['accent'],
        'softBg'    => $card['softBg'],
        'textColor' => $card['textColor'],
        'size'      => 64,
        'pctSize'   => 'text-sm',
    ])
    @endforeach
@endif