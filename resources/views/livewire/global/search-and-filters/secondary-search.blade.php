<div>
    <div x-data="{
        open: false,
        localSearch: @entangle($xSearchQueryString).defer,
        align: 'left',
        checkPosition() {
            const rect = this.$refs.dropdown.getBoundingClientRect();
            if (window.innerWidth - rect.left < 800) {
                this.align = 'right';
            } else {
                this.align = 'left';
            }
        },
        init() {
            if (typeof this.localSearch !== 'string') {
                this.localSearch = '';
            }
        },
    }" wire:key="secondary-search-{{ $inputXFilterString }}" class="relative w-full sm:flex-1">

        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <x-dynamic-component :component="'flux::icon.' . $iconString" variant="mini" class="text-[var(--contrast-second-text)]" />
            </div>

            {{-- INPUT --}}
            <input type="text" x-model="localSearch" placeholder="{{ $placeholderString }}"
                @focus="
                    open = true; 
                    $event.target.select();
                    $wire.{{ $inputXFilterString }}(); 
                    $nextTick(() => checkPosition());
                "
                @input.debounce.300ms="
                    open = true;
                    $wire.set('{{ $xSearchQueryString }}', localSearch);
                    $wire.{{ $inputXFilterString }}(); 
                "
                @click.outside="open = false" @keydown.escape.window="open = false"
                class="text-xs sm:text-sm focus:ring-2 focus:ring-[var(--focus-color)] outline-none w-full h-10 pl-10 px-4 pr-10 rounded-lg shadow-sm
                        placeholder-shown:pr-2 bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
                "
                autocomplete="off" />

            @include('livewire.global.search-and-filters.partial.reset-button', [
                'xShow' => "typeof localSearch === 'string' && localSearch.length > 0",
                'xClick' => "localSearch = ''",
                'xWire' => $resetXFilter,
            ])
        </div>


        {{-- DROPDOWN --}}
       <div x-show="open" x-cloak 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" 
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" 
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-ref="dropdown"
            :class="align === 'right' ? 'right-0' : 'left-0'"
            class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute z-[100] w-full {{ $minW ?? 'sm:min-w-[450px]' }} mt-1 rounded-lg shadow-xl {{ $maxH ?? 'max-h-80' }} overflow-y-auto">
            @forelse ($xSearchResults as $x)
                <div wire:key="x-{{ $x['id'] }}"
                    @click="
                    localSearch = '{{ $x[$typeXString] }}';
                    open = false;
                    $wire.{{ $selectXForFilterString }}({{ $x['id'] }});
                "
                    class="px-4 py-2 cursor-pointer transition-colors duration-200
                        bg-[var(--main-pop-up-color)] border-[var(--focus-color)]
                        hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90 hover:text-[var(--main-text)] active:text-[var(--main-text)]/90
                        text-xs sm:text-sm">
                        <div class="flex flex-wrap items-start gap-x-4 gap-y-1">
                            
                            <div class="flex-1 min-w-[200px]">
                                <div class="text-[var(--contrast-main-text)] font-medium break-words">
                                    {{ $x[$typeXString] }}
                                </div>

                                <div class="flex flex-wrap items-center text-xs sm:text-sm">
                                    <div class="whitespace-nowrap text-[var(--hover-focus-color)]">
                                        <span class="font-bold">- ID: {{ $x['id'] }}</span>
                                    </div>

                                    @if ($typeX2String ?? null)
                                        <div class="inline-flex items-center whitespace-nowrap text-[var(--contrast-second-text)]">
                                            <span class="mx-2">|</span>
                                            <span>{{ $x[$typeX2String] }}</span>
                                        </div>
                                    @endif

                                    @if ($typeX3String ?? null)
                                        <div class="inline-flex items-center whitespace-nowrap text-[var(--contrast-second-text)]">
                                            <span class="mx-2">|</span>
                                            <span>{{ $x[$typeX3String] }}</span>
                                        </div>
                                    @endif

                                    @if ($typeX4String ?? null)
                                        <div class="inline-flex items-center whitespace-nowrap text-[var(--contrast-second-text)]">
                                            <span class="mx-2">|</span>
                                            <span>{{ $x[$typeX4String] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="shrink-0">
                                <span class="my-2 inline-block bg-[var(--focus-color)] text-[var(--main-text)] text-[9px] sm:text-xs px-2 py-1 rounded-md">
                                    @if ($typeKodeString ?? null)
                                        {{ $x[$typeKodeString] }}
                                    @else
                                        {{ filled($x['kode']) ? $x['kode'] : 'UNI' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                </div>
            @empty
                <div class="py-4">
                    <div wire:loading.remove target="{{ $inputXFilterString }}"
                        class="text-[var(--contrast-second-text)] italic text-xs text-center">
                        {{ $unfoundString }}
                    </div>

                    <div wire:loading flex target="{{ $inputXFilterString }}"
                        class="text-[var(--hover-focus-color)] w-full flex-col items-center justify-center gap-2">

                        <div class="flex justify-center">
                            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        <span class="block text-xs mt-1 text-center italic">
                            Menyaring data...
                        </span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
