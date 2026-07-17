<div class="flex flex-wrap items-center gap-6 mb-2">

    <h3 class="text-xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2">
        <flux:icon name="calendar-days" class="h-6 w-6 text-[var(--focus-color)]" />
        Manajemen Nilai & Capaian Mahasiswa
    </h3>
    <div class="mt-3 flex flex-col md:flex-row md:items-end md:justify-between gap-4 w-full" x-data="{ activeTab: @entangle('switchTable') }">

        <div class="flex items-center min-w-0 w-full md:w-auto pb-1.5 md:pb-0.5">
            <div class="w-full">

                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 w-full">

                    <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full pb-1">
                        @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                            'xString' => 'switchingTable',
                            'xFilter' => $switchTable,
                            'tabFilter' => $stats['mahasiswa-opsi'] ?? null,
                            'tabString' => 'mahasiswa',
                            'icon' => 'users',
                        ])

                        @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                            'xString' => 'switchingTable',
                            'xFilter' => $switchTable,
                            'tabFilter' => $stats['rps'] ?? null,
                            'tabString' => 'rps',
                            'tabNameString' => 'Rencana Pembelajaran Semester',
                            'icon' => 'clipboard-document-list',
                        ])
                    </div>

                </div>
            </div>
        </div>

        <div class="flex flex-col items-stretch md:items-end gap-3 mb-5 w-full md:w-auto shrink-0">
            <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">

                    <div></div>
             
                    <div class="shrink-0">
                        <flux:dropdown>
                            <flux:button variant="primary" icon="plus" size="sm"
                                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out whitespace-nowrap">
                                Atur Kunci Nilai
                            </flux:button>

                            <flux:menu
                                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                                <flux:menu.heading>Atur Kunci Nilai</flux:menu.heading>
                                <flux:menu.separator />

                                <flux:menu.item
                                    @click="
                                        $store.nilai?.reset();
                                        $store.nilai?.setEdit(1);
                                        $store.nilai?.setColor('text-blue-700 dark:text-blue-400');
                                        $flux.modal('lock-nilai-modal').show();
                                        $dispatch('open-edit-lock-nilai-modal')
                                    "
                                    class="text-xs sm:text-sm cursor-pointer !text-blue-600 dark:!text-blue-400 hover:!bg-blue-100 dark:hover:!bg-blue-900/30 active:!bg-blue-200 dark:active:!bg-blue-900">
                                    <flux:icon name="chart-pie"
                                        class="!text-blue-600 dark:!text-blue-400 mr-2 h-4 w-4" />
                                    <div class="flex justify-between items-center w-full">
                                        <span class="mr-7 whitespace-nowrap">Kunci Nilai</span>
                                    </div>
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

            </div>

        </div>
    </div>
</div>
