<div>
    <flux:modal name="ref-modal" wire:model.live="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()"
        :flyout="!!$parent" wire:key="ref-modal-{{ $parent }}"
        class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            <div class="flex flex-col h-full relative">

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                    <h3 class="text-xl font-semibold">

                        <flux:badge icon="clipboard-document-list" color="orange" size="lg">
                            <span
                                x-text="$store.ref?.isEdit ? 'Edit OBE - Referensi' : 'Tambah OBE - Referensi'"></span>
                        </flux:badge>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
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
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-skeleton')
        @endif
    </flux:modal>
</div>
