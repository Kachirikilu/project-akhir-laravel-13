<div>
    <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            @if ($wireLoadingParent ?? null)
                <div wire:loading wire:target="{{ $wireLoadingParent }}">
                    <svg class="animate-spin h-4 w-4 text-[var(--focus-color)]" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            @endif
            <div @if ($wireLoadingParent ?? null) wire:loading.remove wire:target="{{ $wireLoadingParent }}" @endif>
                <flux:icon icon="{{ $iconString }}" variant="mini"
                    x-bind:class="isParentReady ? $store.{{ $alpine ?? 'config' }}?.colorIcon : 'text-gray-400'" />
            </div>
        </div>

        <input x-model="search" autocomplete="off" type="text" :disabled="!isParentReady" id="{{ $modelString }}"
            @if ($typeInput == 'single') @focus="
                if(isParentReady) {
                    open = true; 
                    $event.target.select();
                    $wire.{{ $fetchString }}('single'); 
                }
            "
            @else
                @focus="open = true; @isset($nameSearchString) $wire.set({{ json_encode($nameSearchString) }}, search); @endisset 
                $wire.{{ $fetchString }}('array');" 
            @endif
                @input.debounce.300ms="open = true;
                @isset($nameSearchString) $wire.set({{ json_encode($nameSearchString) }}, search); @endisset 
                "
                @click.outside="open = false"
                :placeholder="isParentReady ? 'Cari dan tambahkan {{ $nameXString ?? ucfirst($modelString) }}...' :
                    'Pilih {{ $nameXParent ?? 'Induk' }} terlebih dahulu...'"
                :class="!isParentReady ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' :
                    'bg-[var(--second-table-color)]'"
                class="placeholder-shown:pr-2 text-xs sm:text-sm focus:ring-2 focus:ring-[var(--focus-color)] outline-none table-border text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 py-2.5 transition-all">

        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''",
            'xWire' => $resetXInput ?? null,
            'xWire2' => $typeInput === 'single' && $fetchString ? $fetchString . '("single")' : null,
            'xAlpine1' => $idString ?? null,
            'xAlpine2' => $itemsAllString ?? null,
        ])
    </div>
</div>
