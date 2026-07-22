<div>
    <flux:modal name="ref-modal" wire:model.live="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()"
        :flyout="!!$parent" wire:key="ref-modal-{{ $parent }}"
        class="modal-flux md:w-[90vw] max-w-3xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveRef, updateRef'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="clipboard-document-list" color="orange" size="lg">
                            <span
                                x-text="$store.ref?.isEdit ? 'Edit OBE - Referensi' : 'Tambah OBE - Referensi'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        x-on:submit.prevent="$store.ref.isEdit ? $wire.updateRef($store.ref.getDataRef()) : $wire.saveRef($store.ref.getDataRef())"
                        enctype="multipart/form-data" id="refForm">

                        @include('livewire.staff.obe-management.ref-management.ref-modal-form.ref-input')

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include(
                                    'livewire.staff.obe-management.ref-management.ref-modal-form.ref-message-form',
                                    ['show' => $showRefModal]
                                )
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addRef, saveRef, editRef, updateRef',
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
