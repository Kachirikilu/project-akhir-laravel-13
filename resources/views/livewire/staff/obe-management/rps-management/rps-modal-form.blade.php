<flux:modal
    {{-- :flyout="$isFlyoutRPS" wire:key="rps-modal-{{ $isFlyoutRPS }}" --}}
    name="rps-modal" wire:model.live="showRPSModal" x-data
    @refresh-data-rps.window="$store.rps.reset()"
    class="md:w-[90vw] max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">


    <div class="flex flex-col h-full relative">

        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="clipboard-document-list" color="emerald" size="lg">
                    <span
                        x-text="$store.rps?.isEdit ? 'Edit OBE - Rencana Pembelajaran Semester' : 'Tambah OBE - Rencana Pembelajaran Semester'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingRPS ? 'updateRPS' : 'saveRPS' }}($store.rps)"
                enctype="multipart/form-data" id="rpsForm">

                @include('livewire.staff.obe-management.rps-management.rps-modal-form.rps-input')

                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)]
                    p-4 mt-4
                    rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include(
                            'livewire.staff.obe-management.rps-management.rps-modal-form.rps-message-form',
                            ['show' => $showRPSModal]
                        )
                        @include('livewire.global.modal-form.button-form', [
                            'targetX' => 'addRPS, saveRPS, editRPS, updateRPS',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
