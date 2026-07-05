<div>
    <flux:modal name="rps-modal" wire:model="showRPSModal" x-data :flyout="!!$isFlyout"
        wire:key="rps-modal-{{ $parent }}" @refresh-data-rps.window="$store.rps.reset()"
        class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">
        <div class="flex flex-col h-full relative">

            {{-- 1. Header Modal --}}
            <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                <h3 class="text-xl font-semibold">

                    <flux:badge icon="clipboard-document-list" color="green" size="lg">
                        <span x-text="$store.rps?.isEdit ? 'Edit OBE - RPS' : 'Tambah OBE - RPS'"></span>
                    </flux:badge>

                </h3>
            </div>

            {{-- 2. Konten & Form --}}
            <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
                <form x-on:submit.prevent="$store.rps.isEdit ? $wire.updateRPS($store.rps.getDataRPS()) : $wire.saveRPS($store.rps.getDataRPS())"
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
        </div>

    </flux:modal>
</div>
