<flux:modal
    {{-- :flyout="$isFlyoutCPMK" wire:key="cpmk-modal-{{ $isFlyoutCPMK }}" --}}
    name="cpmk-modal" wire:model.live="showCPMKModal" x-data
    @refresh-data-cpmk.window="$store.cpmk.reset()"
    class="md:w-[90vw] max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    <div class="flex flex-col h-full relative">


        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="academic-cap" color="amber" size="lg">
                    <span
                        x-text="$store.cpmk?.isEdit ? 'Edit OBE - Capaian Pembelajaran Mata Kuliah' : 'Tambah OBE - Capaian Pembelajaran Mata Kuliah'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingCPMK ? 'updateCPMK' : 'saveCPMK' }}($store.cpmk)"
                enctype="multipart/form-data" id="cpmkForm">

                @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-input')

                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include(
                            'livewire.staff.obe-management.cpmk-management.cpmk-modal-form.cpmk-message-form',
                            ['show' => $showCPMKModal]
                        )
                        @include('livewire.global.modal-form.button-form', [
                            'targetX' => 'addCPMK, saveCPMK, editCPMK, updateCPMK',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

</flux:modal>
