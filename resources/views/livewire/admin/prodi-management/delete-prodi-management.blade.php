<div>
    <flux:modal name="prodi-delete" wire:model.live="showProdiDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                        x-show="$store.prodi?.isForceDelete">PERMANEN!</strong></flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin menghapus
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="
                            $store.prodi?.typeModal_delete == 'prodi' ? '***Program Studi ' + $store.prodi?.nama_pr_delete + '***' : 
                            ($store.prodi?.typeModal_delete == 'departemen' ? '***Departemen ' + $store.prodi?.nama_dp_delete + '***' : 
                            ($store.prodi?.typeModal_delete == 'fakultas' ? '***Fakultas ' + $store.prodi?.nama_fk_delete + '***' : '***Data ini***'))
                        ">
                    </strong> dengan <strong class="text-red-700 dark:text-red-400"
                        x-text="
                            '***Kode ' + $store.prodi?.kode_delete + '***'
                        ">
                    </strong>?
                    <span x-show="$store.prodi?.isForceDelete">
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

                <flux:button wire:click="destroyProdi" wire:loading.attr="disabled"
                    wire:target="deleteProdi, destroyProdi" type="submit" variant="primary"
                    class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                    <span
                        x-text="
                            $store.prodi?.typeModal_delete === 'prodi' ? 'Ya, Hapus Program Studi' : 
                            ($store.prodi?.typeModal_delete === 'departemen' ? 'Ya, Hapus Departemen' : 
                            ($store.prodi?.typeModal_delete === 'fakultas' ? 'Ya, Hapus Fakultas' : 'Ya, Hapus Data ini'))
                        "
                        wire:loading.remove wire:target="destroyProdi">
                        Ya, Hapus Data
                    </span>

                    <span wire:loading wire:target="destroyProdi">
                        Menghapus...
                    </span>
                </flux:button>

            </div>
        </div>

    </flux:modal>
</div>
