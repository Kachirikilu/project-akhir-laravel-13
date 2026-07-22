@php
    $data = [
        'nama' => Auth::user()->name ?? 'Budi Santoso',
        'ID_AKADEMIK' => Auth::user()->identity1,
        'id_label' => Auth::user()->label_id1,
        'ttl' =>
            Auth::user()->tmt_lahir && Auth::user()->tgl_lahir
                ? Auth::user()->tmt_lahir . ', ' . Auth::user()->tgl_lahir
                : Auth::user()->tmt_lahir ?? (Auth::user()->tgl_lahir ?? 'Tidak Terdaftar'),
        'gender' => Auth::user()->gender ?? 'Tidak Terdaftar',
        'agama' => Auth::user()->agama ?? 'Tidak Terdaftar',
        'no_hp' => Auth::user()->no_wa_full ?? 'Tidak Terdaftar',
        'wa_aktif' => Auth::user()->wa_aktif,
        'kode_pr' => Auth::user()->kode_pr ?? 'XXX',
        'kode_fk' => Auth::user()->kode_fk ?? 'XXX',
        'prodi' => Auth::user()->prodi ?? 'Tidak Terdaftar',
        'fakultas' => Auth::user()->fakultas_fk ?? 'Tidak Terdaftar',
    ];
@endphp

{{-- ============================================================
         HEADER: IDENTITAS PENGGUNA
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

    {{-- Body --}}
    <div class="flex flex-1 flex-col gap-2.5 p-3 sm:p-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">

            {{-- Program Studi --}}
            <div
                class="col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Program
                    Studi</span>
                <span class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">
                    {{ $data['prodi'] }}
                    <span class="text-[var(--focus-color)]">({{ $data['kode_pr'] }})</span>
                </span>
            </div>

            {{-- Fakultas --}}
            <div
                class="col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                <span
                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Fakultas</span>
                <span class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">
                    {{ $data['fakultas'] }}
                    <span class="text-[var(--focus-color)]">({{ $data['kode_fk'] }})</span>
                </span>
            </div>

            {{-- TTL --}}
            <div
                class="col-span-2 flex flex-col gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Tempat
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

        {{-- No HP / WhatsApp --}}
        <div
            class="flex items-center gap-2.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-3 py-2.5">
            <div
                class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $data['wa_aktif'] ? 'bg-emerald-500/15' : 'bg-[var(--sub-table-color)]' }}">
                <flux:icon name="device-phone-mobile"
                    class="w-4 h-4 {{ $data['wa_aktif'] ? 'text-emerald-500' : 'text-[var(--contrast-third-text)]' }}" />
            </div>
            <div class="flex flex-col gap-0.5 min-w-0 flex-1">
                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">No.
                    WhatsApp</span>
                <span
                    class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">{{ $data['no_hp'] }}</span>
            </div>
            <div class="flex items-center gap-4 flex-shrink-0">
                @if ($data['wa_aktif'])
                    <span
                        class="hidden sm:inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">
                        <flux:icon name="check-badge" class="w-3 h-3" />
                        Aktif
                        @if (Auth::user()->mahasiswa)
                            <span class="mx-1">|</span> 50 Token
                        @endif
                    </span>
                @else
                    <span
                        class="hidden sm:inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                        <flux:icon name="exclamation-triangle" class="w-3 h-3" />
                        Belum Aktif
                    </span>
                @endif
                <div>
                    <button
                        @click="
                                $store.user?.reset();
                                $flux.modal('wa-activation-modal').show();
                                $dispatch('open-edit-wa-activation-modal');
                            "
                        class="cursor-pointer flex items-center gap-1.5 rounded-[10px] border-0 px-3 py-2 text-[11px] sm:text-xs font-bold tracking-[0.02em] bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 text-white active:scale-[0.97]">
                        <flux:icon name="cog-6-tooth" class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">{{ $data['wa_aktif'] ? 'Kelola' : 'Aktifkan' }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Container Utama Riwayat Perangkat --}}
        <div class="mt-2 flex flex-col gap-2">
            {{-- Judul --}}
            <span class="text-[10px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)] px-1">
                {{ count($sessions) }} Perangkat terdeteksi
            </span>

            {{-- Daftar Perangkat --}}
            <div class="flex flex-col gap-1.5">
                @foreach ($sessions as $device)
                    <div
                        class="flex items-center gap-2.5 rounded-[10px] border pl-3 pr-5 py-2.5 
                {{ $device->is_current
                    ? 'bg-[var(--focus-color)]/[0.1] border-[var(--focus-color)]/30'
                    : 'border-[var(--border-table-color)] bg-[var(--second-table-color)]' }}">

                        {{-- Ikon --}}
                        <div
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg 
                    {{ $device->is_current ? 'bg-[var(--focus-color)]/20' : 'bg-[var(--sub-table-color)]' }}">
                            <flux:icon name="{{ $device->is_desktop ? 'computer-desktop' : 'device-phone-mobile' }}"
                                class="w-4 h-4 {{ $device->is_current ? 'text-[var(--focus-color)]' : 'text-[var(--contrast-third-text)]' }}" />
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col gap-0.5 min-w-0 flex-1">
                            <span
                                class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
                                {{ $device->platform }} {{ $device->is_current ? '• Sesi Saat Ini' : '' }}
                            </span>
                            <span class="text-xs sm:text-sm font-semibold text-[var(--contrast-main-text)] truncate">
                                {{ $device->device }}
                                <span
                                    class="text-[var(--contrast-third-text)] font-normal">({{ $device->browser }})</span>
                            </span>
                        </div>

                        {{-- Waktu --}}
                        <div class="flex-shrink-0 text-[10px] text-[var(--contrast-third-text)] font-medium">
                            {{ $device->last_activity }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
