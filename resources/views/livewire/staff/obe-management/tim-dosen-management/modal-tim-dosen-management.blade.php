<div>
    <flux:modal name="tim-dosen-modal" wire:model.live="showTimDosenModal" x-data :flyout="!!$parent"
        wire:key="tim-dosen-modal-{{ $parent }}" @refresh-data-tim-dosen.window="$store.tim_dosen.reset()"
        class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            <div class="flex flex-col h-full relative">

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                    <h3 class="text-xl font-semibold">

                        <flux:badge icon="clipboard-document-list" color="blue" size="lg">
                            <span
                                x-text="$store.tim_dosen?.isEdit ? 'Edit OBE - Tim Dosen' : 'Tambah OBE - Tim Dosen'"></span>
                        </flux:badge>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
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
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-full-skeleton')
        @endif
    </flux:modal>
</div>
