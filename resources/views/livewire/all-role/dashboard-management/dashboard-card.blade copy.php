<div class="flex flex-col gap-4 sm:gap-6 p-3 sm:p-6 max-w-6xl mx-auto">

    {{-- ============================================================
         DUMMY DATA (hapus saat sudah pakai data asli)
         ============================================================ --}}
    @php
        $data = [
            'nama' => Auth::user()->name ?? 'Budi Santoso',
            'ID_AKADEMIK' => Auth::user()->identity1,
            'id_label' => Auth::user()->label_id1,
            'ttl' => Auth::user()->tmt_lahir . ', ' . Auth::user()->tgl_lahir,
            'gender' => Auth::user()->gender,
            'agama' => Auth::user()->agama,
            'no_hp' => Auth::user()->no_wa_full,
            'wa_aktif' => Auth::user()->wa_aktif,

            'kode_pr' => Auth::user()->kode_pr,
            'kode_fk' => Auth::user()->kode_fk,
            'prodi' => Auth::user()->prodi,
            'fakultas' => Auth::user()->fakultas_fk,
        ];
    @endphp

    {{-- ============================================================
         HEADER: IDENTITAS PENGGUNA (Tampil untuk SEMUA role)
         ============================================================ --}}
    <div
        class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200">

        {{-- Hero --}}
        <div class="flex flex-col gap-3 p-4 sm:p-[18px] bg-[var(--main-color)]">

            <div class="flex items-start justify-between gap-2">
                <span
                    class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.07em] text-white/75">
                    <flux:icon name="identification" class="w-3 h-3" />
                    @if (Auth::user()->admin)
                        Administrator
                    @elseif (Auth::user()->dosen)
                        Dosen
                    @elseif (Auth::user()->mahasiswa)
                        Mahasiswa
                    @endif
                </span>
            </div>

            <p class="text-md sm:text-lg font-bold leading-[1.35] tracking-[-0.02em] text-[var(--main-text)]">
                {{ $data['nama'] }}
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs sm:text-sm font-medium text-[var(--main-text)]/65">
                    <flux:icon name="hashtag" class="w-3 h-3" />
                    {{ $data['id_label'] }}: {{ $data['ID_AKADEMIK'] }}
                </span>
            </div>
        </div>
      
        {{-- Body: detail identitas --}}
        <div class="flex flex-1 flex-col gap-2.5 p-3 sm:p-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">

                <div
                    class="col-span-2 sm:col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
                        Program Studi</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">{{ $data['prodi'] }} <span
                        class="text-[var(--focus-color)]">({{ $data['kode_pr'] }})</span></span>
                </div>
                    <div
                    class="col-span-2 sm:col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
                        Fakultas</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">{{ $data['fakultas'] }} <span
                        class="text-[var(--focus-color)]">({{ $data['kode_fk'] }})</span></span>
                </div>

                {{-- Tempat/Tanggal Lahir --}}
                <div
                    class="col-span-2 sm:col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Tempat
                        / Tanggal Lahir</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">{{ $data['ttl'] }}</span>
                </div>

                {{-- Gender --}}
                <div
                    class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Gender</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)]">{{ $data['gender'] }}</span>
                </div>

                {{-- Agama --}}
                <div
                    class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Agama</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)]">{{ $data['agama'] }}</span>
                </div>
            </div>

            {{-- ============================================================
                 NO HP / WHATSAPP — FOKUS UTAMA
                 ============================================================ --}}
            <div
                class="flex items-center gap-2.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-3 py-2.5">

                {{-- Icon WA status --}}
                <div
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $data['wa_aktif'] ? 'bg-emerald-500/15' : 'bg-[var(--sub-table-color)]' }}">
                    <flux:icon name="device-phone-mobile"
                        class="w-4.5 h-4.5 {{ $data['wa_aktif'] ? 'text-emerald-500' : 'text-[var(--contrast-third-text)]' }}" />
                </div>

                <div class="flex flex-col gap-0.5 min-w-0 flex-1">
                    <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">No.
                        WhatsApp</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">{{ $data['no_hp'] }}</span>
                </div>

                {{-- Badge status + tombol aksi --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if ($data['wa_aktif'])
                        <span
                            class="hidden sm:inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                            <flux:icon name="check-badge" class="w-3 h-3" />
                            Aktif
                        </span>
                    @else
                        <span
                            class="hidden sm:inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                            <flux:icon name="exclamation-triangle" class="w-3 h-3" />
                            Belum Aktif
                        </span>
                    @endif

                    {{-- Tombol pop-up modal --}}
                    <flux:modal.trigger name="wa-activation">
                        <button
                            class="cursor-pointer transition-all flex items-center gap-1.5 rounded-[10px] border-0 px-3 py-2 text-[11px] sm:text-xs font-bold tracking-[0.02em] bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out text-white transition-all active:scale-[0.97]">
                            <flux:icon name="cog-6-tooth" class="w-3.5 h-3.5" />
                            <span class="hidden sm:inline">{{ $data['wa_aktif'] ? 'Kelola' : 'Aktifkan' }}</span>
                        </button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL: AKTIVASI WHATSAPP
         ============================================================ --}}
    <flux:modal name="wa-activation" class="max-w-md w-full">
        <div class="flex flex-col gap-5">

            {{-- Header Modal --}}
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-500/15">
                        <flux:icon name="device-phone-mobile" class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <flux:heading size="lg">Aktifkan WhatsApp</flux:heading>
                        <flux:text class="text-xs sm:text-sm text-[var(--contrast-third-text)]">
                            Verifikasi nomor untuk notifikasi otomatis
                        </flux:text>
                    </div>
                </div>
            </div>

            {{-- Nomor saat ini --}}
            <div
                class="flex items-center gap-2.5 rounded-[12px] border border-[var(--border-table-color)] bg-[var(--sub-table-color)] px-3 py-2.5">
                <flux:icon name="phone" class="w-4 h-4 text-[var(--contrast-third-text)] flex-shrink-0" />
                <div class="flex flex-col min-w-0">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Nomor
                        Terdaftar</span>
                    <span
                        class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)]">{{ $data['no_hp'] }}</span>
                </div>
            </div>

            {{-- Step 1: Kirim kode aktivasi --}}
            <div class="flex flex-col gap-2">
                <flux:button variant="primary" icon="paper-airplane"
                    class="cursor-pointer transition-all w-full justify-center bg-[var(--main-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out"
                    wire:click="kirimTokenWhatsapp">
                    Kirim Kode Aktivasi via WhatsApp
                </flux:button>
                <p class="text-[11px] sm:text-xs text-center text-[var(--contrast-third-text)]">
                    Kode 6 digit akan dikirim ke nomor di atas.
                </p>
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-3">
                <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
                <span class="text-[10px] font-bold uppercase tracking-wide text-[var(--contrast-third-text)]">Masukkan
                    Token</span>
                <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
            </div>

            {{-- Step 2: Input 6 digit token --}}
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-center gap-2" x-data="{ digits: ['', '', '', '', '', ''] }">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" maxlength="1"
                            x-model="digits[{{ $i }}]"
                            @input="
                                $event.target.value = $event.target.value.replace(/[^0-9]/g, '');
                                if ($event.target.value && $event.target.nextElementSibling) {
                                    $event.target.nextElementSibling.focus();
                                }
                            "
                            @keydown.backspace="
                                if (!$event.target.value && $event.target.previousElementSibling) {
                                    $event.target.previousElementSibling.focus();
                                }
                            "
                            class="w-10 h-12 sm:w-12 sm:h-14 text-center text-lg sm:text-xl font-black rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-pop-up-color)] text-[var(--contrast-main-text)] focus:outline-none focus:ring-2 focus:ring-[var(--focus-color)] focus:border-[var(--focus-color)] transition-all" />
                    @endfor
                </div>

                <p class="text-[11px] sm:text-xs text-center text-[var(--contrast-third-text)]">
                    Tidak menerima kode?
                    <button wire:click="kirimTokenWhatsapp"
                        class="font-semibold text-[var(--focus-color)] hover:text-[var(--hover-focus-color)] active:text-[var(--hover-focus-color)]/90 cursor-pointer">
                        Kirim ulang
                    </button>
                </p>
            </div>

            {{-- Footer Aksi --}}
            <div class="flex items-center gap-2 pt-1">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer flex-1 justify-center transition-all">Batal
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="check-circle"
                    class="cursor-pointer flex-1 justify-center bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 transition-all"
                    wire:click="verifikasiTokenWhatsapp">
                    Verifikasi
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
