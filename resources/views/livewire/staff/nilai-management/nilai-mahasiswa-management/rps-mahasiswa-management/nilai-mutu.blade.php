@php
    $glowClasses = 'ring-zinc-400/40 shadow-zinc-400/20';
    switch ($value) {
        case 'A':
            $glowClasses = 'ring-cyan-300 shadow-cyan-100/50';
            break;
        case 'A-':
            $glowClasses = 'ring-green-300 shadow-green-100/50';
            break;
        case 'B+':
            $glowClasses = 'ring-emerald-300 shadow-emerald-100/50';
            break;
        case 'B':
            $glowClasses = 'ring-yellow-300 shadow-yellow-100/50';
            break;
        case 'B-':
            $glowClasses = 'ring-amber-300 shadow-amber-100/50';
            break;
        case 'C+':
        case 'C':
            $glowClasses = 'ring-orange-300 shadow-orange-100/50';
            break;
        case 'D':
        case 'E':
            $glowClasses = 'ring-red-300 shadow-red-100/50';
            break;
    }
@endphp

<div
    class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-transparent bg-[var(--second-table-color)] px-2.5 py-2 text-center ring-2 shadow-lg transition-all {{ $glowClasses }}">
    <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">
        Mutu
    </span>
    <span class="text-base font-bold leading-none text-[var(--contrast-main-text)]">
        {{ $value }}
    </span>
</div>
