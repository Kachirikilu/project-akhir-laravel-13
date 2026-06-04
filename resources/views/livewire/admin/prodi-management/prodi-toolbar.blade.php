<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Program Studi</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] transition-all duration-200 ease-in-out"
                wire:target="addProdi">
                Tambah Program Studi
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">
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
                        $wire.addProdi('prodi');
                    "
                    class="cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30">
                    <flux:icon name="academic-cap" class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7">Program Studi</span>
                        <flux:icon wire:loading wire:target="addProdi('prodi')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
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
                        $wire.addProdi('departemen');
                    "
                    class="cursor-pointer !text-amber-600 dark:!text-amber-400 hover:!bg-amber-100 dark:hover:!bg-amber-900/30">
                    <flux:icon name="book-open" class="!text-amber-600 dark:!text-amber-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7">Departemen</span>
                        <flux:icon wire:loading wire:target="addProdi('departemen')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
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
                        $wire.addProdi('fakultas');
                    "
                    class="cursor-pointer !text-indigo-600 dark:!text-indigo-400 hover:!bg-indigo-100 dark:hover:!bg-indigo-900/30">
                    <flux:icon name="building-library" class="!text-indigo-600 dark:!text-indigo-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7">Fakultas</span>
                        <flux:icon wire:loading wire:target="addProdi('fakultas')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>
