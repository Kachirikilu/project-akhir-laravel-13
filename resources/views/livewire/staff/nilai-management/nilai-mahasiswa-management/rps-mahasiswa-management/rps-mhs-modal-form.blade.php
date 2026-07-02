<flux:modal name="nilai-modal" wire:model="showEditNilai" x-data
    @refresh-data-nilai.window="$store.nilai?.reset()"
    class="w-full md:w-4xl max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="updateNilaiMahasiswa">
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
                    <span>Nilai Mahasiswa</span>
                </flux:badge>

            </h3>
        </div>

        {{-- 2. Konten & Form --}}
        <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">
            <form @if (Auth::user()->admin || Auth::user()->dosen) x-on:submit.prevent="$wire.updateNilaiMahasiswa($store.nilai)" enctype="multipart/form-data" id="nilaiForm" @endif>

                @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input')

                @if (Auth::user()->admin || Auth::user()->dosen)
                <div
                    class="form-message-container">
                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-message-form')
                        @include('livewire.global.modal-form.footer.button-form', [
                            'targetX' => 'updateNilai',
                            'isLeft' => 0,
                        ])
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>
</flux:modal>
