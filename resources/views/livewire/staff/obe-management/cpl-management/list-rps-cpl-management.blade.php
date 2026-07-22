<div>
    <flux:modal name="cpl-rps-modal" wire:model.live="showCPLRPSModal" flyout wire:key="cpl-rps-modal-flyout"
        wire:ignore.self class="modal-flux md:w-3xl max-w-4xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        <div class="modal-flux-main scrollbar-large">

            @if ($isReady)
                <div class="modal-flux-header">
                    <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">
                        <h3 class="text-xl font-semibold">
                            <flux:badge icon="clipboard-document-list" color="sky" size="lg">
                                <span x-text="'Rencana Pembelajaran Semester - CPL'"></span>
                            </flux:badge>
                        </h3>
                    </div>

                    {{-- 2. Konten Formulir (Bisa di-Scroll) --}}
                    <div class="modal-flux-body">
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
                @else
                    @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
