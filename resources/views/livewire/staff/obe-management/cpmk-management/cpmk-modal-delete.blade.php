<flux:modal name="cpmk-delete" wire:model.live="showCPMKDelete"
    class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                    x-show="$store.cpmk?.isForceDelete">PERMANEN!</strong></flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus
                <strong class="text-red-700 dark:text-red-400"
                    x-text="$store.cpmk?.cpmk_delete ? '***CPMK ' + $store.cpmk?.cpmk_delete + '***' : '***CPMK ini***'
                    ">
                </strong> dengan
                <strong class="text-red-700 dark:text-red-400"
                    x-text="$store.cpmk?.kode_cpmk_delete ? '***Kode ' + $store.cpmk?.kode_cpmk_delete + '***' : '***Kode XXXYYYY***'
                    ">
                </strong>?
                Tindakan ini tidak dapat dibatalkan!
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

            <flux:button wire:click="destroyCPMK" wire:loading.attr="disabled" wire:target="deleteCPMK, destroyCPMK"
                type="submit" variant="primary"
                class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                <span wire:loading.remove wire:target="destroyCPMK">Ya, Hapus CPMK
                </span>

                <span wire:loading wire:target="destroyCPMK">
                    Menghapus...
                </span>
            </flux:button>

        </div>
    </div>

</flux:modal>
