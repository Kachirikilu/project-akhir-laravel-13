<div>
    <flux:modal name="wa-activation-modal" wire:model.live="showWAModal" wire:key="wa-activation--modal"
        @refresh-data-user.window="$store.user.reset()" class="max-w-md w-full">

        @if ($isReady)
            <div class="flex flex-col gap-5">
                @php
                    $waSystem = env('WA_SYSTEM', '628985655826');
                    $cleanWa = preg_replace('/[^0-9]/', '', $waSystem);
                    $waSystemDisplay =
                        '+' .
                        substr($cleanWa, 0, 2) .
                        '-' .
                        substr($cleanWa, 2, 3) .
                        '-' .
                        substr($cleanWa, 5, 4) .
                        '-' .
                        substr($cleanWa, 9);
                    $waSystemName = 'Akademik UNSRI';
                    $waMessage = urlencode('Login ' . Auth::user()->identity1);
                    $waLink = 'https://wa.me/' . $waSystem . '?text=' . $waMessage;
                @endphp
                {{-- Header --}}
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-500/15">
                        <flux:icon name="device-phone-mobile" class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <flux:heading size="lg">Autentikasi WhatsApp</flux:heading>
                        <flux:text class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
                            Verifikasi atau perbarui nomor WhatsApp Anda
                        </flux:text>
                    </div>
                </div>

                {{-- Update nomor HP --}}
                <div class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
                        Nomor WhatsApp Terdaftar
                    </span>
                    <div>
                        @include('livewire.global.modal-form.partial.label', [
                            'nameXString' => 'Nomor Telepon',
                            'isRequired' => 0,
                        ])
                        <div class="grid grid-cols-12 gap-1">

                            <div class="col-span-3">
                                @include('livewire.global.modal-form.kode-input', [
                                    'alpine' => 'user',
                                    'isLivewire' => 1,
                                    'noLabel' => 1,
                                    'modelString' => 'kode_no_hp',
                                    'valueString' => '+62',
                                    'iconString' => 'phone',
                                ])
                            </div>

                            <div class="col-span-9">
                                @include('livewire.global.modal-form.input-form', [
                                    'alpine' => 'user',
                                    'isLivewire' => 1,
                                    'isLivewireBlur' => 1,
                                    'noLabel' => 1,
                                    'modelString' => 'no_hp_back',
                                    'isNoHP' => 1,
                                    'value' => Auth::user()->no_hp_back,
                                    'iconString' => 'device-phone-mobile',
                                    'placeholder' => 'Contoh: 898 - 5655 - 826',
                                    // 'isFocusSelect' => 1,
                                ])
                            </div>
                        </div>
                        @error('user_input.no_hp')
                            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                        @if (session()->has('message'))
                            <span class="text-[9px] sm:text-xs text-green-600 mt-1 block font-semibold">
                                {{ session('message') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-[var(--contrast-third-text)]">
                        Pastikan nomor aktif dan terhubung ke WhatsApp.
                    </p>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-3">
                    <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-[var(--contrast-third-text)]">Kirim
                        Pesan Autentikasi</span>
                    <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                </div>

                {{-- Kontak Akademik UNSRI --}}
                <div
                    class="flex items-center gap-3 rounded-[12px] border border-[var(--border-table-color)] bg-[var(--sub-table-color)] px-3 py-3">
                    <img src="{{ asset('favicon.svg') }}" alt="Logo UNSRI"
                        class="h-10 w-10 flex-shrink-0 rounded-lg object-contain bg-white p-1 border border-[var(--border-table-color)]" />
                    <div class="flex flex-col gap-0.5 min-w-0 flex-1">
                        <span class="text-xs font-bold text-[var(--contrast-main-text)]">{{ $waSystemName }}</span>
                        <span
                            class="text-[10px] sm:text-xs font-medium text-[var(--contrast-third-text)]">{{ $waSystemDisplay }}</span>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                        <flux:icon name="check-badge" class="w-3 h-3" />
                        Official
                    </span>
                </div>

                {{-- Pesan yang akan dikirim --}}
                <div class="flex flex-col gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
                        Pesan yang akan dikirim
                    </span>
                    <div
                        class="flex items-center gap-2 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-3 py-2.5">
                        <flux:icon name="chat-bubble-left-ellipsis"
                            class="w-4 h-4 text-[var(--contrast-third-text)] flex-shrink-0" />
                        <code class="text-xs sm:text-sm font-mono font-bold text-[var(--focus-color)]">
                            Login {{ Auth::user()->identity1 }}
                        </code>
                    </div>
                    <p class="text-[10px] text-[var(--contrast-third-text)]">
                        Pesan ini akan dikirim otomatis saat Anda menekan tombol di bawah.
                    </p>
                </div>

                {{-- Footer Aksi --}}
                <div class="flex items-center gap-2 pt-1">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="cursor-pointer flex-1 justify-center transition-all">Batal</flux:button>
                    </flux:modal.close>
                    <a href="{{ $waLink }}" target="_blank" class="flex-1">
                        <button
                            class="cursor-pointer w-full flex items-center justify-center gap-2 rounded-[11px] px-4 py-2.5 text-xs font-bold tracking-[0.02em] bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white transition-all active:scale-[0.99]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            Kirim via WhatsApp
                        </button>
                    </a>
                </div>
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-wa-skeleton')
        @endif
    </flux:modal>
</div>
