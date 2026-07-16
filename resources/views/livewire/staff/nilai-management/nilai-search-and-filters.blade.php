<div x-data="{
    activeTab: $wire.entangle('switchTable'),
    activeFilter: $wire.entangle('filterStatus')
}"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-50">
        @include('livewire.staff.obe-management.obe-partial.obe-filters', ['rpsOnly' => 1])


        <div x-show="activeTab == 'mahasiswa'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByStatus',
                    'filterString' => 'filterStatus',
                    'totalTab' => $stats['mahasiswa-prodi'] ?? null,
                    'totalTab1' => $stats['mahasiswa-opsi'] ?? null,
                    'totalTab2' => $stats['mahasiswa-aktif'] ?? null,
                    'totalTab3' => $stats['mahasiswa-non-aktif'] ?? null,
                    'tab1String' => 'mahasiswa-all',
                    'tab2String' => 'mahasiswa-aktif',
                    'tab3String' => 'mahasiswa-non-aktif',
                    'tabName' => Auth::user()->prodi ?? 'Program Studi Saya',
                    'tab1Name' => 'Semua Status',
                    'tab2Name' => 'Aktif',
                    'tab3Name' => 'Tidak Aktif',
                ])
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-user',
                    'autoSmall' => 'md',
                ])
            </div>
        </div>
    </div>

    <div x-show="activeTab == 'mahasiswa'" class="grid grid-cols-1 grid-rows-1 relative isolate z-40">
        <div x-show="activeFilter == ''" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
            <div class="sm:col-span-7 relative">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Nama, Email, atau NIM Mahasiswa...',
                    'searchMode' => $searchMode,
                    'searchValues' => ['simple', 'smart', 'complex'],
                    'searchOptions' => ['Cari Email & Identitas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                ])
            </div>
        </div>

        <div x-show="activeFilter !== ''" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4" class="col-start-1 row-start-1 w-full">
            <div class=" grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Nama, Email, atau NIM Mahasiswa...',
                        'searchMode' => $searchMode,
                        'searchValues' => ['simple', 'smart', 'complex'],
                        'searchOptions' => ['Cari Email & Identitas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                    ])
                </div>

                <div class="order-3 sm:order-2 sm:col-span-3 relative">
                    <livewire:global.search-filters.prodi-search-filter lazy wire:key="pr-search-filter" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">
                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.departemen-search-filter lazy wire:key="dp-search-filter" />
                </div>

                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.fakultas-search-filter lazy wire:key="fk-search-filter" />
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN SEARCH UTAMA --}}
    <div x-show="activeTab == 'rps'" class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 z-20">
        <div class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Rencana Pembelajaran Semester...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Cerdas', 'Pencarian Kompleks'],
            ])
        </div>
        <div class="relative sm:col-span-3">
            <livewire:global.search-filters.prodi-search-filter lazy wire:key="prodi-search-filter" />
        </div>
        <div class="relative sm:col-span-3">
            <livewire:global.search-filters.mk-search-filter lazy wire:key="mk-search-filter" />
        </div>
        <div class="relative sm:col-span-4">
            <livewire:global.search-filters.dosen-search-filter lazy wire:key="dosen-search-filter" />
        </div>

    </div>
</div>
