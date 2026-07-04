<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="table-border border-b gap-2 sm:gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-tiny flex space-x-4 overflow-x-auto pb-1 w-full">
                {{-- Program Studi --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['prodi'] ?? null,
                    'tabString' => '',
                    'tabNameString' => 'Program Studi',
                ])
                {{-- Tab Departemen --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['departemen'] ?? null,
                    'tabString' => 'departemen',
                ])
                {{-- Tab Fakultas --}}
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['fakultas'] ?? null,
                    'tabString' => 'fakultas',
                ])
            </div>
        </div>

        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'nameXString' => 'Rekap Capaian',
                'xString' => "generateRekapCapaian()",
                'color' => 'blue',
                'icon' => 'academic-cap',
                'autoSmall' => 'sm',
            ])
        </div>

        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportProdiExcel()',
                'autoSmall' => 'sm',
            ])
        </div>

    </div>
</div>
