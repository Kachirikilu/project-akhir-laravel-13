{{--
    ============================================================
    DASHBOARD ADMIN
    Data masih DUMMY — ganti $dummy dengan data asli dari controller
    ============================================================
--}}

@php
    $dummy = [
        'capaianProdi' => ['persen' => 78, 'label' => '8 dari 12 indikator tercapai'],
        'rps' => ['aktif' => 96, 'total' => 142],
        'dosen' => ['aktif' => 41, 'total' => 48],
        'mahasiswa' => ['aktif' => 1042, 'total' => 1204],

        'jumlahMk' => 142,
        'targetSks' => 144,
        'kelasHariIni' => 9,
        'kelasSemesterIni' => 37,
        'totalKelas' => 214,
    ];
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="shield-check" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Sistem
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Gambaran keseluruhan capaian dan aktivitas program studi
        </p>
    </div>

    {{-- Donut charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 p-2">

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'academic-cap',
            'title' => 'Capaian Prodi',
            'subtitle' => $dummy['capaianProdi']['label'],
            'value' => $dummy['capaianProdi']['persen'],
            'max' => 100,
            'displayValue' => $dummy['capaianProdi']['persen'] . '%',
            'accent' => 'var(--focus-color)',
            'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'book-open',
            'title' => 'RPS Aktif',
            'subtitle' => 'dari ' . $dummy['rps']['total'] . ' total RPS',
            'value' => $dummy['rps']['aktif'],
            'max' => $dummy['rps']['total'],
            'displayValue' => $dummy['rps']['aktif'] . '/' . $dummy['rps']['total'],
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'user-circle',
            'title' => 'Dosen Aktif',
            'subtitle' => 'dari ' . $dummy['dosen']['total'] . ' total dosen',
            'value' => $dummy['dosen']['aktif'],
            'max' => $dummy['dosen']['total'],
            'displayValue' => $dummy['dosen']['aktif'] . '/' . $dummy['dosen']['total'],
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'users',
            'title' => 'Mahasiswa Aktif',
            'subtitle' => 'dari ' . $dummy['mahasiswa']['total'] . ' (selain lulus)',
            'value' => $dummy['mahasiswa']['aktif'],
            'max' => $dummy['mahasiswa']['total'],
            'displayValue' => $dummy['mahasiswa']['aktif'] . '/' . $dummy['mahasiswa']['total'],
            'accent' => '#d97706',
            'accentSoft' => 'rgba(217,119,6,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Akademik
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Jumlah Mata Kuliah',
                'value' => $dummy['jumlahMk'],
                'unit' => null,
                'sub' => null,
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'scale',
                'label' => 'Target SKS',
                'value' => $dummy['targetSks'] ?? 144,
                'unit' => 'SKS',
                'sub' => null,
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar-days',
                'label' => 'Kelas Hari Ini',
                'value' => $dummy['kelasHariIni'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => '#d97706',
                'accentSoft' => 'rgba(217,119,6,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar',
                'label' => 'Kelas Semester Ini',
                'value' => $dummy['kelasSemesterIni'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => '#7c3aed',
                'accentSoft' => 'rgba(124,58,237,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-group',
                'label' => 'Total Kelas',
                'value' => $dummy['totalKelas'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => 'var(--main-color)',
                'accentSoft' => 'color-mix(in srgb, var(--main-color) 15%, transparent)',
            ])
        </div>
    </div>
</div>