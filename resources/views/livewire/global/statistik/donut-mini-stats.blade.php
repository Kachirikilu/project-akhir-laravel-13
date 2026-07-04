@php
    $size    = $size ?? 56;
    $pctSize = $pctSize ?? 'text-xs';
    $r      = 28;
    $c      = 2 * M_PI * $r;
    $pct    = ($max ?? 0) > 0 ? min(100, round(($value / $max) * 100)) : 0;
    $offset = $c - ($pct / 100) * $c;
    $vb = 64;
@endphp

<div class="flex flex-col gap-3">

    {{-- Label atas --}}
    <div class="flex items-center gap-2.5">
        <div class="flex h-5 w-5 items-center justify-center rounded flex-shrink-0"
            style="background: {{ $softBg }};">
            <flux:icon name="{{ $icon }}" class="w-3 h-3" style="color: {{ $accent }};" />
        </div>
        <span class="text-[9px] sm:text-xs font-bold uppercase tracking-[0.08em] text-[var(--contrast-third-text)] truncate">
            {{ $title }}
        </span>
    </div>

    {{-- Donut + nilai --}}
    <div class="flex items-center gap-3">
        <div class="relative flex-shrink-0" style="width: {{ $size }}px; height: {{ $size }}px;">
            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $vb }} {{ $vb }}" class="-rotate-90">
                {{-- Track --}}
                <circle cx="32" cy="32" r="{{ $r }}" fill="none"
                    stroke="var(--main-table-color)" stroke-width="8" />
                {{-- Progress --}}
                <circle cx="32" cy="32" r="{{ $r }}" fill="none"
                    stroke="{{ $accent }}" stroke-width="8"
                    stroke-linecap="round"
                    stroke-dasharray="{{ round($c, 2) }}"
                    stroke-dashoffset="{{ round($offset, 2) }}"
                    style="transition: stroke-dashoffset 0.5s ease;" />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="{{ $pctSize }} font-black leading-none text-[var(--contrast-main-text)]">
                    {{ $pct }}%
                </span>
            </div>
        </div>

        <div class="flex flex-col gap-1 min-w-0">
            <span class="text-md sm:text-lg font-black leading-tight" style="color: {{ $textColor }};">
                {{ $display }}
            </span>
            <span class="text-[9px] sm:text-xs text-[var(--contrast-third-text)] leading-snug">
                {{ $sub }}
            </span>
        </div>
    </div>
</div>