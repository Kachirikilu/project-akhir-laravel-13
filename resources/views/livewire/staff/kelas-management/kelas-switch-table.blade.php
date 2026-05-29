<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">


    <div class="border-[var(--border-table-color)] border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">
                {{-- Mata Kuliah --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalKelas,
                    'tabString' => '',
                    'tabNameString' => 'Semua Kelas',
                ])
                {{-- Tab Tatap Muka --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalTatapMuka,
                    'tabString' => 'tatap-muka',
                    'tabNameString' => 'Tatap Muka',
                ])
                {{-- Tab Praktikum --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalPraktikum,
                    'tabString' => 'praktikum',
                    'tabNameString' => 'Praktikum',
                ])
                {{-- Tab Praktek Lapangan --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalPraktek,
                    'tabString' => 'praktek-lapangan',
                    'tabNameString' => 'Praktek Lapangan',
                ])
                {{-- Tab Simulasi --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalSimulasi,
                    'tabString' => 'simulasi',
                    'tabNameString' => 'Simulasi',
                ])
            </div>
        </div>

        {{-- <div class="shrink-0">
            @include('livewire.global.table.export-excel', [
                'xString' => 'exportKelasExcel',
                'autoSmall' => 'lg',
            ])
        </div> --}}

    </div>

</div>
