<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Error') — @yield('code', '500')</title>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-[var(--wadah-color)] flex items-center justify-center p-5">
    <div class="absolute inset-0 bg-black/10 z-0"></div>
    {{-- Background decorative rings --}}
    @include('components.global.bg-elements')


    {{-- Card utama --}}
    <div class="relative z-10 w-full max-w-md px-6 flex flex-col items-center text-center gap-5">

        {{-- Kode error besar + ikon di tengah --}}
        <div class="relative inline-flex items-center justify-center select-none">
            <span
                class="text-[100px] sm:text-[120px] font-black leading-none tracking-[-0.06em] text-[var(--focus-color)]">
                @yield('code', '500')
            </span>

        </div>
        <div class="flex flex-col items-center gap-2">
            <h1 class="text-lg sm:text-2xl font-bold tracking-tight text-[var(--contrast-main-text)]"
                style="letter-spacing: -0.02em;">
                @yield('headline', 'Terjadi Kesalahan')
            </h1>
            <span
                class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-red-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.07em] text-red-500 dark:text-red-400">
                <flux:icon name="x-circle" class="w-3 h-3" />
                Error @yield('code', '500')
            </span>
        </div>

        {{-- Pesan keterangan --}}
        <div
            class="w-full rounded-[14px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-4 text-left">
            <p class="text-[9px] font-bold uppercase tracking-[0.08em] text-[var(--contrast-third-text)] mb-2">
                Keterangan
            </p>
            <p class="text-xs text-[var(--contrast-second-text)] leading-relaxed">
                @yield('message', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi atau hubungi administrator sistem.')
            </p>
        </div>

        {{-- Tombol aksi --}}
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url()->previous() }}"
                class="flex items-center gap-2 text-xs font-semibold text-[var(--focus-color)] hover:text-[var(--hover-focus-color)] active:text-[var(--hover-focus-color)]/90 transition-colors duration-300">
                <flux:icon name="arrow-left" class="w-3.5 h-3.5" />
                Kembali
            </a>
            @if (Auth::user())
                <span class="w-px h-3.5 bg-[var(--border-table-color)]"></span>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2 text-xs font-semibold text-[var(--contrast-second-text)] hover:text-[var(--contrast-main-text)] active:text-[var(--contrast-main-text)]/90 transition-colors duration-300">
                    <flux:icon name="squares-2x2" class="w-3.5 h-3.5" />
                    Ke Dashboard
                </a>
            @endif
            <span class="w-px h-3.5 bg-[var(--border-table-color)]"></span>
            <a href="{{ url('/') }}"
                class="flex items-center gap-2 text-xs font-semibold text-[var(--contrast-second-text)] hover:text-[var(--contrast-main-text)] active:text-[var(--contrast-main-text)]/90 transition-colors duration-300">
                <flux:icon name="computer-desktop" class="w-3.5 h-3.5" />
                Ke Halaman Depan
            </a>
        </div>

        {{-- <x-livewire::navigation.dark-mode :noPadding="1" :noToggle="1" />
                <x-livewire::navigation.color-mode :noBar="1" :autoSmall="1" /> --}}

    </div>

    @fluxScripts

</body>

</html>
