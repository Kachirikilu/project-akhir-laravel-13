<flux:modal name="sesi-modal" wire:model="showSesiModal" x-data @refresh-data-sesi.window="$store.sesi?.reset()"
    class="sm:w-full md:w-4xl max-w-5xl h-[98vh] !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveSesi, updateSesi">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>

    <div class="flex flex-col h-full relative">

        {{-- 1. Header Modal --}}
        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="academic-cap" color="emerald" size="lg">
                    <span x-text="$store.sesi?.isEdit ? 'Edit Sesi' : 'Tambah Sesi'"></span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.{{ $isEditingSesi ? 'updateSesi' : 'saveSesi' }}($store.sesi)"
                enctype="multipart/form-data" id="sesiForm">

                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-input')

                {{-- 3. Footer / Button Action --}}
                <div
                    class="bg-[var(--sub-table-color)] table-border
                    p-4 mt-4 rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-modal-form.sesi-message-form')
                        @include('livewire.global.modal-form.footer.button-form', [
                            'targetX' => 'addSesi, saveSesi, editSesi, updateSesi',
                            'isLeft' => 0
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
