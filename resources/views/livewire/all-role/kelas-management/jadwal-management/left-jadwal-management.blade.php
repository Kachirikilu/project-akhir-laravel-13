<div>
<flux:modal name="left-jadwal-modal" wire:model="showJadwalLeft" wire:key="left-jadwal-modal" x-data @refresh-data-jadwal.window="$store.jadwal?.reset()"
    class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi Keluar Kelas</flux:heading>
                <flux:subheading x-data>
                    Apakah Anda yakin ingin keluar dari kelas
                    <strong class="text-red-700 dark:text-red-400">
                        <span x-text="'***' + ($store.jadwal.label_extra || '') + '***'"></span>
                    </strong> dengan
                    <strong class="text-red-700 dark:text-red-400">
                        Kode Kelas <span x-text="($store.jadwal.kode_kelas || '')"></span>
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

            <flux:button wire:click="leftJadwal" wire:loading.attr="disabled" wire:target="deleteRPS, leftJadwal"
                type="submit" variant="primary"
                class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                <span wire:loading.remove wire:target="leftJadwal">Ya, Keluar Kelas
                </span>

                <span wire:loading wire:target="leftJadwal">
                    Menghapus...
                </span>
            </flux:button>

        </div>
    </div>

</flux:modal>
</div>