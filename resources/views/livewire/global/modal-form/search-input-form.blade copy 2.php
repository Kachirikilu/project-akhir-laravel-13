<div class="relative" wire:key="input-array.search-input-form-{{ $typeXString }}-{{ $selectX }}-{{ $alpine }}"
    x-data="{
        open: false,
        search: @entangle($nameSearchString).live,
        items: @entangle($idString).live,
        itemsAll: @entangle($itemsAllString).live,
        itemsAllDisplay: null,
        isManual: false
    }"
    x-effect="
    const config = $store.{{ $alpine ?? 'config' }};
    
    if (config?.isEdit === 0) {
        search = '';
        items = null;
        itemsAll = null;
        itemsAllDisplay = null;
    } else {
        let currentId = config?.['{{ $idString }}'];

        if (!currentId) {
            search = '';
            items = null;
            itemsAll = null;
            itemsAllDisplay = null;
        } else {
            search = config?.['{{ $modelString }}'];
            items =  config?.['{{ $idString }}'];
            itemsAll = config?.['{{ $itemsAllString }}'];
            itemsAllDisplay = config?.['{{ $itemsAllString }}'];
        }
    }
">
    @include('livewire.global.modal-form.partial.label')

    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input x-model="search" autocomplete="off" type="text"
            @focus="
                open = true; 
                $event.target.select();
                $wire.{{ $fetchString }}(null, 'single'); 
            "
            @input.debounce.300ms="
                open = true;
                $wire.{{ $fetchString }}(search, 'single'); 
            "
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
            placeholder="Cari nama {{ $nameXString ?? ucfirst($modelString) }}..."
            class="bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
                {{-- placeholder-[var(--contrast-third-text)] --}}
            w-full border rounded-lg pl-10 px-3 py-2 pr-10">

        {{-- Tombol Reset --}}
        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'search',
            'xClick' => "search = ''; items = null; itemsAll = null; itemsAllDisplay = null",
            'xWire' => $resetXInput,
            'xWire2' => $fetchString . "(null, 'single')",
            'xAlpine1' => $idString,
            'xAlpine2' => $itemsAllString,
        ])
    </div>

    {{-- Info Terpilih --}}
    <div x-show="itemsAllDisplay && search" x-cloak>
        <p class="text-[var(--focus-color)] text-xs mt-1 font-medium italic">
            Terpilih:
            <span x-text="itemsAllDisplay?.slot1" class="ml-1"></span>
            <span class="mx-1">|</span>
            Kode: <span x-text="itemsAllDisplay?.kode"></span>

            @if ($typeX2String ?? null)
                <span class="mx-1">|</span>
                <span x-text="itemsAllDisplay?.slot2"></span>
            @endif
            @if ($typeX3String ?? null)
                <span class="mx-1">|</span>
                <span x-text="itemsAllDisplay?.slot3"></span>
            @endif
            <span class="mx-1">|</span>
            ID: <span x-text="itemsAllDisplay?.id"></span>
        </p>
    </div>

    {{-- DROPDOWN HASIL --}}
    <div x-show="open" x-cloak {{-- x-collapse.duration.300ms --}} x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-80 overflow-y-auto custom-scrollbar">

        @forelse ($xResults as $x)
            <div wire:key="{{ $x[$typeXString] }}-{{ $x['id'] }}"
                @click="
                    let itemId = {{ $x['id'] }};
                    let newSearch = '{{ $x[$typeXString] }}';
                    let newKode = '{{ filled($x['kode']) ? $x['kode'] : 'UNI' }}';

                    search = newSearch;
                    items = itemId;
                    itemsAll = { 
                        id: itemId,
                        kode: newKode,
                        slot1: '{{ $x[$typeXString] ?? '' }}',
                        slot2: '{{ isset($typeX2String) ? $x[$typeX2String] ?? '' : '' }}',
                        slot3: '{{ isset($typeX3String) ? $x[$typeX3String] ?? '' : '' }}'
                    };
                    itemsAllDisplay = itemsAll;
                    isManual = true;

                    $store.{{ $alpine ?? 'config' }}['{{ $idString }}'] = items;
                    $store.{{ $alpine ?? 'config' }}['{{ $itemsAllString }}'] = itemsAll;
                    $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = newSearch;

                    open = false;

                    $wire.{{ $selectX }}(itemId, newSearch)
                "
                class="px-4 py-2 cursor-pointer transition-colors duration-200
                bg-[var(--main-pop-up-color)] border-[var(--focus-color)]
                hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90
                {{-- border-b last:border-none  --}}
                text-sm">

                <div class="flex justify-between items-center">
                    @include('livewire.global.modal-form.input-array.partial.dropdown-items')
                    <span class="bg-[var(--focus-color)] text-[var(--main-text)] text-xs px-2 py-1 rounded-md ml-2">
                        {{ filled($x['kode']) ? $x['kode'] : 'UNI' }}
                    </span>
                </div>


            </div>
        @empty
            <div class="p-4 text-center">
                <div wire:loading @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                    <p class="text-sm text-[var(--focus-color)] font-medium animate-pulse">
                        Sedang mencari data {{ $nameXString ?? null }}...
                    </p>
                </div>

                <div wire:loading.remove @if ($wireLoading ?? null) wire:target="{{ $wireLoading }}" @endif>
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                        Data {{ $nameXString ?? null }} tidak ditemukan!
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    @error($idString)
        <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $message }}</span>
    @enderror
</div>