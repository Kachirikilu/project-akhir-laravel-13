<div x-data="{ search: @entangle('search').live }" class="relative">
    {{-- Icon Magnifying Glass --}}
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <flux:icon.magnifying-glass variant="mini" class="text-[var(--contrast-second-text)]" />
    </div>

    {{-- Input Search --}}
    <input x-model.debounce.300ms="search" type="text" name="search" placeholder="{{ $placeholder ?? 'Cari data...' }}"
        class="w-full h-10 pl-10 px-4 rounded-lg shadow-sm
               bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
                {{-- placeholder-[var(--contrast-third-text)]  --}}
                "/>

    {{-- Tombol Reset --}}
    @include('livewire.global.search-and-filters.partial.reset-button', [
        'xShow' => 'search.length > 0',
        'xClick' => "search = ''",
        'xWire' => 'resetInputFilter()'
    ])
</div>