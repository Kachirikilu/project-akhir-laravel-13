<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border">

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


        @php
            $runningAllKey = 'rekap_capaian_running_all';
            $isProcessAll = Cache::has($runningAllKey);
        @endphp

        <div class="shrink-0" @if ($isProcessAll) wire:poll.12s @endif>
            @include('livewire.global.table.export-button', [
                'nameXString' => $isProcessAll
                    ? 'Process Capaian ' . (env('UNIV') ?? 'UNSRI')
                    : 'Rekap Capaian ' . (env('UNIV') ?? 'UNSRI'),
                        'isLoading' => $isProcessAll,
                'xString' => 'rekapCapaianAll()',
                'color' => 'yellow',
                'icon' => 'academic-cap',
                'autoSmall' => 'lg',
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
