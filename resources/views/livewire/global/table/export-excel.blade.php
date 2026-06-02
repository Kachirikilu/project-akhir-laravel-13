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
        default => 'px-'.($valuePx ?? '3'),
    };

    if ($manualSmall) {
        $buttonWidthClass = 'px-2';
    }
@endphp

<div
    class="flex justify-end md:order-2 {{ !($isNoPb ?? false) ? 'pb-3' : '' }}"

    x-data="{
        confirmExport: false,
        timeout: null,

        handleClick() {
            if (this.confirmExport) {
                $wire.{{ $xString }}();
                this.confirmExport = false;
                clearTimeout(this.timeout);
                return;
            }

            this.confirmExport = true;

            this.timeout = setTimeout(() => {
                this.confirmExport = false;
            }, 3000);
        }
    }"
>
    <flux:button
        @click="handleClick"
        size="sm"

        class="cursor-pointer h-8 !text-xs border border-emerald-200 transition-colors !text-emerald-600 dark:!text-emerald-400 transition-all duration-200 ease-in-out {{ $buttonWidthClass }}"

        x-bind:class="
            confirmExport
                ? '!bg-emerald-100 dark:!bg-emerald-900/30'
                : 'hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30'
        "
    >
        <div class="flex items-center">
            <flux:icon
                name="printer"
                @class([
                    'h-3.5 w-3.5',
                    'mr-2' => !$manualSmall,
                ])
            />

            {{-- hidden saat small --}}
            @if (!$manualSmall)
                <span
                    x-bind:class="confirmExport ? 'font-bold' : 'font-medium'"
                    class="{{ $autoSmallClass }}"
                >
                    {{ $nameXString ?? 'Export Excel' }}
                </span>
            @endif
        </div>

        <flux:icon
            wire:loading
            wire:target="{{ $xString }}"
            name="arrow-path"
            class="animate-spin h-3.5 w-3.5 ml-2 dark:!text-emerald-600"
        />
    </flux:button>
</div>