<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-[var(--wadah-color)] dark:bg-neutral-950">
    @include('components.global.bg-elements')
    <div class="min-h-screen flex items-center justify-center p-3 sm:p-6">
        <div class="w-full max-w-5xl flex rounded-[24px] overflow-hidden border border-[var(--border-table-color)]"
            style="min-height: 560px;">

            {{-- ═══ PANEL KIRI: Branding (Sama seperti Login) ═══ --}}
            <div class="relative hidden lg:flex flex-1 flex-col justify-between p-10 overflow-hidden"
                style="background: var(--main-color);">

                <div class="absolute inset-0"
                    style="background-image: url('{{ asset('images/bg-unsri.webp') }}'); background-size: cover; background-position: center;">
                </div>
                <div class="absolute inset-0 opacity-90"
                    style="background: linear-gradient(150deg, var(--main-color) 0%, var(--hover-main-color) 100%);">
                </div>

                <div class="relative z-10 flex flex-col justify-between h-full gap-8">
                    <a href="/" class="flex items-center gap-5">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/20 bg-white/10 flex-shrink-0">
                            <x-app-logo-icon class="h-5 w-auto" />
                        </div>
                        <span class="text-[var(--main-text)] text-[13px] font-bold uppercase tracking-[0.08em]">
                            {{ config('app.name', 'RPS Manajemen') }}
                        </span>
                    </a>

                    <div class="flex flex-col gap-4">
                        <span
                            class="text-[var(--main-text)] inline-flex items-center gap-1.5 self-start rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.08em]">
                            <flux:icon name="shield-check" class="w-3 h-3" />
                            {{ env('UNIVERSITAS', 'Universitas Sriwijaya') }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-bold leading-tight text-white tracking-tighter">
                            Registrasi Akun<br>Administrator
                        </h1>
                        <p class="text-[var(--main-text)]/70 text-sm leading-relaxed max-w-xs">
                            Halaman ini disediakan sebagai portal pendaftaran untuk membuat akun Administratif, yang
                            akan memberikan akses penuh dalam mengelola Sistem Manajemen Pembelajaran berbasis kurikulum
                            OBE.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-[var(--main-text)] shadow-[0_0_8px_var(--main-text)]"></span>
                            <span class="text-[var(--main-text)]/70 text-xs">Akses penuh Sistem</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span
                                class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-[var(--main-text)] shadow-[0_0_8px_var(--main-text)]"></span>
                            <span class="text-[var(--main-text)]/70 text-xs">Manajemen User & Hak Akses</span>
                        </div>
                        <div class="flex items-center mt-6 gap-4">
                            <x-livewire::navigation.dark-mode :noPadding="1" :noToggle="1" />
                            <x-livewire::navigation.color-mode :noBar="1" :autoSmall="1" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ PANEL KANAN: Form Register ═══ --}}
            <div
                class="w-full lg:w-[600px] flex-shrink-0 flex flex-col justify-center bg-[var(--second-table-color)]/70 backdrop-blur-lg p-8 sm:p-10 overflow-y-auto">

                <div class="mb-6">
                    <h2 class="text-xl font-bold tracking-tight text-[var(--contrast-main-text)]">Buat Akun Admin</h2>
                    <p class="text-xs text-[var(--contrast-third-text)] mt-1">Lengkapi Data Pendaftaran Administrator
                        Baru</p>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
                    @csrf

                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'user',
                        'isLivewire' => 1,
                        'noEntangle' => 1,
                        'nameXString' => 'Nama Anda',
                        'modelString' => 'name',
                        'oldValue' => old('name'),
                        'iconString' => 'user-circle',
                        'placeholder' => 'Masukkan Nama Lengkap...',
                        'message' => $errors->first('name'),
                    ])
                    {{-- <flux:input name="name" label="Nama Lengkap" :value="old('name')" required
                        placeholder="Nama Anda" /> --}}

                    @include('livewire.global.modal-form.input-form', [
                        'alpine' => 'user',
                        'isLivewire' => 1,
                        'noEntangle' => 1,
                        'modelString' => 'email',
                        'typeString' => 'email',
                        'iconString' => 'envelope',
                        'oldValue' => old('email'),
                        'placeholder' => 'Default: nip@staff.unsri.ac.id',
                        'message' => $errors->first('email'),
                        'isRequired' => 0,
                    ])
                    {{-- <flux:input name="email" label="Email" :value="old('email')" type="email" required
                        placeholder="nama@unsri.ac.id" /> --}}

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-3 items-start">
                        {{-- <flux:input name="nip" label="NIP" :value="old('nip')" required maxlength="20"
                            placeholder="Masukkan NIP..." />
                        <flux:input name="nik" label="NIK" :value="old('nik')" required maxlength="16"
                            placeholder="Masukkan NIK..." /> --}}
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'user',
                            'isLivewire' => 1,
                            'noEntangle' => 1,
                            'nameXString' => 'Nomor Induk Pegawai (NIP)',
                            'modelString' => 'nip',
                            'numberOnly' => 1,
                            'maxLength' => 20,
                            'oldValue' => old('nip'),
                            'iconString' => 'identification',
                            'placeholder' => 'Masukkan NIP...',
                            'message' => $errors->first('nip'),
                        ])
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'user',
                            'isLivewire' => 1,
                            'noEntangle' => 1,
                            'nameXString' => 'Nomor Induk Kependudukan (NIK)',
                            'modelString' => 'nik',
                            'numberOnly' => 1,
                            'maxLength' => 16,
                            'oldValue' => old('nik'),
                            'iconString' => 'identification',
                            'placeholder' => 'Masukkan NIK',
                            'message' => $errors->first('nik'),
                        ])
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-3 items-start">
                        {{-- <flux:input name="password" label="Password" type="password" required viewable
                            placeholder="••••••••" />
                        <flux:input name="password_confirmation" label="Konfirmasi Password" type="password" required
                            viewable placeholder="••••••••" /> --}}
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'user',
                            'isLivewire' => 1,
                            'noEntangle' => 1,
                            'modelString' => 'password',
                            'typeString' => 'password',
                            'placeholder' => 'Default: NIP',
                            'message' => $errors->first('password'),
                            'isRequired' => 0,
                        ])
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'user',
                            'isLivewire' => 1,
                            'noEntangle' => 1,
                            'modelString' => 'password_confirmation',
                            'typeString' => 'password',
                            'placeholder' => '••••••••',
                            'message' => $errors->first('password_confirmation'),
                            'isRequired' => 0,
                        ])
                    </div>

                    <div
                        class="mt-2 p-4 rounded-xl border border-[var(--main-color)] bg-[var(--main-color)]/30 bg-opacity-5">
                        {{-- <flux:input name="admin_key" label="Admin Secret Key" type="password" required viewable
                            placeholder="Kunci otorisasi Admin..." /> --}}
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'user',
                            'isLivewire' => 1,
                            'noEntangle' => 1,
                            'nameXString' => 'Admin Secret Key',
                            'modelString' => 'admin_key',
                            'typeString' => 'password',
                            'placeholder' => 'Kunci otorisasi Admin...',
                            'message' => $errors->first('admin_key'),
                            'isRequired' => 0,
                        ])
                    </div>

                    <flux:button variant="primary" type="submit"
                        class="cursor-pointer w-full mt-2 !bg-[var(--main-color)] hover:!bg-[var(--hover-main-color)] text-white font-bold py-2.5">
                        {{ __('Create Account') }}
                    </flux:button>
                </form>
                <p
                    class="mt-5 text-center text-[10px] text-[var(--contrast-third-text)] flex items-center justify-center gap-1.5">
                    <a href="/"
                        class="flex items-center justify-center gap-1.5 transition-colors duration-300 hover:text-[var(--focus-color)] active:text-[var(--focus-color)]">
                        <flux:icon name="shield-check" class="w-3 h-3 text-[var(--focus-color)]" />
                        Sistem Informasi {{ env('UNIVERSITAS', 'Universitas Sriwijaya') }}
                    </a>
                </p>
                {{-- <div class="mt-6 text-center text-xs text-[var(--contrast-third-text)]">
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="font-bold text-[var(--focus-color)] hover:underline ml-1">Log in</a>
                </div> --}}
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
