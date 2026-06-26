<flux:modal {{-- :flyout="$isFlyoutCPMK" wire:key="cpmk-modal-{{ $isFlyoutCPMK }}" --}} name="cpmk-modal" wire:model.live="showCPMKModal" x-data
    @refresh-data-cpmk.window="$store.cpmk.reset()"
    class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

    <div class="flex flex-col h-full relative">


        {{-- 1. Header Modal --}}
        <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="academic-cap" color="violet" size="lg">
                    <span
                        x-text="$store.cpmk?.isEdit ? 'Edit OBE - CPMK' : 'Tambah OBE - CPMK'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingCPMK ? 'updateCPMK' : 'saveCPMK' }}($store.cpmk)"
                enctype="multipart/form-data" id="cpmkForm">

                @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-input')

                <div
                    class="form-message-container">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include(
                            'livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-message-form',
                            ['show' => $showCPMKModal]
                        )
                        @include('livewire.global.modal-form.footer.button-form', [
                            'targetX' => 'addCPMK, saveCPMK, editCPMK, updateCPMK',
                            'isLeft' => 0
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

</flux:modal>
