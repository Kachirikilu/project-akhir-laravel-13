<div>
    <flux:modal name="user-modal" wire:model.live="showUserModal" :flyout="!!$parent"
        wire:key="user-modal-{{ $parent }}" @refresh-data-user.window="$store.user.reset()"
        class="modal-flux md:w-3xl max-w-4xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveUser, updateUser'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal (Tetap di Atas) --}}
                <div class="modal-flux-header">

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
                <div class="modal-flux-body">

                    <form
                        x-on:submit.prevent="$store.user.isEdit ? $wire.updateUser($store.user.getDataUser()) : $wire.saveUser($store.user.getDataUser())"
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
            @else
                @include('livewire.global.livewire-skeletons.modal-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
