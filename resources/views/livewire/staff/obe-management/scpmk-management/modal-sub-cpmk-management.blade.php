<div>
    <flux:modal name="scpmk-modal" wire:model.live="showSCPMKModal" x-data :flyout="!!$parent"
        wire:key="scpmk-modal-{{ $parent }}" @refresh-data-scpmk.window="$store.scpmk.reset()"
        class="modal-flux md:w-[90vw] max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveSCPMK, updateSCPMK',
        ])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">

                    {{-- 1. Header Modal --}}
                    <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                        <h3 class="text-xl font-semibold">

                            <flux:badge icon="academic-cap" color="fuchsia" size="lg">
                                <span
                                    x-text="$store.scpmk?.isEdit ? 'Edit OBE - Sub-CPMK' : 'Tambah OBE - Sub-CPMK'"></span>
                            </flux:badge>

                        </h3>
                    </div>

                    {{-- 2. Konten & Form --}}
                    <div class="modal-flux-body">
                        <form
                            x-on:submit.prevent="$store.scpmk.isEdit ? $wire.updateSCPMK($store.scpmk.getDataSCPMK()) : $wire.saveSCPMK($store.scpmk.getDataSCPMK())"
                            enctype="multipart/form-data" id="scpmkForm">

                            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-input')

                            <div class="form-message-container">

                                <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                    @include(
                                        'livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-message-form',
                                        ['show' => $showSCPMKModal]
                                    )
                                    @include('livewire.global.modal-form.footer.button-form', [
                                        'targetX' => 'addSCPMK, saveSCPMK, editSCPMK, updateSCPMK',
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
