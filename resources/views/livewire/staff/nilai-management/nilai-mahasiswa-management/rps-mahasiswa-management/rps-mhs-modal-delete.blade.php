<flux:modal name="nilai-delete" wire:model="showNilaiDelete"
    class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                    x-show="$store.nilai?.isForceDelete">PERMANEN!</strong></flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus nilai Mata Kuliah
                <strong class="text-red-700 dark:text-red-400"
                    x-text="'***' + ($store.nilai?.mk_delete ?? '') + ' ' + ($store.nilai?.kode_rps_delete ?? '') + '***'">
                </strong>
                dari mahasiswa
                <strong class="text-red-700 dark:text-red-400"
                    x-text="($store.nilai?.name_delete ?? '') + ' (NIM: ' + ($store.nilai?.nim_delete ?? '') + ')'">
                </strong>?

                <span x-show="$store.nilai?.isForceDelete"
                    class="block mt-1 font-medium text-red-600 dark:text-red-400">
                    Tindakan ini tidak dapat dibatalkan!
                </span>
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost"
                    class="cursor-pointer w-full sm:w-auto 
                bg-[var(--sub-table-color)] hover:bg-[var(--main-table-color)]
                text-[var(--contrast-second-text)]
                transition-colors duration-200">
                    Batal</flux:button>
            </flux:modal.close>

            <flux:button wire:click="destroyNilai" wire:loading.attr="disabled" wire:target="deleteNilai, destroyNilai"
                type="submit" variant="primary"
                class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                <span wire:loading.remove wire:target="destroyNilai">
                    Ya, Hapus
                    <strong x-text="$store.nilai?.role_delete">
                    </strong>?
                </span>

                <span wire:loading wire:target="destroyNilai">
                    Menghapus...
                </span>
            </flux:button>

        </div>
    </div>
</flux:modal>
