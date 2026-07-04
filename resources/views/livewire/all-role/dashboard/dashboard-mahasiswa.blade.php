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
@endphp

<div class="flex flex-col gap-5">

    <div>
        <h1 class="text-md sm:text-lg font-bold tracking-tight text-[var(--contrast-main-text)] flex items-center gap-2">
            <flux:icon name="academic-cap" class="w-5 h-5 text-[var(--focus-color)]" />
            Ringkasan Akademik
        </h1>
        <p class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
            Progres studi dan capaian nilai Anda
        </p>
    </div>

    {{-- Donut charts --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-2">

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'trophy',
            'title' => 'IPK Saya',
            'subtitle' => 'Skala 4.00',
            'value' => $dummy['ipk'],
            'max' => 4,
            'displayValue' => number_format($dummy['ipk'], 2),
            'accent' => 'var(--focus-color)',
            'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'arrow-trending-up',
            'title' => 'Nilai Baik (≥ B)',
            'subtitle' => 'dari ' . $dummy['nilaiBaik']['total'] . ' mata kuliah',
            'value' => $dummy['nilaiBaik']['jumlah'],
            'max' => $dummy['nilaiBaik']['total'],
            'displayValue' => $dummy['nilaiBaik']['jumlah'] . '/' . $dummy['nilaiBaik']['total'],
            'accent' => '#0d9488',
            'accentSoft' => 'rgba(13,148,136,0.12)',
        ])

        @include('livewire.global.statistik.donut-box-stats', [
            'icon' => 'rectangle-stack',
            'title' => 'Progres SKS Lulus',
            'subtitle' => 'dari ' . $dummy['sksTempuh']['total'] . ' SKS kelulusan',
            'value' => $dummy['sksTempuh']['jumlah'],
            'max' => $dummy['sksTempuh']['total'],
            'displayValue' => $dummy['sksTempuh']['jumlah'] . '/' . $dummy['sksTempuh']['total'],
            'accent' => '#7c3aed',
            'accentSoft' => 'rgba(124,58,237,0.12)',
        ])
    </div>

    {{-- Info boxes --}}
    <div>
        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide mb-2 text-[var(--contrast-second-text)]">
            Statistik Studi
        </h2>

        <div class="grid grid-cols-2 gap-3">

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-group',
                'label' => 'Jumlah Kelas Saya',
                'value' => $dummy['jumlahKelas'],
                'unit' => 'kelas',
                'sub' => 'Semester berjalan',
                'accent' => 'var(--focus-color)',
                'accentSoft' => 'color-mix(in srgb, var(--focus-color) 15%, transparent)',
            ])

            @include('livewire.global.statistik.info-box-stats', [
                'icon' => 'rectangle-stack',
                'label' => 'Mata Kuliah Selesai',
                'value' => $dummy['mkSelesai']['jumlah'],
                'unit' => 'MK',
                'sub' => $dummy['mkSelesai']['sks'] . ' SKS total',
                'accent' => '#0d9488',
                'accentSoft' => 'rgba(13,148,136,0.12)',
            ])
        </div>
    </div>
</div>