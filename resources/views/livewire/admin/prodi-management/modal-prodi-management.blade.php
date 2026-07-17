<div>
    <flux:modal name="prodi-modal" wire:model.live="showProdiModal" x-data @refresh-data-pr.window="$store.prodi.reset()"
        class="w-full md:w-3xl max-w-4xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

        @if ($isReady)
            {{-- Loading Overlay --}}
            <div wire:loading wire:target="saveProdi, updateProdi">
                <div
                    class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
                    <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
                    <p class="mt-4 text-sm font-medium text-[var(--contrast-second-text)] italic">Menyinkronkan...</p>
                </div>
            </div>


            <div class="flex flex-col h-full relative">

                {{-- @php
            $targetLoading = 'editProdi'
        @endphp --}}

                {{-- 1. Header Modal --}}
                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

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
                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
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
            </div>
        @else
            @include('livewire.global.livewire-skeletons.modal-skeleton')
        @endif
    </flux:modal>
</div>
