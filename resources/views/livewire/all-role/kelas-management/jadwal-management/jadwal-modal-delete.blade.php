<flux:modal name="jadwal-delete" wire:model="showJadwalDelete"
    class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                    x-show="$store.jadwal?.isForceDelete">PERMANEN!</strong></flux:heading>
            <flux:subheading>
                Apakah Anda yakin ingin menghapus Jadwal Kelas dengan Label
                <strong class="text-red-700 dark:text-red-400"
                    x-text="'***' + $store.jadwal?.label_jadwal_delete + '***'">
                </strong> dan Kode
                <strong class="text-red-700 dark:text-red-400"
                    x-text="'***' + $store.jadwal?.kode_jadwal_delete + '***'">
                </strong>?
                <span x-show="$store.jadwal?.isForceDelete">
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

            <flux:button wire:click="destroyJadwal" wire:loading.attr="disabled" wire:target="deleteJadwal, destroyJadwal"
                type="submit" variant="primary"
                class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                <span wire:loading.remove wire:target="destroyJadwal">
                    Ya, Hapus
                    <strong x-text="$store.jadwal?.role_delete">
                    </strong>?
                </span>

                <span wire:loading wire:target="destroyJadwal">
                    Menghapus...
                </span>
            </flux:button>
        </div>
    </div>

</flux:modal>
