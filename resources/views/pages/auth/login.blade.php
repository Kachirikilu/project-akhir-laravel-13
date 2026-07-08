<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-[var(--wadah-color)] dark:bg-neutral-950">

    <div class="min-h-screen flex items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-5xl flex rounded-[24px] overflow-hidden border border-[var(--border-table-color)]"
            style="min-height: 560px;">

            {{-- ═══ PANEL KIRI: Branding ═══ --}}
            <div class="relative hidden md:flex flex-1 flex-col justify-between p-10 overflow-hidden"
                style="background: var(--main-color);">

                {{-- Foto latar --}}
                <div class="absolute inset-0"
                    style="background-image: url('{{ asset('images/bg-unsri.png') }}'); background-size: cover; background-position: center;">
                </div>
                <div class="absolute inset-0 opacity-90"
                    style="background: linear-gradient(150deg, var(--main-color) 0%, var(--hover-main-color) 100%);">
                </div>

                <div class="relative z-10 flex flex-col justify-between h-full gap-8">

                    {{-- Logo --}}
                    <div class="flex items-center gap-5">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/20 bg-white/10 flex-shrink-0">
                            <x-app-logo-icon class="h-5 w-auto" />
                        </div>
                        <span class="text-[var(--main-text)] text-[13px] font-bold uppercase tracking-[0.08em]">
                            {{ config('app.name', 'RPS Manajemen') }}
                        </span>
                    </div>

                    {{-- Heading --}}
                    <div class="flex flex-col gap-4">
                        <span
                            class="text-[var(--main-text)] inline-flex items-center gap-1.5 self-start rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.08em]">
                            <flux:icon name="shield-check" class="w-3 h-3" />
                            Universitas Sriwijaya
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-bold leading-tight"
                            style="color: #ffffff; letter-spacing: -0.02em;">
                            Sistem Manajemen<br>Pembelajaran OBE
                        </h1>
                        <p class="text-[var(--main-text)]/70 text-sm leading-relaxed max-w-xs">
                            Platform akademik terintegrasi untuk pengelolaan RPS, Kelas, dan Capaian Pembelajaran
                            berbasis Kurikulum OBE.
                        </p>
                    </div>

                    {{-- Fitur --}}
                    <div class="flex flex-col gap-2.5">
                        @foreach (['Manajemen RPS, CPL, dan CPMK', 'Absensi dan Penilaian Mahasiswa', 'Laporan Capaian Program Studi'] as $feat)
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-[var(--main-text)] shadow-[0_0_8px_var(--main-text)]"></span>

                                <span class="text-[var(--main-text)]/70 text-xs">{{ $feat }}</span>
                            </div>
                        @endforeach
                        <div class="mt-6 flex gap-4">
                            <x-livewire::navigation.dark-mode :noPadding="1" />
                            <x-livewire::navigation.color-mode :noBar="1" />
                        </div>
                    </div>

                </div>
            </div>

            {{-- ═══ PANEL KANAN: Form Login ═══ --}}
            <div
                class="w-full md:w-[400px] flex-shrink-0 flex flex-col justify-center bg-[var(--second-table-color)] p-8 sm:p-10">

                {{-- Header --}}
                <div class="mb-7">
                    {{-- Logo mobile --}}
                    <a href="{{ route('home') }}" class="flex items-center gap-4 mb-6 md:hidden" wire:navigate>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0"
                            style="background: var(--main-color);">
                            <x-app-logo-icon class="h-4 w-auto" />
                        </div>

                        <span
                            class="text-xs font-bold uppercase tracking-[0.08em] text-[var(--contrast-second-text)]/70">
                            {{ config('app.name') }}
                        </span>
                    </a>

                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[var(--contrast-main-text)]"
                        style="letter-spacing: -0.02em;">
                        Masuk ke Akun
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--contrast-third-text)] mt-1.5">
                        Gunakan Email dan Password Anda
                    </p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status
                    class="mb-4 text-xs text-center rounded-[10px] p-2.5 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400"
                    :status="session('status')" />

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                    @csrf

                    {{-- Email --}}
                    <flux:input label="Email" name="email" :value="old('email')" type="email" required autofocus
                        autocomplete="email" placeholder="nama@unsri.ac.id" icon="envelope" />
                    @error('email')
                        <p class="text-[11px] text-red-500">{{ $message }}</p>
                    @enderror

                    {{-- Password --}}
                    <flux:input label="Password" name="password" type="password" required
                        autocomplete="current-password" placeholder="••••••••" icon="lock-closed" viewable />
                    @error('password')
                        <p class="text-[11px] text-red-500">{{ $message }}</p>
                    @enderror

                    {{-- Remember Me --}}
                    <div class="flex items-center gap-2">
                        <flux:checkbox class="cursor-pointer" name="remember" :checked="old('remember')" />
                        <span class="text-xs text-[var(--contrast-second-text)]/70">Ingat saya</span>
                    </div>

                    <flux:button variant="primary" type="submit"
                        class="text-[var(--main-text)] cursor-pointer w-full !flex !flex-row !items-center !justify-center !gap-2 py-2.5 text-sm font-bold tracking-[0.02em] !bg-[var(--main-color)] hover:!bg-[var(--hover-main-color)] border-none transition-all active:scale-[0.99]"
                        data-test="login-button">
                        <div class="flex items-center gap-2">
                            <flux:icon name="arrow-right-start-on-rectangle" class="w-4 h-4" />
                            <span>Masuk</span>
                        </div>
                    </flux:button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-[var(--contrast-third-text)]">
                        atau hubungi Admin
                    </span>
                    <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                </div>

                {{-- Logo Sosial Media --}}
                <div class="flex justify-center gap-8 py-2">
                    {{-- WhatsApp --}}
                    <a href="https://wa.me/{{ env('WA_SYSTEM', '628985655826') }}" target="_blank"
                        class="transition-transform hover:scale-110">
                        <svg class="w-6 h-6 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="https://instagram.com/{{ env('IG_USERNAME', 'athif_kyuziera') }}" target="_blank"
                        class="transition-transform hover:scale-110">
                        <svg class="w-6 h-6 text-[#E1306C]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 1.173.055 1.805.249 2.227.415.563.22.964.482 1.385.904.422.421.684.822.904 1.385.166.422.36 1.054.415 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.055 1.173-.249 1.805-.415 2.227-.22.563-.482.964-.904 1.385-.421.421-.822.684-1.385.904-.422.166-1.054.36-2.227.415-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.173-.055-1.805-.249-2.227-.415-.563-.22-.964-.482-1.385-.904-.421-.421-.684-.822-.904-1.385-.166-.422-.36-1.054-.415-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.055-1.173.249-1.805.415-2.227.22-.563.482-.964.904-1.385.421-.421.822-.684 1.385-.904.422-.166 1.054-.36 2.227-.415 1.266-.058 1.646-.07 4.85-.07m0-2.163c-3.259 0-3.667.014-4.947.072-1.277.058-2.15.253-2.915.55-.79.305-1.46.714-2.128 1.382-.668.668-1.077 1.338-1.382 2.128-.297.765-.492 1.638-.55 2.915C0 8.333 0 8.741 0 12c0 3.259.014 3.667.072 4.947.058 1.277.253 2.15.55 2.915.305.79.714 1.46 1.382 2.128.668.668 1.338 1.077 2.128 1.382.765.297 1.638.492 2.915.55C8.333 24 8.741 24 12 24c3.259 0 3.667-.014 4.947-.072 1.277-.058 2.15-.253 2.915-.55.79-.305 1.46-.714 2.128-1.382.668-.668 1.077-1.338 1.382-2.128.297-.765.492-1.638.55-2.915.058-1.277.072-1.688.072-4.947 0-3.259-.014-3.667-.072-4.947-.058-1.277-.253-2.15-.55-2.915-.305-.79-.714-1.46-1.382-2.128-.668-.668-1.338-1.077-2.128-1.382-.765-.297-1.638-.492-2.915-.55C15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z" />
                        </svg>
                    </a>
                </div>
                {{-- Footer --}}
                <p class="mt-6 text-center text-[10px] text-[var(--contrast-third-text)]">
                    Sistem Informasi Universitas Sriwijaya
                </p>
            </div>
        </div>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts

</body>

</html>
