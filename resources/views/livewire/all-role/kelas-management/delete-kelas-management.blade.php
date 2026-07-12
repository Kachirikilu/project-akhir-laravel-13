<div>
    <flux:modal name="kelas-delete" wire:model.live="showKelasDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

        @if ($isReady)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                            x-show="$store.kelas?.isForceDelete">PERMANEN!</strong></flux:heading>
                    <flux:subheading>
                        Apakah Anda yakin ingin menghapus Kelas
                        <strong class="text-red-700 dark:text-red-400"
                            x-text="'***' + $store.kelas?.nama_kelas_delete + '***'">
                        </strong> dengan kode
                        <strong class="text-red-700 dark:text-red-400"
                            x-text="'***' + $store.kelas?.kode_kelas_delete + '***'">
                        </strong>?
                        <span x-show="$store.kelas?.isForceDelete">
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

                    <flux:button wire:click="destroyKelas" wire:loading.attr="disabled"
                        wire:target="deleteKelas, destroyKelas" type="submit" variant="primary"
                        class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">
                        <span wire:loading.remove wire:target="destroyKelas">
                            Ya, Hapus
                            <strong x-text="$store.kelas?.role_delete">
                            </strong>?
                        </span>

                        <span wire:loading wire:target="destroyKelas">
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
