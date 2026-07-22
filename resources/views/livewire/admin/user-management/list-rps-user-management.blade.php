<div>
    <flux:modal name="user-rps-modal" wire:model.live="showUserRPSModal" flyout wire:key="user-rps-modal"
        class="modal-flux md:w-3xl max-w-4xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        <div class="modal-flux-main scrollbar-large">

            @if ($isReady)
                <div class="modal-flux-head">

                    <h3 class="text-xl font-semibold">
                        <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
                            <flux:badge icon="cog-6-tooth" color="lime" size="lg">
                                <span>Rencana Pembelajaran Semester - Dosen</span>
                            </flux:badge>
                        </template>
                        <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
                            <flux:badge icon="cog-6-tooth" color="cyan" size="lg">
                                <span>Rencana Pembelajaran Semester - Mahasiswa</span>
                            </flux:badge>
                        </template>
                    </h3>
                </div>

                <div class="modal-flux-body">
                    @include('livewire.admin.user-management.user-modal-form.user-rps')

                    @include('livewire.staff.obe-management.obe-partial.rps-list', [
                        'alpine' => 'user',
                        'rps_items_list' => $user_rps_items_list,
                        'rps_modal_paginator' => $user_rps_modal_paginator,
                        'nameXString' => strtoupper($roleType),
                        'wireLoading' => 'editUser',
                        'parent' => 'user-rps',
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
