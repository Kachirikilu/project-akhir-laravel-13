<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">


    <div class="border-[var(--border-table-color)] border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalRPS,
                    'tabString' => 'rps',
                    'tabNameString' => 'RPS',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalCPMK,
                    'tabString' => 'cpmk',
                    'tabNameString' => 'CPMK',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalSCPMK,
                    'tabString' => 'sub-cpmk',
                    'tabNameString' => 'Sub-CPMK',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalCPL,
                    'tabString' => 'cpl',
                    'tabNameString' => 'CPL',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalRef,
                    'tabString' => 'referensi',
                    'tabNameString' => 'Referensi',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalDosen,
                    'tabString' => 'dosen',
                    'tabNameString' => 'Dosen',
                ])
            </div>
        </div>

        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportOBEExcel()',
                'autoSmall' => 'lg',
            ])
        </div>

    </div>


</div>
