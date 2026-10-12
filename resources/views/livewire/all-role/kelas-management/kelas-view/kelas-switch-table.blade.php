<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">


    <div class="table-border border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-tiny flex space-x-4 overflow-x-auto pb-1 w-full">
                {{-- @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => 5526 ?? null,
                    'tabString' => '',
                    'tabNameString' => 'Semua Kelas',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => 4559 ?? null,
                    'tabString' => 'tatap-muka',
                    'tabNameString' => 'Tatap Muka',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => 751 ?? null,
                    'tabString' => 'praktikum',
                    'tabNameString' => 'Praktikum',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => 120 ?? null,
                    'tabString' => 'praktek-lapangan',
                    'tabNameString' => 'Praktek Lapangan',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => 96 ?? null,
                    'tabString' => 'simulasi',
                    'tabNameString' => 'Simulasi',
                ]) --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['kelas'] ?? null,
                    'tabString' => '',
                    'tabNameString' => 'Semua Kelas',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['kelas-tp'] ?? null,
                    'tabString' => 'tatap-muka',
                    'tabNameString' => 'Tatap Muka',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['kelas-pr'] ?? null,
                    'tabString' => 'praktikum',
                    'tabNameString' => 'Praktikum',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['kelas-pl'] ?? null,
                    'tabString' => 'praktek-lapangan',
                    'tabNameString' => 'Praktek Lapangan',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['kelas-sm'] ?? null,
                    'tabString' => 'simulasi',
                    'tabNameString' => 'Simulasi',
                ])
            </div>
        </div>

        {{-- <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportKelasExcel()',
                'autoSmall' => 'lg',
            ])
        </div> --}}

    </div>

</div>
