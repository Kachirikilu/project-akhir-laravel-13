<div>
    <flux:modal name="rps-modal" wire:model.live="showRPSModal" x-data :flyout="!!$isFlyout"
        wire:key="rps-modal-{{ $parent }}" @refresh-data-rps.window="$store.rps.reset()"
        class="modal-flux md:w-[90vw] max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveRPS, updateRPS'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="clipboard-document-list" color="green" size="lg">
                            <span x-text="$store.rps?.isEdit ? 'Edit OBE - RPS' : 'Tambah OBE - RPS'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        x-on:submit.prevent="$store.rps.isEdit ? $wire.updateRPS($store.rps.getDataRPS()) : $wire.saveRPS($store.rps.getDataRPS())"
                        enctype="multipart/form-data" id="rpsForm">

                        @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-input')

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include(
                                    'livewire.staff.obe-management.rps-management.rps-modal-form.rps-message-form',
                                    ['show' => $showRPSModal]
                                )
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addRPS, saveRPS, editRPS, updateRPS',
                                    'isLeft' => 0,
                                ])
                            </div>
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
