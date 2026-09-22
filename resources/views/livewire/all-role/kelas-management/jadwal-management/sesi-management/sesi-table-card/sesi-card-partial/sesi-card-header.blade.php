<div class="flex flex-col gap-3 p-[18px] {{ $mainColor }}" @click.stop>

    @php
        $dropdownBage =
            ($isPastDate
                ? 'dark:border-white/10 dark:bg-white/5 dark:text-white/50 dark:hover:bg-white/10 dark:active:bg-white/25 '
                : '') . 'border-white/20 bg-white/10 text-white/75 hover:bg-white/20 active:bg-white/50';
        $dateText =
            'inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/64 ' .
            ($isPastDate ? 'dark:text-[var(--main-text)]/32 line-through' : '');
    @endphp
    <div class="flex items-start justify-between gap-2">

        <div class="flex items-center gap-2">
            <flux:dropdown>
                <button
                    class="{{ $dropdownBage }} inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] transition-colors focus:outline-none cursor-pointer">
                    <flux:icon name="bookmark" class="w-3 h-3" />
                    P-{{ $s->pertemuan_ke }}
                </button>
                @include(
                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                    ['key' => 1]
                )
            </flux:dropdown>


            @if ($showMore)
                <flux:dropdown>
                    <button
                        class="{{ $dropdownBage }} inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] transition-colors focus:outline-none cursor-pointer">
                        <flux:icon name="academic-cap" class="w-3 h-3" />
                        {{ $s->metode }}
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        ['key' => 2]
                    )
                </flux:dropdown>
            @endif

            <span
                class="text-xs {{ $isPastDate ? 'text-[var(--main-text)]/64' : 'text-[var(--main-text)]' }} font-mono">ID:
                {{ $s->id }}</span>
        </div>

        {{-- Tombol Menu --}}
        <flux:dropdown>
            <button
                class="{{ $dropdownBage }} flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border transition-colors focus:outline-none cursor-pointer"
                @click.stop>
                <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
            </button>
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                ['key' => 3]
            )
        </flux:dropdown>
    </div>

    {{-- Judul: Sub-CPMK atau label Ujian --}}
    <p
        class="mt-1 text-[15px] font-bold leading-[1.35] tracking-[0.1em] {{ $isPastDate ? 'dark:text-[var(--main-text)]/64' : '' }} text-[var(--main-text)]">

        {{ $s->kode_cpmk ?? 'Sesi Evaluasi Utama' }}

        @if ($isPastDate)
            <span class="mx-2">
                |
            </span>
            <span class="font-mono">
                Selesai
            </span>
        @endif
    </p>

    <div class="flex flex-wrap items-center gap-2">
        <span class="{{ $dateText }}" <flux:icon name="calendar-days" class="w-3 h-3" />
        {{ $s->tanggal_pelaksanaan ?? '-' }}
        </span>
        <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>

        <span class="{{ $dateText }}" <flux:icon name="clock" class="w-3 h-3" />
        {{ $s->hari ?? '-' }}, {{ $s->jam_pelaksanaan ?? '-' }}
        </span>

    </div>

</div>
