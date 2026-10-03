<div>
    <flux:modal name="user-antrean-modal" wire:model.live="showUserAntreanModal" :flyout="!!$parent"
        wire:key="user-antrean-modal-{{ $parent }}" @refresh-data-user-antrean.window="$store.user.reset_antrean()"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addAntreanUser, saveAntreanUser'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal (Tetap di Atas) --}}
                <div class="modal-flux-header">

                    <h3 class="text-xl font-semibold">
                            <flux:badge icon="queue-list" color="yellow" size="lg">
                                <span>Input Pengguna - List Antrean</span>
                            </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                <div class="modal-flux-body">

                    <form wire:submit.prevent="saveUserAntrean" enctype="multipart/form-data" id="userForm">

                        @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input')

                        {{-- 3. Footer/Tombol --}}
                        <div class="form-message-container">
                                @include('livewire.admin.user-management.user-modal.user-modal-partial.user-message-form')

                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => $roleType,
                                    'targetX' => 'addAntreanUser, saveAntreanUser',
                                    'isLeft' => 0,
                                    'textButton' => 'Daftar Antrean',
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
