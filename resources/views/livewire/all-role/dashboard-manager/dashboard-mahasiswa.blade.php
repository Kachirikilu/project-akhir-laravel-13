{{--
    ============================================================
    DASHBOARD MAHASISWA
    Data masih DUMMY — ganti $dummy dengan data asli dari controller
    ============================================================
--}}

@php
    $dummy = [
        'ipk' => 3.78,
        'nilaiBaik' => ['jumlah' => 21, 'total' => 24], // nilai >= B
        'sksTempuh' => ['jumlah' => 96, 'total' => 144], // progres menuju lulus

        'jumlahKelas' => 6,
        'mkSelesai' => ['jumlah' => 24, 'sks' => 96],
    ];

    $rekapSaya = Auth::user()->mahasiswa->rekap_mhs ?? 0;
    $ipkSaya = Auth::user()->mahasiswa->ipk_mhs ?? 0;
    $mutuSaya = Auth::user()->mahasiswa->mutu_mhs ?? 'E';

    $mkSaya = Auth::user()->mahasiswa->nilai_mahasiswas->count() ?? 0;
    $sksSaya = Auth::user()->mahasiswa->nilai_mahasiswas
        ->pluck('rps_rel.mk_rel')
        ->filter()
        ->unique('id')
        ->sum('sks_kuliah');

    $sksSayaB = Auth::user()->mahasiswa->nilai_mahasiswas
        ->filter(fn($item) => $item->nilai > 70)
        ->groupBy('rps_rel.mk_rel.id')
        ->map(fn($group) => $group->sortByDesc('nilai')->first())
        ->sum('rps_rel.mk_rel.sks_kuliah');
    

    $targetSks = Auth::user()->admin->pr_rel->target_sks ?? 144;

    $kelasSaya = $stats['kelas-saya'] ?? 0;
    $sksSaya = $stats['kelas-sks-saya'] ?? 0;
    $jadwalHariIni = $stats['jadwal-saya-hari-ini'] ?? 0;
    $sksHariIni = $stats['jadwal-sks-saya-hari-ini'] ?? 0;
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="academic-cap" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Akademik
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Progres Studi dan Capaian Nilai Anda
        </p>
    </div>

    {{-- Donut charts --}}
    <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 py-2">

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'trophy',
            'title' => 'IPK Saya',
            'subtitle' => "dari $sksSaya SKS",
            'value' => $ipkSaya,
            'max' => 4,
            'displayValue' => number_format($ipkSaya, 2) . ' / 4.00',
            'accent' => 'var(--focus-color)',
            'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'arrow-trending-up',
            'title' => 'Nilai Baik (≥ B)',
            'subtitle' => "dari $mkSaya Mata Kuliah",
            'value' => $sksSayaB,
            'max' => $sksSaya,
            'displayValue' => $sksSayaB . ' / ' . $sksSaya . ' SKS',
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'rectangle-stack',
            'title' => 'Progres SKS Lulus',
            'subtitle' => "dari $targetSks SKS kelulusan",
            'value' => $sksSaya,
            'max' => $targetSks,
            'displayValue' => $sksSaya . ' / ' . $targetSks,
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Studi
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-3"">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-group',
                'label' => 'Jumlah Kelas Saya',
                'value' => $kelasSaya,
                'unit' => 'kelas',
                'sub' => "$sksSaya SKS di Semester berjalan",
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
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

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Mata Kuliah Selesai',
                'value' => $mkSaya,
                'unit' => 'MK',
                'sub' => $sksSaya . ' SKS Total',
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])
        </div>
    </div>
</div>