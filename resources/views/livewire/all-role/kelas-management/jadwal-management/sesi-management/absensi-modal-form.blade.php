<flux:modal name="absensi-modal" wire:model="showMahasiswaAbsen" x-data @refresh-data-sesi.window="$store.sesi?.reset()"
    class="w-full md:w-4xl max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="updateNilaiAbsensi">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>

    <div class="flex flex-col h-full relative">

        {{-- 1. Header Modal --}}
        <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">

                <flux:badge icon="academic-cap" color="emerald" size="lg">
                    <span>Nilai & Absensi Mahasiswa</span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
            <form x-on:submit.prevent="$wire.updateNilaiAbsensi($store.sesi)" enctype="multipart/form-data" id="sesiForm">

                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input')

                <div
                    class="form-message-container">
                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-message-form')
                        @include('livewire.global.modal-form.footer.button-form', [
                            'targetX' => 'editNilaiAbsensi, updateNilaiAbsensi',
                            'isLeft' => 0
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
