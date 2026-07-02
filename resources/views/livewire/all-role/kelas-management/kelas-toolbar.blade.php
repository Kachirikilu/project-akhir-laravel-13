<div class="flex flex-wrap items-center gap-2 mb-4">
    <h2 class="text-xl sm:text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Kelas</h2>
    @if (Auth::user()->admin || Auth::user()->dosen)
        <div class="ml-auto">
            <flux:dropdown>
                <flux:button variant="primary" icon="plus" size="sm"
                    class="cursor-pointer text-white bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)] active:bg-[var(--hover-focus-color)]/90 transition-all duration-200 ease-in-out"
                    wire:target="addKelas">
                    Tambah Kelas
                </flux:button>

                <flux:menu
                    class="min-w-48 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] scrollbar-medium">
                    <flux:menu.heading>Tambah Kelas</flux:menu.heading>
                    <flux:menu.separator />

                    {{-- Program Studi --}}
                    <flux:menu.item
                        @click="
                            $store.kelas?.setEdit(0);
                            $store.kelas?.setColor('text-emerald-700 dark:text-emerald-400');
                            $store.kelas?.reset(1);
                            $flux.modal('kelas-modal').show();
                            $dispatch('open-add-kelas-modal');
                        "
                        class="text-xs sm:text-sm cursor-pointer !text-emerald-600 dark:!text-emerald-400 hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30 active:!bg-emerald-200 dark:active:!bg-emerald-900">
                        <flux:icon name="rectangle-group"
                            class="!text-emerald-600 dark:!text-emerald-400 mr-2 h-4 w-4" />
                        <div class="flex justify-between items-center w-full">
                            <span class="mr-7 whitespace-nowrap">Kelas Perkuliahan</span>
                        </div>
                    </flux:menu.item>

                </flux:menu>
            </flux:dropdown>
        </div>
    @endif
</div>
