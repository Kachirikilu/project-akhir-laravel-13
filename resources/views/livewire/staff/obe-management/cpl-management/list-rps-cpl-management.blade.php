<div>
    <flux:modal name="cpl-rps-modal" wire:model.live="showCPLRPSModal" flyout wire:key="cpl-rps-modal-flyout"
        wire:ignore.self
        class="w-full md:w-3xl max-w-4xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">
        @if ($isReady)
            {{-- Loading Overlay --}}
            <div wire:loading wire:target="saveCPL, updateCPL">
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
                        <flux:badge icon="clipboard-document-list" color="sky" size="lg">
                            <span x-text="'Rencana Pembelajaran Semester - CPL'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
                    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-rps')
                    @include('livewire.staff.obe-management.obe-partial.rps-list', [
                        'alpine' => 'cpl',
                        'rps_items_list' => $cpl_rps_items_list,
                        'rps_modal_paginator' => $cpl_rps_modal_paginator,
                        'nameXString' => 'CPL',
                        'parent' => 'tim-dosen-rps',
                        'isFlyout' => false,
                    ])
                    @include('livewire.global.modal-form.footer.button-close')
                </div>

            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-full-skeleton')
        @endif
    </flux:modal>
</div>
