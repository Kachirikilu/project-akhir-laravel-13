<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ config('app.name') }} — Sistem Manajemen Pembelajaran OBE</title>
</head>

<body
    class="min-h-screen antialiased bg-[var(--wadah-color)] dark:bg-neutral-950 text-[var(--contrast-main-text)] scrollbar-large">

    <div class="relative w-full min-h-screen overflow-hidden">
        @include('components.global.bg-elements')

        {{-- ═══ NAVBAR ═══ --}}
        <nav
            class="sticky top-0 z-50 flex items-center justify-between px-5 py-4 border-b border-[var(--border-table-color)] bg-[var(--second-table-color)]/50 backdrop-blur-lg">
            <div class="flex items-center gap-4">
                <div class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-[var(--main-color)]/90">
                    <x-app-logo-icon class="h-5 w-auto text-white" />
                </div>
                <span class="text-sm font-bold tracking-widest hidden sm:block">
                    {{ config('app.name', 'RPS Manajemen') }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-4 mr-2">
                    <x-livewire::navigation.dark-mode :noPadding="1" :noToggle="1" />
                    <x-livewire::navigation.color-mode :noBar="1" :autoSmall="1" />
                </div>
                @auth
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="px-4 py-2 rounded-xl text-xs font-bold bg-[var(--main-color)] border-[var(--focus-color)] border text-white flex items-center gap-2 transition hover:opacity-90 active:opacity-80">
                        <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                        Dashboard
                    </a>
                @else
                    @if (\App\Models\User::count() === 0)
                        <a href="{{ route('register') }}" wire:navigate
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-[var(--main-color)] border-[var(--focus-color)] text-white shadow-lg">
                            Daftar Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="px-4 py-2 rounded-xl border border-[var(--border-table-color)] text-xs font-semibold hover:bg-[var(--main-color)] hover:text-[var(--main-text)] active:bg-[var(--main-color)] active:text-[var(--main-text)] transition-all">
                            Masuk
                        </a>
                    @endif
                @endauth
            </div>
        </nav>


        {{-- ═══ HERO ═══ --}}
        <header class="py-24 px-5 text-center space-y-8">
            <span
                class="text-[var(--focus-color)] inline-flex items-center gap-2 rounded-full border border-[var(--focus-color)]/30 bg-[var(--focus-color)]/10 px-3 py-1 text-xs sm:text-sm font-bold uppercase tracking-[0.07em]">
                <flux:icon name="shield-check" class="w-4 h-4" />
                {{ env('UNIVERSITAS') }}
            </span>

            <h1
                class="text-4xl sm:text-6xl font-extrabold tracking-tighter text-[var(--contrast-main-text)] max-w-2xl mx-auto leading-[1.1]">
                Manajemen Pembelajaran<br>Berbasis <span class="text-[var(--focus-color)]">OBE</span>
            </h1>
            <p class="text-[var(--contrast-third-text)] max-w-lg mx-auto text-lg leading-relaxed">
                Platform untuk pengelolaan RPS, Kurikulum, dan Capaian Pembelajaran yang terpusat.
            </p>
        </header>

        {{-- ═══ STATISTIK ═══ --}}
        <section class="max-w-4xl mx-auto px-5 mb-24">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @php
                    $stats = [
                        [
                            'label' => 'Program Studi',
                            'val' => \App\Models\ProgramStudi\Prodi::count(),
                            'sub' => \App\Models\ProgramStudi\Fakultas::count() . ' Fakultas',
                            'icon' => 'building-library',
                        ],
                        [
                            'label' => 'Mata Kuliah',
                            'val' => \App\Models\Akademik\MataKuliah::whereHas('rps', function ($q) {
                                $q->where('is_draf', 0);
                            })->count(),
                            'sub' => \App\Models\Akademik\TimDosen::count() . ' Tim Pengajar',
                            'icon' => 'book-open',
                        ],
                        [
                            'label' => 'Mahasiswa Aktif',
                            'val' => \App\Models\Auth\Mahasiswa::where('status', 'Aktif')->count(),
                            'sub' => \App\Models\Auth\Dosen::where('status', 'Aktif')->count() . ' Dosen Aktif',
                            'icon' => 'users',
                        ],
                    ];
                @endphp

                @foreach ($stats as $index => $stat)
                    <div
                        class="group relative overflow-hidden rounded-3xl border border-[var(--border-table-color)] bg-[var(--second-table-color)]/30 backdrop-blur-lg shadow-sm {{ $loop->last ? 'col-span-2 sm:col-span-1' : '' }}">

                        {{-- Background Hover --}}
                        <div
                            class="absolute right-0 bottom-0 aspect-square w-[180%] translate-x-1/2 translate-y-1/2 rounded-full bg-[var(--focus-color)] scale-0 transition-transform duration-[800ms] ease-out group-hover:scale-[2] group-hover:duration-[600ms] group-active:scale-[2] group-active:duration-[600ms]">
                        </div>

                        {{-- Isi --}}
                        <div class="relative z-10">
                            <div
                                class="absolute bottom-0 right-0 w-16 h-16 flex items-end justify-end p-2 pointer-events-none">
                                <div
                                    class="w-8 h-8 border-r-4 border-b-4 border-[var(--focus-color)] rounded-br-2xl transition-colors duration-500 group-hover:border-white group-active:border-white">
                                </div>
                            </div>

                            <div class="p-8">
                                <flux:icon name="{{ $stat['icon'] }}"
                                    class="w-7 h-7 mb-4 text-[var(--focus-color)] transition-colors duration-500 group-hover:text-white group-active:text-white" />

                                <div
                                    class="text-3xl font-black tracking-tight transition-colors duration-500 group-hover:text-white group-active:text-white">
                                    {{ $stat['val'] }}
                                </div>

                                <div
                                    class="text-xs sm:text-sm font-bold uppercase tracking-[0.2em] mt-2 text-[var(--contrast-third-text)] transition-colors duration-500 group-hover:text-white/80 group-active:text-white/70">
                                    {{ $stat['label'] }}
                                </div>

                                {{-- Sub Information --}}
                                <div
                                    class="mt-1 text-[10px] font-medium text-[var(--contrast-third-text)] transition-colors duration-500 group-hover:text-white/60 group-active:text-white/50">
                                    {{ $stat['sub'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ═══ FITUR + ROLE (2 kolom desktop) ═══ --}}
        <section
            class="relative z-10 bg-[var(--sub-table-color)]/50 border-t border-[var(--border-table-color)] px-5 sm:px-8 py-12">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14">

                {{-- Fitur --}}
                <div class="flex flex-col gap-5">
                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-[0.12em] text-[var(--contrast-third-text)] mb-1">
                            Fitur Utama</p>
                        <h2 class="text-lg sm:text-xl font-bold tracking-tight text-[var(--contrast-main-text)]">Semua
                            yang Anda butuhkan</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach ([['icon' => 'document-text', 'title' => 'Manajemen RPS', 'desc' => 'CPL, CPMK, Sub-CPMK sesuai standar OBE.', 'color' => 'var(--focus-color)', 'soft' => 'color-mix(in srgb, var(--focus-color) 12%, transparent)'], ['icon' => 'users', 'title' => 'Kelas & Absensi', 'desc' => 'Jadwal dan presensi mahasiswa secara real-time.', 'color' => '#10b981', 'soft' => 'rgba(16,185,129,0.12)'], ['icon' => 'chart-bar', 'title' => 'Capaian Lulusan', 'desc' => 'IPK, IPS, dan analitik nilai per Program Studi.', 'color' => '#f59e0b', 'soft' => 'rgba(245,158,11,0.12)'], ['icon' => 'building-library', 'title' => 'Akreditasi Prodi', 'desc' => 'Pantau kinerja dan status akreditasi terpusat.', 'color' => '#7c3aed', 'soft' => 'rgba(124,58,237,0.12)']] as $feat)
                            <div
                                class="flex flex-col gap-3 rounded-[14px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-4 hover:shadow-sm transition-shadow">
                                <div class="flex h-9 w-9 items-center justify-center rounded-[9px]"
                                    style="background: {{ $feat['soft'] }};">
                                    <flux:icon name="{{ $feat['icon'] }}" class="w-4 h-4"
                                        style="color: {{ $feat['color'] }};" />
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm font-bold text-[var(--contrast-main-text)] mb-0.5">
                                        {{ $feat['title'] }}</p>
                                    <p class="text-[11px] sm:text-xs text-[var(--contrast-third-text)] leading-relaxed">
                                        {{ $feat['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Role --}}
                <div class="flex flex-col gap-5">
                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-[0.12em] text-[var(--contrast-third-text)] mb-1">
                            Akses Pengguna</p>
                        <h2 class="text-lg sm:text-xl font-bold tracking-tight text-[var(--contrast-main-text)]">Tiga
                            peran, satu platform</h2>
                    </div>

                    <div class="flex flex-col gap-3">
                        @foreach ([['tag' => 'Admin', 'name' => 'Administrator', 'desc' => 'Kontrol penuh atas seluruh data Program Studi, Dosen, Mahasiswa, dan konfigurasi sistem.', 'accent' => '#ef4444', 'soft' => 'rgba(239,68,68,0.12)', 'icon' => 'shield-check'], ['tag' => 'Dosen', 'name' => 'Dosen Pengampu', 'desc' => 'Kelola RPS, Kelas, Pertemuan, dan Penilaian dalam lingkup Mata Kuliah yang diampu.', 'accent' => '#10b981', 'soft' => 'rgba(16,185,129,0.12)', 'icon' => 'presentation-chart-bar'], ['tag' => 'Mahasiswa', 'name' => 'Mahasiswa', 'desc' => 'Lihat Jadwal, Absensi, Nilai, dan Progres Capaian Pembelajaran Semester berjalan.', 'accent' => '#06b6d4', 'soft' => 'rgba(6,182,212,0.12)', 'icon' => 'academic-cap']] as $role)
                            <div
                                class="flex items-start gap-4 rounded-[14px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-4 hover:shadow-sm transition-shadow">
                                <div class="flex h-10 w-10 items-center justify-center rounded-[10px] flex-shrink-0"
                                    style="background: {{ $role['soft'] }};">
                                    <flux:icon name="{{ $role['icon'] }}" class="w-4.5 h-4.5"
                                        style="color: {{ $role['accent'] }};" />
                                </div>
                                <div class="flex flex-col gap-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="text-xs sm:text-sm font-bold text-[var(--contrast-main-text)]">{{ $role['name'] }}</span>
                                        <span
                                            class="text-[9px] font-bold uppercase tracking-[0.07em] rounded-md px-2 py-0.5"
                                            style="background: {{ $role['soft'] }}; color: {{ $role['accent'] }};">
                                            {{ $role['tag'] }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] sm:text-xs text-[var(--contrast-third-text)] leading-relaxed">
                                        {{ $role['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══ FOOTER ═══ --}}
        <footer
            class="flex flex-col sm:flex-row items-center justify-between gap-2 px-5 sm:px-8 py-4 border-t border-[var(--border-table-color)] bg-[var(--second-table-color)]">
            <span class="text-[10px] text-[var(--contrast-third-text)]">
                © {{ date('Y') }} {{ env('UNIVERSITAS') }}
            </span>
            <span class="text-[10px] text-[var(--contrast-third-text)] flex items-center gap-1.5">
                <flux:icon name="shield-check" class="w-3 h-3 text-[var(--focus-color)]" />
                Sistem Akademik Terintegrasi
            </span>
        </footer>

    </div>

    @fluxScripts
</body>

</html>
