@php
    $isStaff = Auth::user()->admin || Auth::user()->dosen;
    $isMahasiswa = Auth::user()->mahasiswa;
    
    $maxStaff = ((int) $stats['sesi']) * ((int) ($stats['mahasiswa'] ?? 0));
    $maxMhs = $stats['sesi'] ?? 16;
    $denominator = $isStaff ? $maxStaff : $maxMhs;

    $dataSource = $isStaff ? $absensi : ($absensi['mahasiswa'] ?? []);

    $statsCards = [
        [
            'title' => $isStaff ? 'Total Poin Absensi' : 'Poin Absensi Saya',
            'value' => ($dataSource['mhs_poin_absensi_percent'] ?? 0) . '%',
            'sub' => $isStaff ? 'Poin Seluruh Sesi' : 'Total Poin Kehadiran',
            'color' => 'text-[var(--focus-color)] font-semibold',
        ],
        [
            'title' => 'Masuk',
            'value' => $dataSource['mhs_masuk'] ?? 0,
            'sub' => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Kehadiran Saya',
            'color' => 'text-emerald-500 font-semibold',
            'use_max' => true,
        ],
        [
            'title' => 'Izin / Sakit',
            'value' => ((int) ($dataSource['mhs_izin'] ?? 0)) + ((int) ($dataSource['mhs_sakit'] ?? 0)),
            'sub' => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Izin & Sakit Saya',
            'color' => 'text-amber-500 font-semibold',
            'use_max' => true,
        ],
        [
            'title' => 'Tidak Hadir',
            'value' => $dataSource['mhs_tidak_masuk'] ?? 0,
            'sub' => $isStaff ? 'Akumulasi Seluruh Sesi' : 'Ketidakhadiran Saya',
            'color' => 'text-red-500 font-semibold',
            'use_max' => true,
        ],
    ];
@endphp

@if ($isStaff || $isMahasiswa)
    @foreach ($statsCards as $card)
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
                {{ $card['title'] }}
            </span>

            <span class="text-lg text-[var(--focus-color)]">
                <span class="{{ $card['color'] }}">{{ $card['value'] }}</span>
                @if (isset($card['use_max']))
                    / {{ $denominator }}
                @endif
            </span>

            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
                {{ $card['sub'] }}
            </span>
        </div>
    @endforeach
@endif