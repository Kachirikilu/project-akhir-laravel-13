@php
    $isLive = $isLive ?? false;
    $defaultLive = $defaultLive ?? true;
    $alpineStore = $alpine ?? false;
    $isMode = $isMode ?? true;
    $isBounce = $isBounce ?? '300ms';

    // Pastikan searchValues ada isinya
    $searchValues = $searchValues ?? [];

    // Jika searchValues tidak kosong, otomatis isi label dan deskripsi default-nya
    if (!empty($searchValues)) {
        $searchOptions = $searchOptions ?? ['Pencarian Sederhana', 'Pencarian Kompleks'];
        $searchDescs = $searchDescs ?? [
            'Mencari data utama secara instan.',
            'Mencari ke seluruh kolom dan relasi data secara detail.',
        ];
    } else {
        $searchOptions = [];
        $searchDescs = [];
    }
@endphp

<div x-data="{
    search: @entangle('search'),
    selectedMode: '{{ $searchMode ?? '' }}',

    isRealtime: {{ $isLive ? 'true' : ($defaultLive ? 'true' : 'false') }},
    showSearchModePopup: false,

    triggerRealtimeSearch(val) {
        $wire.$set('search', val);
        @if ($alpineStore) $store.{{ $alpineStore }}.search = val; @endif
    },

    triggerManualSearch() {
        if (!this.isRealtime) {
            $wire.$set('search', this.search);
            $wire.search();
        }
    },

    changeSearchMode(value) {
        this.selectedMode = value;
        $wire.$set('searchMode', value);
        this.showSearchModePopup = false;
    }
}" class="relative flex items-center">

    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini" ::class="isRealtime
                ? 'text-[var(--focus-color)]'
                : 'text-[var(--contrast-second-text)]'" />
    </div>

    <input type="text" placeholder="{{ $placeholder ?? 'Cari data...' }}"
        @if ($alpineStore) x-model="$store.{{ $alpineStore }}.search"
            @input.debounce.{{ $isBounce }}="if(isRealtime) triggerRealtimeSearch($el.value)"
        @else
            x-model="search"
            @input.debounce.{{ $isBounce }}="if(isRealtime) triggerRealtimeSearch($el.value)" @endif
        @dblclick="showSearchModePopup = true" @keydown.enter="triggerManualSearch()"
        class="
            focus:ring-2
            focus:ring-[var(--focus-color)]
            outline-none
            w-full
            h-10
            pl-10
            rounded-lg
            shadow-sm
            bg-[var(--second-table-color)]
            border-{{ $isBorder ?? 0 ?: 0 }}
            border-[var(--border-table-color)]
            text-[var(--contrast-main-text)]
        "
        @if ($alpineStore) :class="$store.{{ $alpineStore }}.search?.length > 0
            ? '{{ !$isLive && $isMode ? 'pr-26' : 'pr-10' }}'
            : 'pr-[64px]'"
    @else
        :class="search?.length > 0
            ? '{{ !$isLive && $isMode ? 'pr-26' : 'pr-10' }}'
            : 'pr-[64px]'" @endif />

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

        @if (!$isLive && $isMode)
            <button type="button" @contextmenu.prevent="showSearchModePopup = true" @click="triggerManualSearch()"
                @dblclick="showSearchModePopup = true" wire:loading.attr="disabled" class="bg-[var(--focus-color)]"
                :class="{
                    'cursor-pointer h-8 px-5 rounded-md flex items-center shadow-sm transition-all duration-200 select-none': true,
                    'hover:bg-[var(--hover-focus-color)] text-white': !isRealtime,
                    'text-white ring-2 ring-white/10': isRealtime
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

    @if (!$isLive)
        <div wire:ignore x-show="showSearchModePopup" x-cloak @click.outside="showSearchModePopup = false" x-transition
            class="absolute top-full right-0 mt-2 w-72 z-[100]
                bg-[var(--main-pop-up-color)]
                border border-[var(--focus-color)]
                rounded-lg shadow-xl overflow-hidden">

            {{-- REALTIME TOGGLE --}}
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-[var(--contrast-main-text)]">
                            Realtime Search
                        </div>
                        <div class="text-xs text-[var(--contrast-second-text)]">
                            Cari otomatis saat mengetik
                        </div>
                    </div>

                    <button type="button" @click="isRealtime = !isRealtime"
                        class="cursor-pointer relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        :class="isRealtime ? 'bg-[var(--focus-color)]' : 'bg-gray-500'">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                            :class="isRealtime ? 'translate-x-6' : 'translate-x-1'">
                        </span>
                    </button>
                </div>
            </div>

            {{-- LOOPING YANG SUDAH DIPERBAIKI --}}
            @if (!empty($searchValues))
                <div class="px-4 py-2 border-y border-[var(--contrast-second-text)]">
                    <div class="text-sm font-semibold text-[var(--contrast-main-text)]">
                        Mode Pencarian
                    </div>
                </div>

                @foreach ($searchValues as $index => $value)
                    @php
                        // Ambil label dari $searchOptions sesuai index, fallback ke value jika tidak diset
                        $label = $searchOptions[$index] ?? $value;
                        $desc = $searchDescs[$index] ?? null;
                    @endphp

                    <div @click="changeSearchMode('{{ $value }}')"
                        class="px-4 py-3 cursor-pointer transition-colors hover:bg-[var(--hover-pop-up-color)]">
                        <div class="flex items-center justify-between gap-4">

                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium text-[var(--contrast-main-text)]">
                                    {{ $label }}
                                </span>

                                @if ($desc)
                                    <span
                                        class="text-[10px] leading-normal text-[var(--contrast-second-text)] opacity-80 mt-0.5 break-words">
                                        {{ $desc }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-shrink-0">
                                <template x-if="selectedMode == '{{ $value }}'">
                                    <flux:icon.check class="w-4 h-4 text-[var(--focus-color)]" />
                                </template>
                            </div>

                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    @endif

</div>
