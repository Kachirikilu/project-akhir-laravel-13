<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ config('app.name') }} — Sistem Manajemen Pembelajaran OBE</title>
</head>

<body class="min-h-screen antialiased bg-[var(--wadah-color)] dark:bg-neutral-950">

    {{-- ═══ NAVBAR ═══ --}}
    <nav class="sticky top-0 z-50 flex items-center justify-between px-5 py-4 border-b border-[var(--border-table-color)] bg-[var(--second-table-color)]/80 backdrop-blur-md">
        <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-[10px]" style="background: var(--main-color);">
                <x-app-logo-icon class="h-4 w-auto" />
            </div>
            <span class="text-sm font-bold tracking-tight text-[var(--contrast-second-text)] hidden sm:block">
                {{ config('app.name', 'RPS Manajemen') }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <x-livewire::navigation.dark-mode :noPadding="1" :noToggle="1" />
            
            @auth
                <a href="{{ route('dashboard') }}" wire:navigate class="px-4 py-2 rounded-lg text-xs font-bold text-white bg-[var(--main-color)] flex items-center gap-2">
                    <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="px-4 py-2 rounded-lg border border-[var(--border-table-color)] text-xs font-semibold hover:bg-[var(--sub-table-color)] transition-all">
                    Masuk
                </a>
                @if(\App\Models\User::count() === 0)
                    <a href="{{ route('register') }}" wire:navigate class="px-4 py-2 rounded-lg text-xs font-bold bg-[var(--main-color)] text-white">
                        Daftar Admin
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    {{-- ═══ HERO ═══ --}}
    <header class="py-16 px-5 text-center space-y-6">
        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--focus-color)]/20 bg-[var(--focus-color)]/10 px-4 py-1.5 text-xs font-semibold text-[var(--focus-color)]">
            <flux:icon name="shield-check" class="w-3.5 h-3.5" />
            {{ env('UNIVERSITAS', 'Portal Akademik') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tighter text-[var(--contrast-main-text)]">
            Sistem Manajemen Pembelajaran<br>Outcome-Based Education
        </h1>
        <p class="text-[var(--contrast-third-text)] max-w-lg mx-auto leading-relaxed">
            Kelola RPS, kurikulum, dan capaian pembelajaran mahasiswa dalam satu platform yang terintegrasi dan modern.
        </p>
    </header>

    {{-- ═══ STATISTIK DYNAMIC ═══ --}}
    <section class="max-w-4xl mx-auto px-5 mb-16">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php
                $stats = [
                    ['label' => 'Program Studi', 'val' => \App\Models\ProgramStudi\Prodi::count(), 'icon' => 'building-library'],
                    ['label' => 'Mata Kuliah', 'val' => \App\Models\Akademik\MataKuliah::count(), 'icon' => 'book-open'],
                    ['label' => 'Mahasiswa Aktif', 'val' => \App\Models\Auth\Mahasiswa::where('status', 'Aktif')->count(), 'icon' => 'users'],
                ];
            @endphp
            @foreach($stats as $stat)
                <div class="p-6 rounded-2xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] text-center">
                    <flux:icon name="{{ $stat['icon'] }}" class="w-6 h-6 mx-auto mb-3 text-[var(--focus-color)]" />
                    <div class="text-2xl font-black">{{ $stat['val'] }}</div>
                    <div class="text-xs text-[var(--contrast-third-text)] uppercase tracking-widest mt-1">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Footer, Script, dan lainnya tetap di bawah... --}}
    @fluxScripts
</body>
</html>