<div>
    <flux:modal name="tim-dosen-rps-modal" wire:model.live="showTimDosenRPSModal" flyout
        wire:key="tim-dosen-rps-modal-flyout" wire:ignore.self
        class="modal-flux md:w-3xl max-w-4xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal (Tetap di Atas) --}}
                <div class="modal-flux-head">

                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="cog-6-tooth" color="lime" size="lg">
                            <span>Rencana Pembelajaran Semester - Tim Dosen</span>
                        </flux:badge>
                    </h3>
                </div>

                <div class="modal-flux-body">
                    @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-rps')

                    @include('livewire.staff.obe-management.obe-partial.rps-list', [
                        'alpine' => 'tim_dosen',
                        'rps_items_list' => $tim_dosen_rps_items_list,
                        'rps_modal_paginator' => $tim_dosen_rps_modal_paginator,
                        'nameXString' => 'Tim Dosen',
                        'wireLoading' => 'editTimDosen',
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
