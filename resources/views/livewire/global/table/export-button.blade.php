@php
    $breakpoint = $autoSmall ?? null;
    $manualSmall = $isSmall ?? false;

    // kapan teks muncul
    $autoSmallClass = match ($breakpoint) {
        'sm' => 'hidden sm:inline',
        'md' => 'hidden md:inline',
        'lg' => 'hidden lg:inline',
        'xl' => 'hidden xl:inline',
        '2xl' => 'hidden 2xl:inline',
        default => '',
    };

    // padding horizontal tombol
    $buttonWidthClass = match ($breakpoint) {
        'sm' => 'px-2 sm:px-3',
        'md' => 'px-2 md:px-3',
        'lg' => 'px-2 lg:px-3',
        'xl' => 'px-2 xl:px-3',
        '2xl' => 'px-2 2xl:px-3',
        default => 'px-' . ($valuePx ?? '3'),
    };

    $colorClass = match ($color ?? '') {
        'red' => 'border-red-200 transition-colors !text-red-600 dark:!text-red-400',
        'rose' => 'border-rose-200 transition-colors !text-rose-600 dark:!text-rose-400',
        'blue' => 'border-blue-200 transition-colors !text-blue-600 dark:!text-blue-400',
        'cyan' => 'border-cyan-200 transition-colors !text-cyan-600 dark:!text-cyan-400',
        'green' => 'border-green-200 transition-colors !text-green-600 dark:!text-green-400',
        'primary' => 'border-primary-200 transition-colors !text-primary-600 dark:!text-primary-400',
        default => 'border-emerald-200 transition-colors !text-emerald-600 dark:!text-emerald-400',
    };

    $colorClassFull = match ($color ?? '') {
        'red' => '!bg-red-50 hover:!bg-red-100 dark:!bg-red-950/20 dark:hover:!bg-red-900/30',
        'rose' => '!bg-rose-50 hover:!bg-rose-100 dark:!bg-rose-950/20 dark:hover:!bg-rose-900/30',
        'blue' => '!bg-blue-50 hover:!bg-blue-100 dark:!bg-blue-950/20 dark:hover:!bg-blue-900/30',
        'cyan' => '!bg-cyan-50 hover:!bg-cyan-100 dark:!bg-cyan-950/20 dark:hover:!bg-cyan-900/30',
        'green' => '!bg-green-50 hover:!bg-green-100 dark:!bg-green-950/20 dark:hover:!bg-green-900/30',
        'primary' => '!bg-primary-50 hover:!bg-primary-100 dark:!bg-primary-950/20 dark:hover:!bg-primary-900/30',
        default => '!bg-emerald-50 hover:!bg-emerald-100 dark:!bg-emerald-950/20 dark:hover:!bg-emerald-900/30',
    };

    $colorClassActive = match ($color ?? '') {
        'red' => '!bg-red-100 dark:!bg-red-900/40 ring-2 ring-red-400/30',
        'rose' => '!bg-rose-100 dark:!bg-rose-900/40 ring-2 ring-rose-400/30',
        'blue' => '!bg-blue-100 dark:!bg-blue-900/40 ring-2 ring-blue-400/30',
        'cyan' => '!bg-cyan-100 dark:!bg-cyan-900/40 ring-2 ring-cyan-400/30',
        'green' => '!bg-green-100 dark:!bg-green-900/40 ring-2 ring-green-400/30',
        'primary' => '!bg-primary-100 dark:!bg-primary-900/40 ring-2 ring-primary-400/30',
        default => '!bg-emerald-100 dark:!bg-emerald-900/40 ring-2 ring-emerald-400/30',
    };

    if ($manualSmall) {
        $buttonWidthClass = 'px-2';
    }
    $name = $nameXString ?? 'Export Excel';
    $full = $isFull ?? false;
    $text = $isTextMd ?? false;
    $noPb = $isNoPb ?? $full ?? false;
@endphp

<div class="flex justify-end md:order-2 {{ !($noPb ?? false) ? 'pb-3' : '' }}" x-data="{
    confirmExport: false,
    timeout: null,

    handleClick() {
        if (this.confirmExport) {
            $wire.{{ $xClick ?? $xString }};
            {{-- $wire.printPDFRPS(\$store.{{ $alpineKey ?? 'rps?.rps_id_show' }} ?? null) --}}
            {{-- $wire.printPDFRPS($store.{{ $alpineKey ?? 'rps?.rps_id_show' }} ?? null) --}}

            this.confirmExport = false;
            clearTimeout(this.timeout);
            return;
        }

        this.confirmExport = true;

        this.timeout = setTimeout(() => {
            this.confirmExport = false;
        }, 3000);
    }
}">
    <flux:button @click="handleClick" size="sm" :icon="$full ? 'printer' : null"
        class="cursor-pointer h-8
    {{ !$text ? '!text-xs' : '' }}
    {{ $full ? $colorClassFull : '' }}
    {{ $colorClass }} border transition-colors transition-all duration-200 ease-in-out {{ $buttonWidthClass }}"
        x-bind:class="confirmExport
            ?
            '{{ $colorClassActive }}' :
            ''">
        <div class="flex items-center">
            @if (!$full)
                <flux:icon name="printer" @class(['h-3.5 w-3.5', 'mr-2' => !$manualSmall]) />
            @endif

            <div class="relative inline-flex justify-center items-center">
                @if (!$manualSmall)
                    <span wire:loading.remove wire:target="{{ $xString }}" class="{{ $autoSmallClass }}">
                        {{ $name }}
                    </span>
                @endif
                <span wire:loading wire:target="{{ $xString }}"
                    class="invisible {{ $autoSmallClass }} whitespace-nowrap">
                    {{ $name }}
                </span>
                <span wire:loading wire:target="{{ $xString }}"
                    class="absolute flex justify-center items-center pointer-events-none">
                    <flux:icon name="arrow-path" class="animate-spin h-3.5 w-3.5 text-current" />
                </span>

            </div>
        </div>
    </flux:button>
</div>
