@php
    $pointAutoSmall = $autoSmall ?? null;
    $manualSmall = $isSmall ?? false;
    $countClick = max(1, (int) ($countClick ?? 2));

    // kapan teks muncul
    $autoSmallClass = match ($pointAutoSmall) {
        'sm' => 'hidden sm:inline',
        'md' => 'hidden md:inline',
        'lg' => 'hidden lg:inline',
        'xl' => 'hidden xl:inline',
        '2xl' => 'hidden 2xl:inline',
        default => '',
    };

    // padding horizontal tombol
    $buttonWidthClass = match ($pointAutoSmall) {
        'sm' => 'px-2 sm:px-3',
        'md' => 'px-2 md:px-3',
        'lg' => 'px-2 lg:px-3',
        'xl' => 'px-2 xl:px-3',
        '2xl' => 'px-2 2xl:px-3',
        default => $valuePx ?? 'px-3',
    };

    $colorClass = match ($color ?? '') {
        'red' => '!border-red-200 dark:!border-red-800/50 transition-colors !text-red-600 dark:!text-red-400',
        'rose' => '!border-rose-200 dark:!border-rose-800/50 transition-colors !text-rose-600 dark:!text-rose-400',
        'blue' => '!border-blue-200 dark:!border-blue-800/50 transition-colors !text-blue-600 dark:!text-blue-400',
        'cyan' => '!border-cyan-200 dark:!border-cyan-800/50 transition-colors !text-cyan-600 dark:!text-cyan-400',
        'green' => '!border-green-200 dark:!border-green-800/50 transition-colors !text-green-600 dark:!text-green-400',
        'yellow' => '!border-yellow-200 dark:!border-yellow-800/50 transition-colors !text-yellow-600 dark:!text-yellow-400',
        'primary' => '!border-primary-200 dark:!border-primary-800/50 transition-colors !text-primary-600 dark:!text-primary-400',
        default => '!border-emerald-200 dark:!border-emerald-800/50 transition-colors !text-emerald-600 dark:!text-emerald-400',
    };
    $colorClassFull = match ($color ?? '') {
        'red' => '!border-red-200 dark:!border-red-800/50 !bg-red-50 hover:!bg-red-100 active:!bg-red-100 dark:!bg-red-950/20 dark:hover:!bg-red-900/30 dark:active:!bg-red-900/40',
        'rose' => '!border-rose-200 dark:!border-rose-800/50 !bg-rose-50 hover:!bg-rose-100 active:!bg-rose-100 dark:!bg-rose-950/20 dark:hover:!bg-rose-900/30 dark:active:!bg-rose-900/40',
        'blue' => '!border-blue-200 dark:!border-blue-800/50 !bg-blue-50 hover:!bg-blue-100 active:!bg-blue-100 dark:!bg-blue-950/20 dark:hover:!bg-blue-900/30 dark:active:!bg-blue-900/40',
        'cyan' => '!border-cyan-200 dark:!border-cyan-800/50 !bg-cyan-50 hover:!bg-cyan-100 active:!bg-cyan-200 active:!bg-cyan-100 dark:!bg-cyan-950/20 dark:hover:!bg-cyan-900/30 dark:active:!bg-cyan-900 dark:active:!bg-cyan-900/40',
        'green' => '!border-green-200 dark:!border-green-800/50 !bg-green-50 hover:!bg-green-100 active:!bg-green-100 dark:!bg-green-950/20 dark:hover:!bg-green-900/30 dark:active:!bg-green-900/40',
        'yellow' => '!border-yellow-200 dark:!border-yellow-800/50 !bg-yellow-50 hover:!bg-yellow-100 active:!bg-yellow-100 dark:!bg-yellow-950/20 dark:hover:!bg-yellow-900/30 dark:active:!bg-yellow-900/40',
        'primary' => '!border-primary-200 dark:!border-primary-800/50 !bg-primary-50 hover:!bg-primary-100 active:!bg-primary-100 dark:!bg-primary-950/20 dark:hover:!bg-primary-900/30 dark:active:!bg-primary-900/40',
        default => '!border-emerald-200 dark:!border-emerald-800/50 !bg-emerald-50 hover:!bg-emerald-100 active:!bg-emerald-100 dark:!bg-emerald-950/20 dark:hover:!bg-emerald-900/30 dark:active:!bg-emerald-900/40',
    };

    $colorClassActive = match ($color ?? '') {
        'red' => '!border-red-300 dark:!border-red-700 !bg-red-100 dark:!bg-red-900/40 ring-2 ring-red-400/30',
        'rose' => '!border-rose-300 dark:!border-rose-700 !bg-rose-100 dark:!bg-rose-900/40 ring-2 ring-rose-400/30',
        'blue' => '!border-blue-300 dark:!border-blue-700 !bg-blue-100 dark:!bg-blue-900/40 ring-2 ring-blue-400/30',
        'cyan' => '!border-cyan-300 dark:!border-cyan-700 !bg-cyan-100 dark:!bg-cyan-900/40 ring-2 ring-cyan-400/30',
        'green' => '!border-green-300 dark:!border-green-700 !bg-green-100 dark:!bg-green-900/40 ring-2 ring-green-400/30',
        'yellow' => '!border-yellow-300 dark:!border-yellow-700 !bg-yellow-100 dark:!bg-yellow-900/40 ring-2 ring-yellow-400/30',
        'primary' => '!border-primary-300 dark:!border-primary-700 !bg-primary-100 dark:!bg-primary-900/40 ring-2 ring-primary-400/30',
        default => '!border-emerald-300 dark:!border-emerald-700 !bg-emerald-100 dark:!bg-emerald-900/40 ring-2 ring-emerald-400/30',
    };

    $iconMarginClass = match ($pointAutoSmall) {
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

    // Label teks konfirmasi dinamis berdasarkan $countClick
    $confirmLabel = match (true) {
        $countClick === 2 => 'Double click...',
        $countClick === 3 => 'Triple click...',
        $countClick >= 4 => $countClick . 'x click...',
        default => $name,
    };
@endphp

<div class="flex justify-end md:order-2 {{ !($noPb ?? false) ? 'pb-3' : '' }}" x-data="{
    clicks: 0,
    maxClicks: {{ $countClick }},
    timeout: null,

    handleClick() {
        this.clicks++;

        if (this.clicks >= this.maxClicks) {
            $wire.{{ $xClick ?? $xString }};

            this.clicks = 0;
            clearTimeout(this.timeout);
            return;
        }

        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.clicks = 0;
        }, 3000);
    }
}">
    <flux:button @click="handleClick" size="sm"
        title="Klik {{ $countClick > 1 ? $countClick . 'x' : '' }} untuk {{ $name }}"
        class="cursor-pointer h-8
    {{ !$text ? '!text-xs' : '' }}
    {{ $full ? $colorClassFull : '' }}
    {{ $colorClass }} border transition-colors transition-all duration-200 ease-in-out {{ $valuePy ?? 'py-4.5' }} {{ $buttonWidthClass }}"
        x-bind:class="clicks > 0 ? '{{ $colorClassActive }}' : ''">
        <div class="flex items-center">
            @if ($isLoading ?? false)
                <flux:icon name="arrow-path" class="animate-spin h-3.5 w-3.5 text-current" />
            @else
                <flux:icon name="{{ $icon ?? 'printer' }}" variant="{{ $full ? 'solid' : 'outline' }}"
                    class="h-3.5 w-3.5" />
            @endif

            <div class="relative inline-flex justify-center items-center">
                @if (!$manualSmall)
                    <span wire:loading.remove wire:target="{{ $wireLoading ?? $xString }}"
                        class="ml-2 {{ $autoSmallClass }}"
                        x-text="clicks > 0 ? '{{ $confirmLabel }}' : '{{ $name }}'">
                        {{ $name }}
                    </span>
                @endif
                <span wire:loading wire:target="{{ $wireLoading ?? $xString }}"
                    class="invisible {{ $autoSmallClass }} whitespace-nowrap">
                    {{ $name }}
                </span>
                <span wire:loading wire:target="{{ $wireLoading ?? $xString }}"
                    class="absolute flex justify-center items-center pointer-events-none">
                    <flux:icon name="arrow-path" class="animate-spin h-3.5 w-3.5 text-current" />
                </span>
            </div>
        </div>
    </flux:button>
</div>