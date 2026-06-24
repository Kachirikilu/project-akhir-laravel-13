<!-- Header Section: Judul Saja (Lebih Minimalis) -->
<div x-data="{ activeTab: @entangle('switchTable') }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2 mb-4">
        <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2.5">
            <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
            Sesi Kelas
        </h3>

        <div class="shrink-0 flex lg:hidden">
            <div x-show="activeTab === 'sesi-card'">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [2, 4, 8, 16],
                    'alpine' => 'sesi',
                    'key' => 'page-control-sesi-card',
                    'withB' => 0,
                ])
            </div>
            <div x-show="activeTab === 'sesi-table'">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [2, 4, 8, 16],
                    'key' => 'page-control-sesi-table',
                    'withB' => 0,
                ])
            </div>
            <div x-show="activeTab === 'mahasiswa' || activeTab === 'cpmk'">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-mahasiswa',
                    'withB' => 0,
                ])
            </div>
        </div>
    </div>

    <!-- Container Filter & Tab -->
    <div class="mb-5">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full lg:w-auto pb-1">
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['sesi'],
                    'tabString' => 'sesi-card',
                    'tabNameString' => 'Pertemuan',
                    'icon' => 'academic-cap',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['sesi'],
                    'tabString' => 'sesi-table',
                    'tabNameString' => 'Tabel Pertemuan',
                    'icon' => 'table-cells',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['mahasiswa'],
                    'tabString' => 'mahasiswa',
                    'icon' => 'users',
                ])

                @if (Auth::user()->admin || Auth::user()->dosen)
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => $stats['mahasiswa'],
                        'tabString' => 'cpmk',
                        'icon' => 'academic-cap',
                    ])
                @endif
            </div>

            {{-- <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
            @if ($sesis->hasPages())
                <div class="p-4" id="pagination-links-container" wire:target="{{ $sesis->getPageName() }}">
                    {{ $sesis->links('vendor.pagination.tailwind', ['isSmall' => 1]) }}
                </div>
            @endif
            </div> --}}
            <div class="shrink-0 hidden lg:flex">

                <div x-show="activeTab === 'sesi-card'">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [2, 4, 8, 16],
                        'alpine' => 'sesi',
                        'key' => 'page-control-sesi-card',
                        'withB' => 0,
                    ])
                </div>
                <div x-show="activeTab === 'sesi-table'">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [2, 4, 8, 16],
                        'key' => 'page-control-sesi-table',
                        'withB' => 0,
                    ])
                </div>
                <div x-show="activeTab === 'mahasiswa' || activeTab === 'cpmk'">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                        'key' => 'page-control-mahasiswa',
                        'withB' => 0,
                    ])
                </div>
            </div>

        </div>
    </div>
</div>
