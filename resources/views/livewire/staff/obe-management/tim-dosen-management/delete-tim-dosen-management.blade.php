<div>
    <flux:modal name="tim-dosen-delete" wire:model="showTimDosenDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                        x-show="$store.tim_dosen?.isForceDelete">PERMANEN!</strong></flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin menghapus
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.tim_dosen?.tim_dosen_delete ? '***Tim Dosen ' + $store.tim_dosen?.tim_dosen_delete + '***' : '***Tim Dosen ini***'
                    ">
                    </strong> dengan
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.tim_dosen?.kode_tim_dosen_delete ? '***Kode ' + $store.tim_dosen?.kode_tim_dosen_delete + '***' : '***Kode XXXYYYY***'
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

                <flux:button wire:click="destroyTimDosen" wire:loading.attr="disabled"
                    wire:target="deleteTimDosen, destroyTimDosen" type="submit" variant="primary"
                    class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                    <span wire:loading.remove wire:target="destroyTimDosen">Ya, Hapus Tim Dosen
                    </span>

                    <span wire:loading wire:target="destroyTimDosen">
                        Menghapus...
                    </span>
                </flux:button>

            </div>
        </div>

    </flux:modal>
</div>
