<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div x-show="activeTab === ''" x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="table-border flex items-end justify-between border-b mb-4 gap-4">
        <div class="min-w-0 flex-1 overflow-hidden">
            @include('livewire.global.search-and-filters.filter-mode', [
                'filterByFunc' => 'filterByStrata',
                'filterString' => 'filterPr',
                'totalTab' => $stats['prodi'] ?? null,
                'totalTab1' => $stats['sarjana'] ?? null,
                'totalTab2' => $stats['magister'] ?? null,
                'totalTab3' => $stats['doktor'] ?? null,
                'tab1String' => 'sarjana',
                'tab2String' => 'magister',
                'tab3String' => 'doktor',
                'tabName' => 'Semua Stara',
            ])
        </div>
        <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75],
                'key' => 'page-control-prodi',
                'autoSmall' => 'md',
            ])
        </div>
    </div>

    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 grid-rows-1 gap-2 items-center w-full z-20">
        <div x-show="activeTab === '' || activeTab === 'prodi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4" class="col-start-1 row-start-1 relative w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Program Studi, Departemen, atau Fakultas...',
                'searchValues' => ['simple', 'full'],
            ])
        </div>

        <div x-show="activeTab !== '' && activeTab !== 'prodi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="relative z-40 isolate col-start-1 row-start-1 grid grid-cols-1 grid-rows-1 relative w-full">

            {{-- Tab Departemen --}}
            <div x-show="activeTab === 'departemen'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 grid grid-cols-1 sm:grid-cols-9 gap-2 items-center">
                <div class="col-start-1 row-start-1 sm:col-span-8">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Departemen...',
                        'searchValues' => ['simple', 'full'],
                    ])
                </div>
                <div class="col-start-2 row-start-1 sm:col-span-1">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [3, 5, 8, 10, 15, 25, 50],
                        'key' => 'page-control-departemen',
                        'isSmall' => 1,
                        'withB' => 0,
                    ])
                </div>
            </div>

    

            {{-- Tab Fakultas --}}
            <div x-show="activeTab === 'fakultas'" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                class="col-start-1 row-start-1 grid grid-cols-1 sm:grid-cols-9 gap-2 items-center">
                <div class="col-start-1 row-start-1 sm:col-span-8">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Fakultas...',
                        'searchValues' => ['simple', 'full'],
                    ])
                </div>
                <div class="col-start-2 row-start-1 sm:col-span-1">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [3, 5, 8, 10],
                        'key' => 'page-control-fakultas',
                        'isSmall' => 1,
                        'withB' => 0,
                    ])
                </div>
            </div>

        </div>


    </div>

    {{-- BAGIAN SECONDARY SEARCH (Departemen & Fakultas) --}}
    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full z-10">

        <div class="sm:col-span-4 relative">
            <livewire:global.search-filters.departemen-search-filter lazy wire:key="dp-search-filter" />
        </div>

        <div class="sm:col-span-4 relative">
            <livewire:global.search-filters.fakultas-search-filter lazy wire:key="fak-search-filter" />
        </div>
    </div>
</div>
