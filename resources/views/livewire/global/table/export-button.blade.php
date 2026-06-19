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
        'red' => 'border-red-200 dark:border-red-800/50 transition-colors !text-red-600 dark:!text-red-400',
        'rose' => 'border-rose-200 dark:border-rose-800/50 transition-colors !text-rose-600 dark:!text-rose-400',
        'blue' => 'border-blue-200 dark:border-blue-800/50 transition-colors !text-blue-600 dark:!text-blue-400',
        'cyan' => 'border-cyan-200 dark:border-cyan-800/50 transition-colors !text-cyan-600 dark:!text-cyan-400',
        'green' => 'border-green-200 dark:border-green-800/50 transition-colors !text-green-600 dark:!text-green-400',
        'primary' => 'border-primary-200 dark:border-primary-800/50 transition-colors !text-primary-600 dark:!text-primary-400',
        default => 'border-emerald-200 dark:border-emerald-800/50 transition-colors !text-emerald-600 dark:!text-emerald-400',
    };
    $colorClassFull = match ($color ?? '') {
        'red' => 'border-red-200 dark:border-red-800/50 !bg-red-50 hover:!bg-red-100 active:!bg-red-100 dark:!bg-red-950/20 dark:hover:!bg-red-900/30 dark:active:!bg-red-900/40',
        'rose' => 'border-rose-200 dark:border-rose-800/50 !bg-rose-50 hover:!bg-rose-100 active:!bg-rose-100 dark:!bg-rose-950/20 dark:hover:!bg-rose-900/30 dark:active:!bg-rose-900/40',
        'blue' => 'border-blue-200 dark:border-blue-800/50 !bg-blue-50 hover:!bg-blue-100 active:!bg-blue-100 dark:!bg-blue-950/20 dark:hover:!bg-blue-900/30 dark:active:!bg-blue-900/40',
        'cyan' => 'border-cyan-200 dark:border-cyan-800/50 !bg-cyan-50 hover:!bg-cyan-100 active:!bg-cyan-200 active:!bg-cyan-100 dark:!bg-cyan-950/20 dark:hover:!bg-cyan-900/30 dark:active:!bg-cyan-900 dark:active:!bg-cyan-900/40',
        'green' => 'border-green-200 dark:border-green-800/50 !bg-green-50 hover:!bg-green-100 active:!bg-green-100 dark:!bg-green-950/20 dark:hover:!bg-green-900/30 dark:active:!bg-green-900/40',
        'primary' => 'border-primary-200 dark:border-primary-800/50 !bg-primary-50 hover:!bg-primary-100 active:!bg-primary-100 dark:!bg-primary-950/20 dark:hover:!bg-primary-900/30 dark:active:!bg-primary-900/40',
        default => 'border-emerald-200 dark:border-emerald-800/50 !bg-emerald-50 hover:!bg-emerald-100 active:!bg-emerald-100 dark:!bg-emerald-950/20 dark:hover:!bg-emerald-900/30 dark:active:!bg-emerald-900/40',
    };

    $colorClassActive = match ($color ?? '') {
        'red' => 'border-red-300 dark:border-red-700 !bg-red-100 dark:!bg-red-900/40 ring-2 ring-red-400/30',
        'rose' => 'border-rose-300 dark:border-rose-700 !bg-rose-100 dark:!bg-rose-900/40 ring-2 ring-rose-400/30',
        'blue' => 'border-blue-300 dark:border-blue-700 !bg-blue-100 dark:!bg-blue-900/40 ring-2 ring-blue-400/30',
        'cyan' => 'border-cyan-300 dark:border-cyan-700 !bg-cyan-100 dark:!bg-cyan-900/40 ring-2 ring-cyan-400/30',
        'green' => 'border-green-300 dark:border-green-700 !bg-green-100 dark:!bg-green-900/40 ring-2 ring-green-400/30',
        'primary' => 'border-primary-300 dark:border-primary-700 !bg-primary-100 dark:!bg-primary-900/40 ring-2 ring-primary-400/30',
        default => 'border-emerald-300 dark:border-emerald-700 !bg-emerald-100 dark:!bg-emerald-900/40 ring-2 ring-emerald-400/30',
    };

    $iconMarginClass = match ($breakpoint) {
        'sm' => 'hidden sm:mr-2',
        'md' => 'hidden md:mr-2',
        'lg' => 'hidden lg:mr-2',
        'xl' => 'hidden xl:mr-2',
        '2xl' => 'hidden 2xl:mr-2',
        default => !$manualSmall ? 'mr-2' : '',
    };

    if ($manualSmall) {
        $buttonWidthClass = 'px-2';
    }
    $name = $nameXString ?? 'Export Excel';
    $full = $isFull ?? false;
    $text = $isTextMd ?? false;
    $noPb = $isNoPb ?? ($full ?? false);
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
    <flux:button @click="handleClick" size="sm" :icon="$full ? $icon ?? 'printer' : null"
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
                <flux:icon name="{{ $icon ?? 'printer' }}" class="h-3.5 w-3.5" />
                {{-- <flux:icon name="{{ $icon ?? 'printer' }}" @class(['h-3.5 w-3.5', 'mr-2' => !$manualSmall && !$breakpoint]) /> --}}
            @endif

            <div class="relative inline-flex justify-center items-center">
                @if (!$manualSmall)
                    <span wire:loading.remove wire:target="{{ $xString }}" class="ml-2 {{ $autoSmallClass }}">
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
