@php
    $pct = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;
    $radius = 36;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference - ($pct / 100) * $circumference;
@endphp

<div
    class="flex flex-col rounded-[20px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-4 sm:p-5 gap-5">

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
    <div class="flex items-center gap-4">
        <div class="relative flex-shrink-0" style="width: 84px; height: 84px;">
            <svg width="84" height="84" viewBox="0 0 84 84" class="-rotate-90">
                {{-- Track / background ring --}}
                <circle cx="42" cy="42" r="{{ $radius }}" fill="none"
                    stroke="var(--sub-table-color)" stroke-width="10" />
                {{-- Progress ring --}}
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

        <div class="flex flex-col gap-2 min-w-0">
            <span class="text-base sm:text-lg font-black leading-none text-[var(--contrast-main-text)]">
                {{ $displayValue }}
            </span>
            <span class="text-[11px] sm:text-xs text-[var(--contrast-third-text)]">
                {{ $subtitle }}
            </span>
        </div>
    </div>
</div>
