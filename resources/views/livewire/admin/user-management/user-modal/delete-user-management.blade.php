<div>
    <flux:modal name="user-delete" wire:model.live="showUserDelete"
        class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
        @if ($isReady)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Konfirmasi Hapus <strong class="text-red-700 dark:text-red-400"
                            x-show="$store.user?.isForceDelete">PERMANEN!</strong></flux:heading>
                    <flux:subheading>
                        Apakah Anda yakin ingin menghapus Akun

                        <strong class="text-red-700 dark:text-red-400"
                            x-text="$store.user?.label_id1_delete ? '***' + $store.user?.role_delete + ' ' + $store.user?.label_id1_delete + '***' : '***Pengguna ini***'">
                        </strong>?
                        <span x-show="$store.user?.isForceDelete">
                            Tindakan ini tidak dapat dibatalkan!
                        </span>
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

                    <flux:button wire:click="destroyUser" wire:loading.attr="disabled"
                        wire:target="deleteUser, destroyUser" type="submit" variant="primary"
                        class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 active:bg-red-800 border-none transition-colors duration-200">
                        <span wire:loading.remove wire:target="destroyUser">
                            Ya, Hapus
                            <strong x-text="$store.user?.role_delete">
                            </strong>?
                        </span>

                        <span wire:loading wire:target="destroyUser">
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
