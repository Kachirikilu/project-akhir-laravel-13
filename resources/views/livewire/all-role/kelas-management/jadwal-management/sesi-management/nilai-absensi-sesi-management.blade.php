<div>
    <flux:modal name="nilai-absensi-modal" wire:model.live="showMahasiswaAbsen" x-data
        @refresh-data-sesi.window="$store.sesi.reset()" wire:key="nilai-absensi-sesi-modal"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'editNilaiAbsensi, updateNilaiAbsensi'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="academic-cap" color="emerald" size="lg">
                            <span>Nilai & Absensi Mahasiswa</span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form x-on:submit.prevent="$wire.updateNilaiAbsensi($store.sesi.getDataAbsensi)"
                        enctype="multipart/form-data" id="sesiForm">

                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-input')

                        <div class="form-message-container">
                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form.absensi-message-form')
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'editNilaiAbsensi, updateNilaiAbsensi',
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
