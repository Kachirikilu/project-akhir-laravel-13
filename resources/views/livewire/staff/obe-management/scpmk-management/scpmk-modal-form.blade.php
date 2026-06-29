<flux:modal {{-- :flyout="$isFlyoutSCPMK" wire:key="scpmk-modal-{{ $isFlyoutSCPMK }}" --}} name="scpmk-modal" wire:model.live="showSCPMKModal" x-data
    @refresh-data-scpmk.window="$store.scpmk.reset()"
    class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="flex flex-col h-full relative">


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
        <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
            <form x-on:submit.prevent="$store.scpmk.isEdit ? $wire.updateSCPMK($store.scpmk) : $wire.saveSCPMK($store.scpmk)"
                enctype="multipart/form-data" id="scpmkForm">

                @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-input')

                <div
                    class="form-message-container">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include(
                            'livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-message-form',
                            ['show' => $showSCPMKModal]
                        )
                        @include('livewire.global.modal-form.footer.button-form', [
                            'targetX' => 'addSCPMK, saveSCPMK, editSCPMK, updateSCPMK',
                            'isLeft' => 0
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

</flux:modal>
