<div>
    <flux:modal name="cpl-modal" wire:model.live="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()"
        :flyout="!!$parent" wire:key="cpl-modal-{{ $parent }}"
        class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            <div class="flex flex-col h-full relative">

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

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
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
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
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-full-skeleton')
        @endif
    </flux:modal>
</div>
