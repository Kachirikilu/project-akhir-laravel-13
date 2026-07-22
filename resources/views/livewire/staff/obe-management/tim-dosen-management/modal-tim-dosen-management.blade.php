<div>
    <flux:modal name="tim-dosen-modal" wire:model.live="showTimDosenModal" x-data :flyout="!!$parent"
        wire:key="tim-dosen-modal-{{ $parent }}" @refresh-data-tim-dosen.window="$store.tim_dosen.reset()"
        class="modal-flux md:w-[90vw] max-w-3xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveTimDosen, updateTimDosen',
        ])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal --}}
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="clipboard-document-list" color="blue" size="lg">
                            <span
                                x-text="$store.tim_dosen?.isEdit ? 'Edit OBE - Tim Dosen' : 'Tambah OBE - Tim Dosen'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        x-on:submit.prevent="$store.tim_dosen.isEdit ? $wire.updateTimDosen($store.tim_dosen.getDataTimDosen()) : $wire.saveTimDosen($store.tim_dosen.getDataTimDosen())"
                        enctype="multipart/form-data" id="timDosenForm">

                        @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-input')

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include(
                                    'livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form.tim-dosen-message-form',
                                    ['show' => $showTimDosenModal]
                                )
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addTimDosen, saveTimDosen, editTimDosen, updateTimDosen',
                                    'isLeft' => 0,
                                ])
                            </div>
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>
    </flux:modal>
</div>
