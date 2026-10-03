<div>
    <flux:modal name="user-antrian-modal" wire:model.live="showUserAntrianModal" :flyout="!!$parent"
        wire:key="user-antrian-modal-{{ $parent }}" @refresh-data-user-antrian.window="$store.user.reset_antrian()"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addAntrianUser, saveAntrianUser'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal (Tetap di Atas) --}}
                <div class="modal-flux-header">

                    <h3 class="text-xl font-semibold">
                            <flux:badge icon="queue-list" color="yellow" size="lg">
                                <span>Input Pengguna - List Antrian</span>
                            </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                <div class="modal-flux-body">

                    <form wire:submit.prevent="saveUserAntrian" enctype="multipart/form-data" id="userForm">

                        @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrian-input')

                        {{-- 3. Footer/Tombol --}}
                        <div class="form-message-container">
                                @include('livewire.admin.user-management.user-modal.user-modal-partial.user-message-form')

                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => $roleType,
                                    'targetX' => 'addAntrianUser, saveAntrianUser',
                                    'isLeft' => 0,
                                    'textButton' => 'Daftar Antrian',
                                ])
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
