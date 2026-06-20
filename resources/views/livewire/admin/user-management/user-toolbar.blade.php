<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-xl sm:text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Pengguna</h2>
    <div class="ml-auto">
        <flux:dropdown>
            <flux:button variant="primary" icon="plus" size="sm"
                class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out"
                wire:target="addUser">
                Tambah Pengguna
            </flux:button>

            <flux:menu
                class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                <flux:menu.heading>Pilih Role Pengguna</flux:menu.heading>
                <flux:menu.separator />

                {{-- Admin --}}
                <flux:menu.item
                    @click="
                        $store.user?.setType('admin');
                        $store.user?.setEdit(0);
                        {{-- $store.user?.resetSelect(); --}}
                        $store.user?.setColor('text-red-700 dark:text-red-400');
                        $store.user?.reset(1);
                        $flux.modal('user-modal').show();
                        $wire.addUser('admin');
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-red-700 dark:!text-red-400 hover:!bg-red-100 dark:hover:!bg-red-900/30 active:!bg-red-200 dark:active:!bg-red-900">
                    <flux:icon name="cog-6-tooth" class="!text-red-700 dark:!text-red-400 mr-2 h-4 w-4" />

                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Admin</span>
                        <flux:icon wire:loading wire:target="addUser('admin')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                {{-- Dosen --}}
                <flux:menu.item
                    @click="
                        $store.user?.setType('dosen');
                        $store.user?.setEdit(0);
                        {{-- $store.user?.resetSelect(); --}}
                        $store.user?.setColor('text-lime-700 dark:text-lime-400');
                        $store.user?.reset(1);
                        $flux.modal('user-modal').show();
                        $wire.addUser('dosen');
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-lime-600 dark:!text-lime-400 hover:!bg-lime-100 dark:hover:!bg-lime-900/30 active:!bg-lime-200 dark:active:!bg-lime-900">
                    <flux:icon name="briefcase" class="!text-lime-600 dark:!text-lime-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Dosen</span>
                        <flux:icon wire:loading wire:target="addUser('dosen')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                {{-- Mahasiswa --}}
                <flux:menu.item
                    @click="
                        $store.user?.setType('mahasiswa');
                        $store.user?.setEdit(0);
                        {{-- $store.user?.resetSelect(); --}}
                        $store.user?.setColor('text-cyan-700 dark:text-cyan-400');
                        $store.user?.reset(1);
                        $flux.modal('user-modal').show();
                        $wire.addUser('mahasiswa');
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-cyan-600 dark:!text-cyan-400 hover:!bg-cyan-100 dark:hover:!bg-cyan-900/30 active:!bg-cyan-200 dark:active:!bg-cyan-900">
                    <flux:icon name="book-open" class="!text-cyan-600 dark:!text-cyan-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Mahasiswa</span>
                        <flux:icon wire:loading wire:target="addUser('mahasiswa')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Input File --}}
                <flux:menu.item
                    @click="
                        $store.user?.setType('excel');
                        $store.user?.setEdit(0);
                        $store.user?.setColor('text-green-700 dark:text-green-400', 'file:bg-green-600 hover:file:bg-green-700 active:file:bg-green-800 dark:file:bg-green-500 dark:hover:file:bg-green-600 dark:active:file:bg-green-700');
                        $store.user?.reset(1);
                        $flux.modal('user-excel-modal').show();
                        $wire.addUser('excel');
                    "
                    class="text-xs sm:text-sm cursor-pointer !text-green-600 dark:!text-green-400 hover:!bg-green-100 dark:hover:!bg-green-900/30 active:!bg-green-200 dark:active:!bg-green-900">
                    <flux:icon name="table-cells" class="!text-green-600 dark:!text-green-400 mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span class="mr-7 whitespace-nowrap">Input File Excel</span>
                        <flux:icon wire:loading wire:target="addUser('excel')" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-2" />
                    </div>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</div>
