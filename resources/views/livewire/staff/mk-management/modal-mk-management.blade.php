<div>
    <flux:modal name="mk-modal" wire:model.live="showMKModal" x-data @refresh-data-mk.window="$store.mk.reset()"
        wire:key="mk-modal"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveMK, updateMK'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">

                    <h3 class="text-xl font-semibold">

                        <template x-if="$store.mk?.typeModal == '1'" x-cloak>
                            <flux:badge icon="academic-cap" color="emerald" size="lg">
                                <span
                                    x-text="$store.mk?.isEdit ? 'Edit Mata Kuliah - Program Studi' : 'Tambah Mata Kuliah - Program Studi'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.mk?.typeModal == 2" x-cloak>
                            <flux:badge icon="book-open" color="amber" size="lg">
                                <span
                                    x-text="$store.mk?.isEdit ? 'Edit Mata Kuliah - Departemen' : 'Tambah Mata Kuliah - Departemen'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.mk?.typeModal == 3" x-cloak>
                            <flux:badge icon="building-library" color="indigo" size="lg">
                                <span
                                    x-text="$store.mk?.isEdit ? 'Edit Mata Kuliah - Fakultas' : 'Tambah Mata Kuliah - Fakultas'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.mk?.typeModal == 4" x-cloak>
                            <flux:badge icon="globe-alt" color="red" size="lg">
                                <span
                                    x-text="$store.mk?.isEdit ? 'Edit Mata Kuliah - Universitas' : 'Tambah Mata Kuliah - Universitas'"></span>
                            </flux:badge>
                        </template>

                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form x-on:submit.prevent="$store.mk.isEdit ? $wire.updateMK($store.mk.getDataMK()) : $wire.saveMK($store.mk.getDataMK())"
                        enctype="multipart/form-data" id="mkForm">

                        @include('livewire.staff.mk-management.mk-modal-form.mk-input')

                        {{-- 3. Footer / Button Action --}}
                        <div class="form-message-container">
                                @include('livewire.staff.mk-management.mk-modal-form.mk-message-form')
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => $mkType,
                                    'targetX' => 'addMK, saveMK, editMK, updateMK',
                                    'isLeft' => 0,
                                ])
                        </div>
                    </form>
                </div>
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>


    </flux:modal>

</div>
