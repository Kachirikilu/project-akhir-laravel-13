<div>
    <flux:modal name="scpmk-delete" wire:model.live="showSCPMKDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                        x-show="$store.scpmk?.isForceDelete">PERMANEN!</strong></flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin menghapus
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.scpmk?.scpmk_delete ? '***Sub-CPMK ' + $store.scpmk?.scpmk_delete + '***' : '***Sub-CPMK ini***'
                    ">
                    </strong> dengan
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.scpmk?.kode_scpmk_delete ? '***Kode ' + $store.scpmk?.kode_scpmk_delete + '***' : '***Kode XXXYYYY***'
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

                <flux:button wire:click="destroySCPMK" wire:loading.attr="disabled"
                    wire:target="deleteSCPMK, destroySCPMK" type="submit" variant="primary"
                    class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                    <span wire:loading.remove wire:target="destroySCPMK">Ya, Hapus Sub-CPMK
                    </span>

                    <span wire:loading wire:target="destroySCPMK">
                        Menghapus...
                    </span>
                </flux:button>

            </div>
        </div>

    </flux:modal>
</div>
