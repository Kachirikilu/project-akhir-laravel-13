<div>
    <flux:modal name="kelas-modal" wire:model.live="showKelasModal" x-data
        @refresh-data-kelas.window="$store.kelas.reset()" wire:key="kelas-modal"
        class="w-full md:w-4xl max-w-5xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            {{-- Loading Overlay --}}
            <div wire:loading wire:target="saveKelas, updateKelas">
                <div
                    class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
                    <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
                    <p class="mt-4 text-sm font-medium text-[var(--contrast-second-text)] italic">Menyinkronkan...</p>
                </div>
            </div>

            <div class="flex flex-col h-full relative">

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                    <h3 class="text-xl font-semibold">

                        <flux:badge icon="academic-cap" color="emerald" size="lg">
                            <span x-text="$store.kelas?.isEdit ? 'Edit Kelas' : 'Tambah Kelas'"></span>
                        </flux:badge>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
                    <form
                        x-on:submit.prevent="$store.kelas.isEdit ? $wire.updateKelas($store.kelas.getDataKelas()) : $wire.saveKelas($store.kelas.getDataKelas())"
                        enctype="multipart/form-data" id="kelasForm">

                        @include('livewire.all-role.kelas-management.kelas-modal-form.kelas-input')

                        {{-- 3. Footer / Button Action --}}
                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.all-role.kelas-management.kelas-modal-form.kelas-message-form')
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addKelas, saveKelas, editKelas, updateKelas',
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
