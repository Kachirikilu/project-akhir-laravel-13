<flux:modal
    {{-- :flyout="$isFlyoutCPL" wire:key="cpl-modal-{{ $isFlyoutCPL }}" --}}
    name="cpl-modal" wire:model.live="showCPLModal" x-data
    @refresh-data-cpl.window="$store.cpl.reset()"
    class="md:w-[90vw] max-w-3xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">


    <div class="flex flex-col h-full relative">


        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="clipboard-document-list" color="red" size="lg">
                    <span
                        x-text="$store.cpl?.isEdit ? 'Edit OBE - Capaian Pembelajaran Lulusan' : 'Tambah OBE - Capaian Pembelajaran Lulusan'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingCPL ? 'updateCPL' : 'saveCPL' }}($store.cpl)"
                enctype="multipart/form-data" id="cplForm">

                @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input')

                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include(
                            'livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-message-form',
                            ['show' => $showCPLModal]
                        )
                        @include('livewire.global.modal-form.button-form', [
                            'targetX' => 'addCPL, saveCPL, editCPL, updateCPL',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

</flux:modal>
