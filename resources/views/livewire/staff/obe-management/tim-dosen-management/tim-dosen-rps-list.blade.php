<flux:modal name="tim-dosen-rps-modal" wire:model.live="showTimDosenRPSModal" flyout wire:key="tim-dosen-rps-modal-flyout" 
    class="w-full md:w-3xl max-w-4xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveTimDosen, updateTimDosen">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-[var(--contrast-second-text)] italic">Menyinkronkan...</p>
        </div>
    </div>


    <div class="flex flex-col h-full">

        {{-- 1. Header Modal (Tetap di Atas) --}}
        <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">
                    <flux:badge icon="cog-6-tooth" color="lime" size="lg">
                        <span>Rencana Pembelajaran Semester - Tim Dosen</span>
                    </flux:badge>
            </h3>
        </div>

        <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
            @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-rps')

            @include('livewire.staff.obe-management.obe-partial.rps-list', [
                'alpine' => 'tim_dosen',
                'rps_items_list' => $tim_dosen_rps_items_list,
                'rps_modal_paginator' => $tim_dosen_rps_modal_paginator,
                'nameXString' => 'Tim Dosen',
                'wireLoading' => 'editTimDosen',
            ])
        </div>

    </div>

</flux:modal>
