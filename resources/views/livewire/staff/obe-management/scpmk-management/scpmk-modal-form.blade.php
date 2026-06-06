<flux:modal {{-- :flyout="$isFlyoutSCPMK" wire:key="scpmk-modal-{{ $isFlyoutSCPMK }}" --}} name="scpmk-modal" wire:model.live="showSCPMKModal" x-data
    @refresh-data-scpmk.window="$store.scpmk.reset()"
    class="md:w-[90vw] max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    <div class="flex flex-col h-full relative">


        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="academic-cap" color="indigo" size="lg">
                    <span
                        x-text="$store.scpmk?.isEdit ? 'Edit OBE - Sub Capaian Pembelajaran Mata Kuliah' : 'Tambah OBE - Sub Capaian Pembelajaran Mata Kuliah'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingSCPMK ? 'updateSCPMK' : 'saveSCPMK' }}($store.scpmk)"
                enctype="multipart/form-data" id="scpmkForm">

                @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form.scpmk-input')

                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

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
