<div>
    <flux:modal name="prodi-modal" wire:model.live="showProdiModal" x-data @refresh-data-pr.window="$store.prodi.reset()"
        class="modal-flux md:w-3xl max-w-4xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveProdi, updateProdi',
        ])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">


                    <h3 class="text-xl font-semibold">

                        <template x-if="$store.prodi?.typeModal == 'prodi'" x-cloak>
                            <flux:badge icon="academic-cap" color="emerald" size="lg">
                                <span
                                    x-text="$store.prodi?.isEdit ? 'Edit Program Studi' : 'Tambah Program Studi'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.prodi?.typeModal == 'departemen'" x-cloak>
                            <flux:badge icon="book-open" color="amber" size="lg">
                                <span x-text="$store.prodi?.isEdit ? 'Edit Departemen' : 'Tambah Departemen'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.prodi?.typeModal == 'fakultas'" x-cloak>
                            <flux:badge icon="building-library" color="indigo" size="lg">
                                <span x-text="$store.prodi?.isEdit ? 'Edit Fakultas' : 'Tambah Fakultas'"></span>
                            </flux:badge>
                        </template>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">

                    <form
                        x-on:submit.prevent="$store.prodi.isEdit ? $wire.updateProdi($store.prodi.getDataProdi()) : $wire.saveProdi($store.prodi.getDataProdi())"
                        enctype="multipart/form-data" id="prodiForm">

                        <template x-if="$store.prodi?.typeModal == 'prodi'" x-cloak>
                            @include('livewire.admin.prodi-management.prodi-modal-form.prodi-input')
                        </template>

                        <template x-if="$store.prodi?.typeModal == 'departemen'" x-cloak>
                            @include('livewire.admin.prodi-management.prodi-modal-form.departemen-input')
                        </template>

                        <template x-if="$store.prodi?.typeModal == 'fakultas'" x-cloak>
                            @include('livewire.admin.prodi-management.prodi-modal-form.fakultas-input')
                        </template>

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.admin.prodi-management.prodi-modal-form.prodi-message-form')
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => $prodiType,
                                    'targetX' => 'addProdi, saveProdi, editProdi, updateProdi',
                                    'isLeft' => 0,
                                ])
                            </div>
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-skeleton')
            @endif
        </div>

    </flux:modal>
</div>
