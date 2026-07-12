<div class="grid grid-cols-3 gap-1.5">

    {{-- Absensi --}}
    <div
        class="flex flex-col items-center gap-0.5 rounded-[10px] border {{ $bgBorder }} px-1.5 py-2 text-center">
        <span class="text-[9px] font-bold uppercase tracking-[0.07em] {{ $thirdText }}">Absensi</span>
        <span
            class="text-base font-bold leading-none {{ $mainText }}">{{ $s->total_absensi ?? 0 }}</span>
        <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">/
            {{ $s->count_mahasiswa }}</span>
    </div>

    {{-- Bobot --}}
    <div
        class="flex flex-col items-center gap-0.5 rounded-[10px] border {{ $bgBorder }} px-1.5 py-2 text-center">
        <span class="text-[9px] font-bold uppercase tracking-[0.07em] {{ $thirdText }}">Bobot</span>
        <span
            class="text-base font-bold leading-none {{ $mainText }}">{{ $s->bobot_normalisasi ?? '-' }}</span>
        <span class="text-[9px] font-semibold {{ $secondText }}">%</span>
    </div>

    {{-- ID Sesi --}}
    <div
        class="flex flex-col items-center justify-center gap-1 rounded-[10px] border {{ $bgBorder }} px-1.5 py-2 text-center">
        <span class="text-[9px] font-bold uppercase tracking-[0.07em] {{ $thirdText }}">Metode</span>
        <flux:dropdown>
            <button class="cursor-pointer focus:outline-none">
                @include('livewire.global.table.badge.metode-badge', [
                    'xValue' => $s->metode,
                    'variant' => '',
                ])
            </button>
            @include(
                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                ['key' => 4]
            )
        </flux:dropdown>
    </div>
</div>
