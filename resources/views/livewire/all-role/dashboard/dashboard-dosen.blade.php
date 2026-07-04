{{--
    ============================================================
    DASHBOARD DOSEN
    Data masih DUMMY — ganti $dummy dengan data asli dari controller
    ============================================================
--}}

@php
    $dummy = [
        'capaianProdi' => ['persen' => 82, 'label' => 'Capaian prodi tempat saya mengajar'],
        'rpsSaya' => ['aktif' => 5, 'total' => 6],
        'timKetua' => ['aktif' => 3, 'total' => 4],
        'totalTim' => ['aktif' => 9, 'total' => 11],

        'mkDiampu' => ['jumlah' => 4, 'sks' => 12],
        'totalSksSemester' => 18,
        'kelasHariIni' => ['jumlah' => 2, 'sks' => 6],
        'kelasSemesterIni' => 7,
        'totalKelas' => 22,
    ];
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="briefcase" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Mengajar
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Aktivitas pengajaran dan tim dosen Anda
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
            'title' => 'RPS Saya',
            'subtitle' => 'dari ' . $dummy['rpsSaya']['total'] . ' RPS terdaftar',
            'value' => $dummy['rpsSaya']['aktif'],
            'max' => $dummy['rpsSaya']['total'],
            'displayValue' => $dummy['rpsSaya']['aktif'] . '/' . $dummy['rpsSaya']['total'],
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'user-group',
            'title' => 'Tim Saya Sebagai Ketua',
            'subtitle' => 'dari ' . $dummy['timKetua']['total'] . ' tim yang diketuai',
            'value' => $dummy['timKetua']['aktif'],
            'max' => $dummy['timKetua']['total'],
            'displayValue' => $dummy['timKetua']['aktif'] . '/' . $dummy['timKetua']['total'],
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'users',
            'title' => 'Total Tim Dosen Saya',
            'subtitle' => 'dari ' . $dummy['totalTim']['total'] . ' tim (anggota & ketua)',
            'value' => $dummy['totalTim']['aktif'],
            'max' => $dummy['totalTim']['total'],
            'displayValue' => $dummy['totalTim']['aktif'] . '/' . $dummy['totalTim']['total'],
            'accent' => '#d97706',
            'accentSoft' => 'rgba(217,119,6,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Mengajar
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Mata Kuliah Diampu',
                'value' => $dummy['mkDiampu']['jumlah'],
                'unit' => 'MK',
                'sub' => $dummy['mkDiampu']['sks'] . ' SKS total',
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'scale',
                'label' => 'Total SKS Semester Ini',
                'value' => $dummy['totalSksSemester'],
                'unit' => 'SKS',
                'sub' => null,
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar-days',
                'label' => 'Kelas Hari Ini',
                'value' => $dummy['kelasHariIni']['jumlah'],
                'unit' => 'kelas',
                'sub' => $dummy['kelasHariIni']['sks'] . ' SKS hari ini',
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
                'label' => 'Total Kelas Saya',
                'value' => $dummy['totalKelas'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => 'var(--main-color)',
                'accentSoft' => 'color-mix(in srgb, var(--main-color) 15%, transparent)',
            ])
        </div>
    </div>
</div>