<div>
    <flux:modal name="cpl-delete" wire:model.live="showCPLDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
        @if ($isReady)

        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                        x-show="$store.cpl?.isForceDelete">PERMANEN!</strong></flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin menghapus
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.cpl?.cpl_delete ? '***CPL ' + $store.cpl?.cpl_delete + '***' : '***CPL ini***'
                    ">
                    </strong> dengan
                    <strong class="text-red-700 dark:text-red-400"
                        x-text="$store.cpl?.kode_cpl_delete ? '***Kode ' + $store.cpl?.kode_cpl_delete + '***' : '***Kode XXXYYYY***'
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

                <flux:button wire:click="destroyCPL" wire:loading.attr="disabled" wire:target="deleteCPL, destroyCPL"
                    type="submit" variant="primary"
                    class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                    <span wire:loading.remove wire:target="destroyCPL">Ya, Hapus CPL
                    </span>

                    <span wire:loading wire:target="destroyCPL">
                        Menghapus...
                    </span>
                </flux:button>

            </div>
        </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-delete-skeleton                               ')
        @endif
    </flux:modal>
</div>
