<div>
    <flux:modal :flyout="$isFlyoutUser" name="user-modal" wire:model.live="showUserModal"
        @refresh-data-user.window="if (!$wire.showUserModal) $store.user.reset()"
        class="w-full md:w-3xl max-w-4xl h-[98vh] !p-4 sm:!px-6 md:!px-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
        {{-- Loading Overlay --}}
        <div wire:loading wire:target="saveUser, updateUser">
            <div
                class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
                <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
            </div>
        </div>


        <div class="flex flex-col h-full">

            {{-- 1. Header Modal (Tetap di Atas) --}}
            <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                <h3 class="text-xl font-semibold">
                    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
                        <flux:badge icon="cog-6-tooth" color="red" size="lg">
                            <span
                                x-text="$store.user?.isEdit ? 'Edit Pengguna - Admin' : 'Tambah Pengguna - Admin'"></span>
                        </flux:badge>
                    </template>
                    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
                        <flux:badge icon="cog-6-tooth" color="lime" size="lg">
                            <span
                                x-text="$store.user?.isEdit ? 'Edit Pengguna - Dosen' : 'Tambah Pengguna - Dosen'"></span>
                        </flux:badge>
                    </template>
                    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
                        <flux:badge icon="cog-6-tooth" color="cyan" size="lg">
                            <span
                                x-text="$store.user?.isEdit ? 'Edit Pengguna - Mahasiswa' : 'Tambah Pengguna - Mahasiswa'"></span>
                        </flux:badge>
                    </template>
                </h3>
            </div>

            {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
            <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">

                <form
                    x-on:submit.prevent="$store.user.isEdit ? $wire.updateUser($store.user.getData()) : $wire.saveUser($store.user.getData())"
                    enctype="multipart/form-data" id="userForm">

                    @include('livewire.admin.user-management.user-modal-form.user-input')

                    {{-- 3. Footer/Tombol --}}
                    <div class="form-message-container">

                        <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                            @include('livewire.admin.user-management.user-modal-form.user-message-form')

                            @include('livewire.global.modal-form.footer.button-form', [
                                'xType' => $roleType,
                                'targetX' => 'addUser, saveUser, editUser, updateUser',
                                'isLeft' => 0,
                            ])
                        </div>

                    </div>
                </form>
            </div>

        </div>

    </flux:modal>
</div>
