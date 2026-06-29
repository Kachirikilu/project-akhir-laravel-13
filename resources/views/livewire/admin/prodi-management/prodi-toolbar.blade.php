<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-xl sm:text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Program Studi</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" size="sm" wire:click="$dispatch('trigger-prodi-modal')"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out"
                wire:target="addProdi">
                Tambah Program Studi
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                <flux:menu.heading>Pilih Jenis</flux:menu.heading>
                <flux:menu.separator />

                {{-- Program Studi --}}
                <flux:menu.item
                    @click="
                        $store.prodi?.setType('prodi');
                        $store.prodi?.setEdit(0);
                        $store.prodi?.setColor('text-emerald-700 dark:text-emerald-400');
                        $store.prodi?.reset(1);
                        $flux.modal('prodi-modal').show();
                        {{-- $wire.addProdi('prodi'); --}}
                        $dispatch('open-add-prodi-modal', { type: 'prodi' });
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 active:!bg-emerald-200 dark:active:!bg-emerald-900">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Program Studi</span>
                    </div>
                </flux:menu.item>

                {{-- Departemen --}}
                <flux:menu.item
                    @click="
                        $store.prodi?.setType('departemen');
                        $store.prodi?.setEdit(0);
                        $store.prodi?.setColor('text-amber-700 dark:text-amber-400');
                        $store.prodi?.reset(1);
                        $flux.modal('prodi-modal').show();
                        {{-- $wire.addProdi('departemen'); --}}
                        $dispatch('open-add-prodi-modal', { type: 'departemen' });
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30 active:!bg-amber-200 dark:active:!bg-amber-900">
                    <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Departemen</span>
                    </div>
                </flux:menu.item>

                {{-- Fakultas --}}
                <flux:menu.item
                    @click="
                        $store.prodi?.setType('fakultas');
                        $store.prodi?.setEdit(0);
                        $store.prodi?.setColor('text-[var(--focus-color)]');
                        $store.prodi?.reset(1);
                        $flux.modal('prodi-modal').show();
                        {{-- $wire.addProdi('fakultas'); --}}
                        $dispatch('open-add-prodi-modal', { type: 'fakultas' });
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-100 dark:hover:!bg-indigo-900/30 active:!bg-indigo-200 dark:active:!bg-indigo-900">
                    <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Fakultas</span>
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>
