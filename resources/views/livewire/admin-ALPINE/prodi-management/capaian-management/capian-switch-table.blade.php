<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">


    <div class="table-border border-b gap-4 flex items-end">

        <div class="min-w-0 flex-1">
            <div class="scrollbar-tiny flex space-x-4 overflow-x-auto pb-1 w-full">
                @include('livewire.global.search-and-filters.partial.tab-filter', [
                    'xString' => 'switchingTable',
                    'xFilter' => $switchTable,
                    'tabFilter' => $stats['cpl'] ?? null,
                    'tabString' => 'cpl',
                    'tabNameString' => 'Capaian ' . $kode_pr_url,
                ])
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
                    'tabFilter' => $stats['mahasiswa'] ?? null,
                    'tabString' => 'mahasiswa',
                    'tabNameString' => 'Mahasiswa',
                ])
            </div>
        </div>

    </div>


</div>
