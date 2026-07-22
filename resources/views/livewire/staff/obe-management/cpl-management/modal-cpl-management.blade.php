<div>
    <flux:modal name="cpl-modal" wire:model.live="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()"
        :flyout="!!$parent" wire:key="cpl-modal-{{ $parent }}"
        class="modal-flux md:w-[90vw] max-w-3xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'saveCPL, updateCPL'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                {{-- 1. Header Modal --}}
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <template x-if="$store.cpl?.typeModal == '1'" x-cloak>
                            <flux:badge icon="academic-cap" color="sky" size="lg">
                                <span
                                    x-text="$store.cpl?.isEdit ? 'Edit CPL - Program Studi' : 'Tambah CPL - Program Studi'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.cpl?.typeModal == 2" x-cloak>
                            <flux:badge icon="book-open" color="sky" size="lg">
                                <span
                                    x-text="$store.cpl?.isEdit ? 'Edit CPL - Departemen' : 'Tambah CPL - Departemen'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.cpl?.typeModal == 3" x-cloak>
                            <flux:badge icon="building-library" color="sky" size="lg">
                                <span
                                    x-text="$store.cpl?.isEdit ? 'Edit CPL - Fakultas' : 'Tambah CPL - Fakultas'"></span>
                            </flux:badge>
                        </template>

                        <template x-if="$store.cpl?.typeModal == 4" x-cloak>
                            <flux:badge icon="globe-alt" color="sky" size="lg">
                                <span
                                    x-text="$store.cpl?.isEdit ? 'Edit CPL - Universitas' : 'Tambah CPL - Universitas'"></span>
                            </flux:badge>
                        </template>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">

                    <form
                        x-on:submit.prevent="$store.cpl.isEdit ? $wire.updateCPL($store.cpl.getDataCPL()) : $wire.saveCPL($store.cpl.getDataCPL())"
                        enctype="multipart/form-data" id="cplForm">

                        @include('livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-input')

                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include(
                                    'livewire.staff.obe-management.cpl-management.cpl-modal-form.cpl-message-form',
                                    ['show' => $showCPLModal]
                                )
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addCPL, saveCPL, editCPL, updateCPL',
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
