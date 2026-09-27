<!-- Header Section: Judul Saja (Lebih Minimalis) -->
<div x-data="{ activeTab: @entangle('switchTable') }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mt-2 mb-4">
        <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2.5">
            <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
            Sesi Kelas
        </h3>

     
            @include('livewire.global.table.detail-view-switch')

    </div>

    <!-- Container Filter & Tab -->
    <div class="mb-5">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full lg:w-auto pb-1">
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['sesi-hari-ini'] ?? null,
                    'tabString' => 'hari-ini',
                    'tabNameString' => 'Sesi Hari Ini',
                    'icon' => 'academic-cap',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['sesi'] ?? null,
                    'tabString' => 'card',
                    'tabNameString' => 'Pertemuan',
                    'icon' => 'academic-cap',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['sesi'] ?? null,
                    'tabString' => 'table',
                    'tabNameString' => 'Tabel Pertemuan',
                    'icon' => 'table-cells',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['mahasiswa'] ?? null,
                    'tabString' => 'mahasiswa',
                    'icon' => 'users',
                ])

                @if (Auth::user()->admin || Auth::user()->dosen)
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => $stats['mahasiswa'] ?? null,
                        'tabString' => 'cpmk',
                        'tabNameString' => 'Capaian Mahasiswa',
                        'icon' => 'academic-cap',
                    ])
                @endif
            </div>

        </div>
    </div>
</div>
