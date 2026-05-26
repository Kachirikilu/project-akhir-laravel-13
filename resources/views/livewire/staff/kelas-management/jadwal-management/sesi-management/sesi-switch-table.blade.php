<!-- Header Section: Judul Saja (Lebih Minimalis) -->
<div x-data="{ activeTab: @entangle('switchTable') }" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2 mb-4">
    <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2.5">
        <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
        Sesi Kelas
    </h3>

    <div class="shrink-0">
        <div x-show="activeTab === 'sesi-card' || activeTab === 'sesi-table'">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [2, 4, 8, 16],
                'key' => 'page-control-sesi',
                'withPM' => 0,
            ])
        </div>
        <div x-show="activeTab === 'mahasiswa'">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                'key' => 'page-control-sesi',
                'withPM' => 0,
            ])
        </div>
    </div>
</div>

<!-- Container Filter & Tab -->
<div x-data="{ activeTab: @entangle('switchTable') }" class="mb-5">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

        <div class="scrollbar-thin -mb-px flex items-center space-x-3 overflow-x-auto w-full md:w-auto pb-1">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable ?? null,
                'tabFilter' => $totalSesiKelas ?? null,
                'tabString' => 'sesi-card',
                'tabNameString' => 'Pertemuan',
                'icon' => 'academic-cap',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable ?? null,
                'tabFilter' => $totalSesiKelas ?? null,
                'tabString' => 'sesi-table',
                'tabNameString' => 'Tabel Pertemuan',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable ?? null,
                'tabFilter' => $totalMahasiswaKelas ?? null,
                'tabString' => 'mahasiswa',
                'icon' => 'users',
            ])
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
            <div class="relative w-full md:w-72 lg:w-96 grid grid-cols-1 grid-rows-1">
                <div x-show="activeTab === 'sesi-card' || activeTab === 'sesi-table'"
                    class="col-start-1 row-start-1 w-full" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 -translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                        'isLive' => 1,
                        'isBorder' => 2,
                    ])
                </div>
                <div x-show="activeTab === 'mahasiswa'" class="col-start-1 row-start-1 w-full"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 -translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Mahasiswa Kelas...',
                        'isLive' => 1,
                        'isBorder' => 2,
                    ])
                </div>
            </div>
        </div>

    </div>
</div>
