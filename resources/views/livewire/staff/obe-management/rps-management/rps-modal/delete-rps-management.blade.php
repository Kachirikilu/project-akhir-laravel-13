<div>
    <flux:modal name="rps-delete" wire:model.live="showRPSDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
        @if ($isReady)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                            x-show="$store.rps?.isForceDelete">PERMANEN!</strong></flux:heading>
                    <flux:subheading>
                        Apakah Anda yakin ingin menghapus
                        <strong class="text-red-700 dark:text-red-400"
                            x-text="$store.rps?.rps_delete ? '***RPS ' + $store.rps?.rps_delete + '***' : '***RPS ini***'
                    ">
                        </strong> dengan
                        <strong class="text-red-700 dark:text-red-400"
                            x-text="$store.rps?.kode_rps_delete ? '***Kode ' + $store.rps?.kode_rps_delete + '***' : '***Kode XXXYYYY***'
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
                bg-[var(--sub-table-color)] hover:bg-[var(--main-table-color)] active:bg-[var(--main-table-color)]/90
                text-[var(--contrast-second-text)]
                transition-colors duration-200">
                            Batal</flux:button>
                    </flux:modal.close>

                    <flux:button wire:click="destroyRPS" wire:loading.attr="disabled"
                        wire:target="deleteRPS, destroyRPS" type="submit" variant="primary"
                        class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 active:bg-red-800 border-none transition-colors duration-200">
                        <span wire:loading.remove wire:target="destroyRPS">Ya, Hapus RPS
                        </span>

                        <span wire:loading wire:target="destroyRPS">
                            Menghapus...
                        </span>
                    </flux:button>

                </div>
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-delete-skeleton')
        @endif
    </flux:modal>
</div>
