<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div
        class="border-[var(--border-table-color)] flex flex-col md:flex-row md:justify-between md:items-end border-b gap-3">

        @include('livewire.global.table.export-excel', ['xString' => 'exportProdiExcel'])

        <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">
            {{-- Program Studi --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalProdis,
                'tabString' => 'prodi',
                'tabNameString' => 'Program Studi',
            ])
            {{-- Tab Departemen --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalDepartemen,
                'tabString' => 'departemen',
            ])
            {{-- Tab Fakultas --}}
            @include('livewire.global.search-and-filters.partial.tab-filter', [
                'xString' => 'switchingTable',
                'xFilter' => $switchTable,
                'tabFilter' => $totalFakultas,
                'tabString' => 'fakultas',
            ])
        </div>
    </div>
</div>