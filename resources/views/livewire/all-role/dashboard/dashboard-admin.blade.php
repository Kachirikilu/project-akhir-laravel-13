@php
    $data = [
        'capaianProdi' => ['persen' => 78, 'label' => '8 dari 12 indikator tercapai'],
        'rps' => ['aktif' => 96, 'total' => 142],
        'dosen' => ['aktif' => 41, 'total' => 48],
        'mahasiswa' => ['aktif' => 1042, 'total' => 1204],

        'jumlahMk' => 142,
        'targetSks' => Auth::user()->admin->pr_rel->target_sks ?? 144,
        'kelasHariIni' => 9,
        'kelasSemesterIni' => 37,
        'totalKelas' => 214,
    ];

    $prodi = Auth::user()->admin->pr_rel->prodi ?? 'Program Studi';
    $rekapProdi = Auth::user()->admin->pr_rel->rekap_pr ?? 0;
    $cplProdi = $stats['cpl'] ?? 0;

    $rpsProdi = $stats['rps-prodi'] ?? 0;
    $rpsProdiAktif = $stats['rps-prodi-aktif'] ?? 0;

    $dsnProdiAktif = $stats['dosen-prodi-aktif'] ?? 0;
    $dsnProdi = $stats['dosen-prodi'] ?? 0;

    $mhsProdiAktif = $stats['mahasiswa-aktif'] ?? 0;
    $mhsProdi = $stats['mahasiswa-total'] ?? 0;

    $mkProdi = $stats['mk-prodi'] ?? 0;
    $targetSks = Auth::user()->admin->pr_rel->target_sks ?? 144;
    $kelasProdi = $stats['kelas-prodi'] ?? 0;
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="shield-check" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Sistem
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Gambaran Keseluruhan Capaian dan Aktivitas Program Studi
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
            'title' => 'RPS Aktif',
            'subtitle' => 'dari total RPS',
            'value' => $rpsProdiAktif,
            'max' => $rpsProdi,
            'displayValue' => $rpsProdiAktif . ' / ' . $rpsProdi,
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'user-circle',
            'title' => 'Dosen Aktif',
            'subtitle' => 'dari total Dosen',
            'value' => $dsnProdiAktif,
            'max' => $dsnProdi,
            'displayValue' => $dsnProdiAktif . ' / ' . $dsnProdi,
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'users',
            'title' => 'Mahasiswa Aktif',
            'subtitle' => 'dari Mahasiswa',
            'value' => $mhsProdiAktif,
            'max' => $mhsProdi,
            'displayValue' => $mhsProdiAktif . ' / ' . $mhsProdi,
            'accent' => '#d97706',
            'accentSoft' => 'rgba(217,119,6,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Akademik
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Jumlah Mata Kuliah',
                'value' => $mkProdi,
                'unit' => null,
                'sub' => null,
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'scale',
                'label' => 'Target SKS',
                'value' => $targetSks ?? 144,
                'unit' => 'SKS',
                'sub' => null,
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])

            {{-- @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar-days',
                'label' => 'Kelas Hari Ini',
                'value' => $data['kelasHariIni'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => '#d97706',
                'accentSoft' => 'rgba(217,119,6,0.12)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'calendar',
                'label' => 'Kelas Semester Ini',
                'value' => $data['kelasSemesterIni'],
                'unit' => 'kelas',
                'sub' => null,
                'accent' => '#7c3aed',
                'accentSoft' => 'rgba(124,58,237,0.12)',
            ]) --}}

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-group',
                'label' => 'Total Kelas',
                'value' => $kelasProdi,
                'unit' => 'Kelas',
                'sub' => null,
                'accent' => 'var(--main-color)',
                'accentSoft' => 'color-mix(in srgb, var(--main-color) 15%, transparent)',
            ])
        </div>
    </div>
</div>