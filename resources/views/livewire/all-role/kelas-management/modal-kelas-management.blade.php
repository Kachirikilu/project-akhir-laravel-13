<div>
    <flux:modal name="kelas-modal" wire:model.live="showKelasModal" x-data
        @refresh-data-kelas.window="$store.kelas.reset()" wire:key="kelas-modal"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveKelas, updateKelas',
        ])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="academic-cap" color="emerald" size="lg">
                            <span x-text="$store.kelas?.isEdit ? 'Edit Kelas' : 'Tambah Kelas'"></span>
                        </flux:badge>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
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
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>
    </flux:modal>

</div>
