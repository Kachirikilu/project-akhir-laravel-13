<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="table-border border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-tiny flex space-x-4 overflow-x-auto pb-1 w-full">

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['user'] ?? null,
                    'tabString' => '',
                    'tabNameString' => 'Semua Pengguna',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['admin'] ?? null,
                    'tabString' => 'admin',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['dosen'] ?? null,
                    'tabString' => 'dosen',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['mahasiswa'] ?? null,
                    'tabString' => 'mahasiswa',
                ])
            </div>
        </div>

        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportUserExcel()',
                'autoSmall' => 'md',
            ])
        </div>

    </div>

</div>
