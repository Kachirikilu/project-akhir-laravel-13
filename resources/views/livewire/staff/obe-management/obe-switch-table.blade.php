<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">


    <div class="table-border border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-tiny flex space-x-4 overflow-x-auto pb-1 w-full">
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['rps'] ?? null,
                    'tabString' => 'rps',
                    'tabNameString' => 'RPS',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['cpl'] ?? null,
                    'tabString' => 'cpl',
                    'tabNameString' => 'CPL',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['cpmk'] ?? null,
                    'tabString' => 'cpmk',
                    'tabNameString' => 'CPMK',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['scpmk'] ?? null,
                    'tabString' => 'sub-cpmk',
                    'tabNameString' => 'Sub-CPMK',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['ref'] ?? null,
                    'tabString' => 'referensi',
                    'tabNameString' => 'Referensi',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['tim-dosen'] ?? null,
                    'tabString' => 'tim-dosen',
                    'tabNameString' => 'Tim Dosen',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['dosen'] ?? null,
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
