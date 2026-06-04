<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

    <div class="border-[var(--border-table-color)] border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-thin flex space-x-4 overflow-x-auto pb-1 w-full">

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalUsers,
                    'tabString' => '',
                    'tabNameString' => 'Semua Pengguna',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalAdmins,
                    'tabString' => 'admin',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalDosens,
                    'tabString' => 'dosen',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $totalMahasiswas,
                    'tabString' => 'mahasiswa',
                ])
            </div>
        </div>

        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportUserExcel',
                'autoSmall' => 'md',
            ])
        </div>

    </div>

</div>
