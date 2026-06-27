<div class="flex flex-wrap items-center gap-6 mb-4">

    <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2">
        <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
        Jadwal Kelas
    </h3>
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-5 w-full" x-data="{ activeTab: @entangle('switchTable') }">

        <div class="flex items-center min-w-0 w-full md:w-auto pb-1.5 md:pb-0.5">
            <div class="w-full">

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 w-full">

                    <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full pb-1">
                        @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                            'xString' => 'switchingTable',
                            'xFilter' => $switchTable,
                            'tabFilter' => $stats['jadwal'],
                            'tabString' => 'jadwal-card',
                            'tabNameString' => 'Jadwal Kelas',
                            'icon' => 'academic-cap',
                        ])

                        @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                            'xString' => 'switchingTable',
                            'xFilter' => $switchTable,
                            'tabFilter' => $stats['jadwal'],
                            'tabString' => 'jadwal-table',
                            'tabNameString' => 'Tabel Jadwal',
                            'icon' => 'table-cells',
                        ])
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col items-stretch md:items-end gap-3 w-full md:w-auto shrink-0">
            <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">

                <div class="shrink-0">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [2, 4, 6, 8, 12],
                        'key' => 'page-control-jadwal',
                        'isSmall' => 1,
                        'withB' => 0,
                        'withArr' => 1,
                    ])
                </div>

                @if (Auth::user()->admin || Auth::user()->dosen)
                    <div class="shrink-0">
                        <flux:dropdown>
                            <flux:button variant="primary" icon="plus" size="sm"
                                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out whitespace-nowrap"
                                wire:target="addJadwal">
                                Tambah Jadwal
                            </flux:button>

                            <flux:menu
                                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                                <flux:menu.heading>Tambah Jadwal</flux:menu.heading>
                                <flux:menu.separator />

                                <flux:menu.item
                                    @click="
                                $store.jadwal?.setEdit(0);
                                $store.jadwal?.setColor('text-amber-700 dark:text-amber-400');
                                $store.jadwal?.reset(1);
                                $flux.modal('jadwal-modal').show();
                                $wire.addJadwal();
                            "
                                    class="text-xs sm:text-sm cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30 active:!bg-amber-200 dark:active:!bg-amber-900">
                                    <flux:icon name="calendar-days"
                                        class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                                    <div class="flex justify-between items-center w-full">
                                        <span class="mr-7 whitespace-nowrap">Jadwal Perkuliahan</span>
                                        <flux:icon wire:loading wire:target="addJadwal()" name="arrow-path"
                                            class="animate-spin h-4 w-4 ml-2" />
                                    </div>
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                @endif

            </div>

            @if ($switchTable == 'jadwal-table')
                <div class="w-full md:w-96 xl:w-108">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Jadwal Kelas...',
                        'defaultLive' => 1,
                        'searchMode' => $searchMode,
                        'searchValues' => ['simple', 'full'],
                        'searchOptions' => ['Cari Kode Kelas', 'Pencarian Kompleks'],
                        'isBorder' => 2,
                    ])
                </div>
            @endif

        </div>
    </div>
</div>
