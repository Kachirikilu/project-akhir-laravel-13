@php
    $rawPct = $max > 0 ? min(100, ($value / $max) * 100) : 0;
    $pct = (float) number_format($rawPct, 1, '.', '');
    if (floor($pct) == $pct) {
        $pct = (int) $pct;
    }
    $radius = 36;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference - ($rawPct / 100) * $circumference;
@endphp

<div class="flex flex-col rounded-[20px] border border-[var(--border-table-color)] bg-[var(--main-table-color)]/80 p-4 sm:p-5 gap-5">

    {{-- Header: icon + judul --}}
    <div class="flex items-center gap-3">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0"
            style="background: {{ $accentSoft }};">
            <flux:icon name="{{ $icon }}" class="w-4 h-4" style="color: {{ $accent }};" />
        </div>
        <span class="text-xs sm:text-sm font-bold tracking-tight text-[var(--contrast-main-text)] truncate">
            {{ $title }}
        </span>
    </div>

    {{-- Body: donut chart (SVG) + nilai --}}
    {{-- PERUBAHAN DI SINI: flex-col untuk mobile, sm:flex-row untuk layar lebih lebar --}}
    <div class="flex flex-col sm:flex-row items-center sm:items-center gap-4">
        
        {{-- Chart --}}
        <div class="relative flex-shrink-0" style="width: 84px; height: 84px;">
            <svg width="84" height="84" viewBox="0 0 84 84" class="-rotate-90">
                <circle cx="42" cy="42" r="{{ $radius }}" fill="none"
                    stroke="var(--main-pop-up-color)" stroke-width="10" />
                <circle cx="42" cy="42" r="{{ $radius }}" fill="none"
                    stroke="{{ $accent }}" stroke-width="10" stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                    style="transition: stroke-dashoffset 0.6s ease;" />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-black leading-none text-[var(--contrast-main-text)]">
                    {{ $pct }}%
                </span>
            </div>
        </div>

        {{-- Text Info --}}
        {{-- Menambahkan text-center untuk mobile agar rapi saat menjadi kolom --}}
        <div class="flex flex-col gap-1 min-w-0 text-center sm:text-left">
            <span class="text-base sm:text-lg font-black leading-none text-[var(--contrast-main-text)]">
                {{ $displayValue }}
            </span>
            <span class="text-[11px] sm:text-xs text-[var(--contrast-third-text)]">
                {{ $subtitle }}
            </span>
        </div>
    </div>
</div>