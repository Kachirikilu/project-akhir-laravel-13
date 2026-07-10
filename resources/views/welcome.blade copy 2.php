<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ config('app.name') }} — Sistem Manajemen Pembelajaran Outcome-Based Education</title>
</head>

<body class="min-h-screen antialiased bg-[var(--wadah-color)] dark:bg-neutral-950">

    {{-- ═══ NAVBAR ═══ --}}
    <nav class="flex items-center justify-between px-5 sm:px-8 py-3.5 border-b border-[var(--border-table-color)] bg-[var(--second-table-color)]">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-[10px] flex-shrink-0"
                style="background: var(--main-color);">
                <x-app-logo-icon class="h-4 w-auto" />
            </div>
            <span class="text-xs font-bold uppercase tracking-[0.08em] text-[var(--contrast-second-text)]">
                {{ config('app.name', 'RPS Manajemen') }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <div class="flex gap-3 mr-2">
                <x-livewire::navigation.dark-mode :noPadding="1" />
                <x-livewire::navigation.color-mode :noBar="1" />
            </div>
            @auth
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-[9px] text-xs font-bold text-[var(--main-text)] transition-all active:scale-[0.98]"
                    style="background: var(--main-color);">
                    <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate
                    class="px-3.5 py-1.5 rounded-[9px] border border-[var(--border-table-color)] text-xs font-semibold text-[var(--contrast-second-text)] hover:bg-[var(--sub-table-color)] transition-all">
                    Masuk
                </a>
                <a href="{{ route('login') }}" wire:navigate
                    class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-[9px] text-xs font-bold text-[var(--main-text)] transition-all active:scale-[0.98]"
                    style="background: var(--main-color);">
                    <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                    Buka Dashboard
                </a>
            @endauth
        </div>
    </nav>

    {{-- ═══ HERO ═══ --}}
    <section class="flex flex-col items-center text-center px-5 sm:px-8 py-14 sm:py-20 gap-5 bg-[var(--sub-table-color)]">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-[var(--focus-color)]/30 bg-[var(--focus-color)]/10 px-3 py-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.07em] text-[var(--focus-color)]">
            <flux:icon name="shield-check" class="w-3 h-3" />
            {{ env('UNIVERSITAS') }}
        </span>

        <h1 class="text-2xl sm:text-4xl font-bold leading-tight text-[var(--contrast-main-text)] max-w-xl"
            style="letter-spacing: -0.02em;">
            Sistem Manajemen<br>Pembelajaran Outcome-Based Education
        </h1>

        <p class="text-sm sm:text-base text-[var(--contrast-third-text)] leading-relaxed max-w-md">
            Platform akademik terintegrasi untuk pengelolaan RPS, Kelas, Absensi, dan Capaian Pembelajaran berbasis kurikulum Outcome-Based Education.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-3 mt-1">
            @auth
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center gap-2 px-5 py-2.5 rounded-[11px] text-sm font-bold text-[var(--main-text)] transition-all active:scale-[0.99]"
                    style="background: var(--main-color);">
                    <flux:icon name="squares-2x2" class="w-4 h-4" />
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate
                    class="flex items-center gap-2 px-5 py-2.5 rounded-[11px] text-sm font-bold text-[var(--main-text)] transition-all active:scale-[0.99]"
                    style="background: var(--main-color);">
                    <flux:icon name="arrow-right-start-on-rectangle" class="w-4 h-4" />
                    Masuk ke Akun
                </a>
                <a href="{{ route('register') }}" wire:navigate
                    class="flex items-center gap-2 px-5 py-2.5 rounded-[11px] border border-[var(--border-table-color)] text-sm font-semibold text-[var(--contrast-second-text)] bg-[var(--second-table-color)] hover:bg-[var(--hover-table-color)] transition-all">
                    <flux:icon name="user-plus" class="w-4 h-4" />
                    Daftar Admin
                </a>
            @endauth
        </div>
    </section>

    {{-- ═══ STATISTIK ═══ --}}
    <div class="grid grid-cols-3 border-y border-[var(--border-table-color)] bg-[var(--second-table-color)]">
        @foreach ([
            ['label' => 'Program Studi', 'value' => '12+', 'icon' => 'building-library'],
            ['label' => 'Mata Kuliah', 'value' => '142', 'icon' => 'book-open'],
            ['label' => 'Mahasiswa Aktif', 'value' => '1.2k', 'icon' => 'users'],
        ] as $stat)
            <div class="flex flex-col items-center py-5 px-3 gap-1 {{ !$loop->last ? 'border-r border-[var(--border-table-color)]' : '' }}">
                <flux:icon name="{{ $stat['icon'] }}" class="w-4 h-4 text-[var(--focus-color)] mb-1" />
                <span class="text-xl sm:text-2xl font-bold text-[var(--contrast-main-text)]">{{ $stat['value'] }}</span>
                <span class="text-[10px] sm:text-xs text-[var(--contrast-third-text)]">{{ $stat['label'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- ═══ FITUR ═══ --}}
    <section class="px-5 sm:px-8 py-10 bg-[var(--sub-table-color)]">
        <h2 class="text-center text-xs font-bold uppercase tracking-[0.08em] text-[var(--contrast-third-text)] mb-6">
            Fitur Utama
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 max-w-5xl mx-auto">
            @foreach ([
                ['icon' => 'document-text', 'title' => 'Manajemen RPS', 'desc' => 'Kelola RPS lengkap dengan CPL, CPMK, dan Sub-CPMK sesuai kurikulum OBE.', 'color' => 'var(--focus-color)', 'soft' => 'color-mix(in srgb, var(--focus-color) 12%, transparent)'],
                ['icon' => 'users', 'title' => 'Kelas & Absensi', 'desc' => 'Jadwal kelas, sesi pertemuan, dan presensi mahasiswa secara real-time.', 'color' => '#10b981', 'soft' => 'rgba(16,185,129,0.12)'],
                ['icon' => 'chart-bar', 'title' => 'Capaian Pembelajaran', 'desc' => 'Laporan IPK, IPS, capaian CPL per program studi, dan analitik nilai.', 'color' => '#f59e0b', 'soft' => 'rgba(245,158,11,0.12)'],
                ['icon' => 'building-library', 'title' => 'Akreditasi Prodi', 'desc' => 'Pantau indeks kinerja dan status akreditasi program studi secara terpusat.', 'color' => '#7c3aed', 'soft' => 'rgba(124,58,237,0.12)'],
            ] as $feat)
                <div class="flex flex-col gap-3 rounded-[16px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg"
                        style="background: {{ $feat['soft'] }};">
                        <flux:icon name="{{ $feat['icon'] }}" class="w-4.5 h-4.5" style="color: {{ $feat['color'] }};" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[var(--contrast-main-text)] mb-1">{{ $feat['title'] }}</p>
                        <p class="text-xs text-[var(--contrast-third-text)] leading-relaxed">{{ $feat['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ ROLE ═══ --}}
    <section class="px-5 sm:px-8 pb-10 bg-[var(--sub-table-color)]">
        <h2 class="text-center text-xs font-bold uppercase tracking-[0.08em] text-[var(--contrast-third-text)] mb-4">
            Akses sesuai peran
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-5xl mx-auto">
            @foreach ([
                ['tag' => 'Admin', 'name' => 'Administrator', 'desc' => 'Kontrol penuh atas seluruh data Program Studi, Dosen, Mahasiswa, dan Konfigurasi Sistem.', 'accent' => '#ef4444', 'soft' => 'rgba(239,68,68,0.12)', 'icon' => 'shield-check'],
                ['tag' => 'Dosen', 'name' => 'Dosen Pengampu', 'desc' => 'Kelola RPS, Kelas, Pertemuan, dan Penilaian Mahasiswa dalam lingkup Mata Kuliah yang diampu.', 'accent' => 'var(--focus-color)', 'soft' => 'color-mix(in srgb, var(--focus-color) 12%, transparent)', 'icon' => 'presentation-chart-bar'],
                ['tag' => 'Mahasiswa', 'name' => 'Mahasiswa', 'desc' => 'Lihat Jadwal, Absensi, Nilai, dan Progres Capaian Pembelajaran Semester berjalan.', 'accent' => '#10b981', 'soft' => 'rgba(16,185,129,0.12)', 'icon' => 'academic-cap'],
            ] as $role)
                <div class="flex flex-col gap-3 rounded-[16px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0"
                            style="background: {{ $role['soft'] }};">
                            <flux:icon name="{{ $role['icon'] }}" class="w-4 h-4" style="color: {{ $role['accent'] }};" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.07em] rounded-md px-2 py-0.5"
                            style="background: {{ $role['soft'] }}; color: {{ $role['accent'] }};">
                            {{ $role['tag'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[var(--contrast-main-text)] mb-1">{{ $role['name'] }}</p>
                        <p class="text-xs text-[var(--contrast-third-text)] leading-relaxed">{{ $role['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="flex flex-col sm:flex-row items-center justify-between gap-2 px-5 sm:px-8 py-4 border-t border-[var(--border-table-color)] bg-[var(--second-table-color)]">
        <span class="text-[10px] text-[var(--contrast-third-text)]">
            © {{ date('Y') }} {{ env('UNIVERSITAS') }}
        </span>
        <span class="text-[10px] text-[var(--contrast-third-text)] flex items-center gap-1.5">
            <flux:icon name="shield-check" class="w-3 h-3 text-[var(--focus-color)]" />
            Sistem Akademik Terintegrasi
        </span>
    </footer>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts

</body>

</html>