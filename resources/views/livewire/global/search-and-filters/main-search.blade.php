@php
    $isLive = $isLive ?? false;
    $alpineStore = $alpine ?? false;
@endphp

{{-- x-model="$store.sesi.search" --}}

<div x-data="{
    search: @entangle('search'){{ $isLive ? '.live' : '' }},
    isRealtime: false,
    clickTimer: null,
    lastClick: 0,

    updatedSearch(val) {
        if (this.isRealtime) {
            $wire.$set('search', val);
        }
    },

    handleAction() {
        let currentTime = new Date().getTime();
        let gap = currentTime - this.lastClick;

        if (gap < 250) {
            if (this.clickTimer) {
                clearTimeout(this.clickTimer);
                this.clickTimer = null;
            }
            this.isRealtime = !this.isRealtime;

            if (this.isRealtime) {
                $wire.$set('search', this.search);
            }
        } else {
            this.clickTimer = setTimeout(() => {
                if (!this.isRealtime) {
                    $wire.$set('search', this.search);
                    {{-- $wire.search(); --}}
                }
                this.clickTimer = null;
            }, 250);
        }

        this.lastClick = currentTime;
    }
}" class="relative flex items-center">

    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini"
            ::class="isRealtime ? 'text-[var(--focus-color)]' : 'text-[var(--contrast-second-text)]'" />
    </div>

    <input
        @if ($alpineStore) x-model="$store.{{ $alpineStore }}.search" 
        @else
        @if ($isLive) 
            x-model.debounce.{{ $isBounce ?? '300ms' }}="search" 
        @else 
            x-model="search"
            @input.debounce.300ms="updatedSearch($el.value)"
            @keydown.enter="$wire.$set('search', search); $wire.search()" @endif
        @endif
    type="text"
    placeholder="{{ $placeholder ?? 'Cari data...' }}"
    class="w-full h-10 pl-10 {{ !$isLive ? 'pr-26' : 'pr-10' }} rounded-lg shadow-sm
               bg-[var(--second-table-color)] border-{{ $isBorder ?? 0 ?: 0 }} border-[var(--border-table-color)] text-[var(--contrast-main-text)]"
    />

    <div class="absolute inset-y-0 right-0 flex items-center pr-1 gap-1">
        @if ($alpineStore)
            @include('livewire.global.search-and-filters.partial.reset-button', [
                'alpine' => $alpineStore,
                'xShow' => "\$store.{$alpineStore}.search.length > 0",
                'xClick' => "\$store.{$alpineStore}.search = ''",
                'isRelative' => true,
            ])
        @else
            @include('livewire.global.search-and-filters.partial.reset-button', [
                'xShow' => 'search.length > 0',
                'xClick' => "search = ''",
                'xWire' => 'resetInputFilter()',
                'isRelative' => true,
            ])
        @endif

        @if (!$isLive)
            <button type="button" @click="handleAction" wire:loading.attr="disabled"
                :class="{
                    'cursor-pointer h-8 px-5 rounded-md flex items-center shadow-sm transition-all duration-200 select-none': true,
                    'bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] text-white': !isRealtime,
                    'bg-[var(--hover-focus-color)] text-white ring-2 ring-white/10': isRealtime
                }">

                <div x-show="!isRealtime" class="flex items-center">
                    <flux:icon.magnifying-glass class="h-4 w-4" variant="mini" wire:loading.remove
                        wire:target="search" />
                    <flux:icon name="arrow-path" class="animate-spin h-4 w-4" wire:loading wire:target="search" />
                </div>

                <div x-show="isRealtime" class="flex items-center">
                    <span class="relative flex h-2 w-2 m-1">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                </div>
            </button>
        @endif
    </div>
</div>
