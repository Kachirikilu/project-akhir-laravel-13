<flux:modal name="mk-modal" wire:model="showMKModal" x-data @refresh-data-mk.window="$store.mk.reset()"
    {{-- x-bind:flyout="$store.mk.isEdit == 1" --}}
    class="sm:w-full md:w-4xl max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveMK, updateMK">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>

    <div class="flex flex-col h-full relative">

        {{-- @php
            $targetLoading = 'editMK'
        @endphp --}}

        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

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
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            {{-- Gunakan satu method general, lalu filter di Backend berdasarkan $mkType --}}
            <form x-on:submit.prevent="$wire.{{ $isEditingMK ? 'updateMK' : 'saveMK' }}($store.mk)"
                enctype="multipart/form-data" id="mkForm">

                @include('livewire.staff.mk-management.mk-modal-form.mk-input')

                {{-- 3. Footer / Button Action --}}
                <div
                    class="bg-[var(--sub-table-color)] table-border
                    p-4 mt-4 rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.staff.mk-management.mk-modal-form.mk-message-form')
                        @include('livewire.global.modal-form.footer.button-form', [
                            'xType' => $mkType,
                            'targetX' => 'addMK, saveMK, editMK, updateMK',
                            'isLeft' => 0
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
