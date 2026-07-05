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

    $prodi = Auth::user()->dosen->pr_rel->prodi ?? 'Program Studi';
    $rekapProdi = Auth::user()->dosen->pr_rel->rekap_pr ?? 0;
    $cplProdi = $stats['cpl'] ?? 0;

    $rpsSayaAktif = $stats['rps-saya-aktif'] ?? 0;
    $rpsSaya = $stats['rps-saya'] ?? 0;

    $timDosenKetuaAktif = $stats['tim-dosen-saya-ketua-aktif'] ?? 0;
    $timDosenKetua = $stats['tim-dosen-saya-ketua'] ?? 0;
    $timDosenAktif = $stats['tim-dosen-saya-aktif'] ?? 0;
    $timDosen = $stats['tim-dosen-saya'] ?? 0;

    $mkSaya = $stats['mk-saya'] ?? 0;
    $mkSksSaya = $stats['mk-sks-saya'] ?? 0;
    $mkSksSemesterSaya = $stats['mk-sks-semester-saya'] ?? 0;

    $kelasSaya = $stats['kelas-saya'] ?? 0;
    $jadwalHariIni = $stats['jadwal-saya-hari-ini'] ?? 0;
    $sksHariIni = $stats['jadwal-sks-saya-hari-ini'] ?? 0;
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="briefcase" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Mengajar
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Aktivitas Pengajaran dan Tim Dosen Anda
        </p>
    </div>

    {{-- Donut charts --}}
    <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 py-2">

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'academic-cap',
            'title' => "Capaian $prodi",
            'subtitle' => "dari $cplProdi CPL terdaftar",
            'value' => $rekapProdi,
            'max' => 100,
            'displayValue' => $rekapProdi . '%',
            'accent' => 'var(--focus-color)',
            'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'book-open',
            'title' => 'RPS Saya',
            'subtitle' => 'dari RPS terdaftar',
            'value' => $rpsSayaAktif,
            'max' => $rpsSaya,
            'displayValue' => $rpsSayaAktif . ' / ' . $rpsSaya,
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'user-group',
            'title' => 'Tim Saya Sebagai Ketua',
            'subtitle' => 'dari Tim yang Diketuai',
            'value' => $timDosenKetuaAktif,
            'max' => $timDosenKetua,
            'displayValue' => $timDosenKetuaAktif . ' / ' . $timDosenKetua,
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'users',
            'title' => 'Total Tim Dosen Saya',
            'subtitle' => 'dari total Tim',
            'value' => $timDosenKetua,
            'max' => $timDosen,
            'displayValue' => $timDosenKetua . ' / ' . $timDosen,
            'accent' => '#d97706',
            'accentSoft' => 'rgba(217,119,6,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Mengajar
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Mata Kuliah Diampu',
                'value' => $mkSaya,
                'unit' => 'MK',
                'sub' => $mkSksSaya . ' SKS total',
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'scale',
                'label' => 'Total SKS Semester Ini',
                'value' => $mkSksSemesterSaya,
                'unit' => 'SKS',
                'sub' => null,
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar-days',
                'label' => 'Kelas Hari Ini',
                'value' => $jadwalHariIni,
                'unit' => 'Jadwal Kelas',
                'sub' => $sksHariIni . ' SKS hari ini',
                'accent' => '#d97706',
                'accentSoft' => 'rgba(217,119,6,0.12)',
            ])

            {{-- @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar',
                'label' => 'Kelas Semester Ini',
                'value' => $dummy['kelasSemesterIni'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => '#7c3aed',
                'accentSoft' => 'rgba(124,58,237,0.12)',
            ]) --}}

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-group',
                'label' => 'Total Kelas Saya',
                'value' => $kelasSaya,
                'unit' => 'Kelas',
                'sub' => null,
                'accent' => 'var(--main-color)',
                'accentSoft' => 'color-mix(in srgb, var(--main-color) 15%, transparent)',
            ])
        </div>
    </div>
</div>