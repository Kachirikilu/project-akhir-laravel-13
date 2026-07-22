<div>
    <flux:modal name="cpmk-modal" wire:model.live="showCPMKModal" x-data :flyout="!!$parent"
        wire:key="cpmk-modal-{{ $parent }}" @refresh-data-cpmk.window="$store.cpmk.reset()"
        class="modal-flux md:w-[90vw] max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveCPMK, updateCPMK'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="academic-cap" color="violet" size="lg">
                            <span x-text="$store.cpmk?.isEdit ? 'Edit OBE - CPMK' : 'Tambah OBE - CPMK'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        x-on:submit.prevent="$store.cpmk.isEdit ? $wire.updateCPMK($store.cpmk.getDataCPMK()) : $wire.saveCPMK($store.cpmk.getDataCPMK())"
                        enctype="multipart/form-data" id="cpmkForm">

                        @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-input')

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include(
                                    'livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-message-form',
                                    ['show' => $showCPMKModal]
                                )
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addCPMK, saveCPMK, editCPMK, updateCPMK',
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
