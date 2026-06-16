@php
    $breakpoint = $autoSmall ?? null;
    $manualSmall = $isSmall ?? false;
    $alpineStore = $alpine ?? false;

    // ===== small mode state =====
    $isResponsiveSmall = match ($breakpoint) {
        'sm' => 'sm',
        'md' => 'md',
        'lg' => 'lg',
        'xl' => 'xl',
        '2xl' => '2xl',
        default => null,
    };

    // ===== label auto-small =====
    $autoSmallClass = match ($breakpoint) {
        'sm' => 'sm:inline hidden',
        'md' => 'md:inline hidden',
        'lg' => 'lg:inline hidden',
        'xl' => 'xl:inline hidden',
        '2xl' => '2xl:inline hidden',
        default => '',
    };

    // ===== padding =====
    $withBValue = $withBValue ?? 4;
    $withTValue = $withTValue ?? 0;

    $autoBSmall = $autoBSmall ?? $withBValue;
    $autoTSmall = $autoTSmall ?? $withTValue;

    $pbClass = match ($breakpoint) {
        'sm' => "pb-{$autoBSmall} sm:pb-{$withBValue}",
        'md' => "pb-{$autoBSmall} md:pb-{$withBValue}",
        'lg' => "pb-{$autoBSmall} lg:pb-{$withBValue}",
        'xl' => "pb-{$autoBSmall} xl:pb-{$withBValue}",
        '2xl' => "pb-{$autoBSmall} 2xl:pb-{$withBValue}",
        default => "pb-{$withBValue}",
    };

    $ptClass = match ($breakpoint) {
        'sm' => "pt-{$autoTSmall} sm:pt-{$withTValue}",
        'md' => "pt-{$autoTSmall} md:pt-{$withTValue}",
        'lg' => "pt-{$autoTSmall} lg:pt-{$withTValue}",
        'xl' => "pt-{$autoTSmall} xl:pt-{$withTValue}",
        '2xl' => "pt-{$autoTSmall} 2xl:pt-{$withTValue}",
        default => "pt-{$withTValue}",
    };

    if ($manualSmall) {
        $pbClass = "pb-{$autoBSmall}";
        $ptClass = "pt-{$autoTSmall}";
    }

    $perPageOptions = $perPageOptions ?? [6, 12, 24, 48];
@endphp

<div wire:key="{{ $key ?? 'page-control-default' }}" @class([
    'flex items-center justify-end',
    $pbClass => $withB ?? true,
    $ptClass => $withT ?? ($withTValue ?? false),
])>
    {{-- 
        KONDISIONAL REAKTIF KEDUA:
        Menggunakan Getter dan Setter untuk mengikat (bind) properti 'selected' 
        langsung ke Alpine Store secara real-time dan global tanpa mengandalkan urutan DOM parent.
    --}}
    <div x-data="{ 
            open: false, 
            @if($alpineStore)
                get selected() { return this.$store.{{ $alpineStore }}.perPage },
                set selected(val) { this.$store.{{ $alpineStore }}.perPage = val }
            @else
                selected: @entangle('perPage').live
            @endif
         }" 
         @class([
            'relative',
            'w-14' => $manualSmall,
            'w-16' => !$manualSmall && !$breakpoint,
            'w-14 sm:w-16' => $breakpoint === 'sm' && !$manualSmall,
            'w-14 md:w-16' => $breakpoint === 'md' && !$manualSmall,
            'w-14 lg:w-16' => $breakpoint === 'lg' && !$manualSmall,
            'w-14 xl:w-16' => $breakpoint === 'xl' && !$manualSmall,
            'w-14 2xl:w-16' => $breakpoint === '2xl' && !$manualSmall,
         ]) 
         @click.away="open = false">
        
        {{-- Tombol utama --}}
        <button type="button" @click="open = !open"
            class="cursor-pointer flex items-center justify-between border rounded-md shadow-sm
                   bg-[var(--second-table-color)] table-border
                   text-[var(--contrast-second-text)]
                   py-1 px-2 text-sm w-full
                   hover:border-[var(--hover-focus-color)]
                   transition-[border-color] duration-200">
            <span x-text="selected"></span>

            <svg
                @class([
                    'h-4 w-4 ml-1 text-gray-400 dark:text-gray-500',
                    'hidden' => $manualSmall && !($withArr ?? false),
                    'hidden sm:block' => $breakpoint === 'sm' && !$manualSmall && !($withArr ?? false),
                    'hidden md:block' => $breakpoint === 'md' && !$manualSmall && !($withArr ?? false),
                    'hidden lg:block' => $breakpoint === 'lg' && !$manualSmall && !($withArr ?? false),
                    'hidden xl:block' => $breakpoint === 'xl' && !$manualSmall && !($withArr ?? false),
                    'hidden 2xl:block' => $breakpoint === '2xl' && !$manualSmall && !($withArr ?? false),
                ])
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- dropdown --}}
        <ul x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-h-40 overflow-y-auto scrollbar-tiny
                   bg-[var(--main-pop-up-color)]
                   ring-[var(--focus-color)]
                   absolute z-100 mt-1 w-full rounded-md shadow-lg
                   ring-1 ring-opacity-5 focus:outline-none overflow-hidden">
            @foreach ($perPageOptions as $option)
                <li wire:key="perPage-{{ $option }}" @click="selected = {{ $option }}; open = false"
                    class="block px-3 py-1 text-sm cursor-pointer transition-colors duration-200
                           hover:bg-[var(--hover-main-color)]
                           hover:text-[var(--main-text)]"
                    :class="{
                        'bg-[var(--main-color)] text-[var(--main-text)] font-semibold': selected == {{ $option }}
                    }">
                    {{ $option }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Label "Baris" --}}
    @if (!$manualSmall)
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-2 {{ $autoSmallClass }}">
            Baris
        </span>
    @endif
</div>