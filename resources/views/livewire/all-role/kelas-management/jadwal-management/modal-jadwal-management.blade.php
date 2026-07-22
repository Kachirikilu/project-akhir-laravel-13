<div>
    <flux:modal name="jadwal-modal" wire:model.live="showJadwalModal" x-data
        @refresh-data-jadwal.window="$store.jadwal?.reset()" wire:key="jadwal-modal"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveJadwal, updateJadwal',
        ])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="academic-cap" color="emerald" size="lg">
                            <span x-text="$store.jadwal?.isEdit ? 'Edit Jadwal' : 'Tambah Jadwal'"></span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        x-on:submit.prevent="$store.jadwal.isEdit ? $wire.updateJadwal($store.jadwal.getDataJadwal(), {{ $kelas_id }}) : $wire.saveJadwal($store.jadwal.getDataJadwal(), {{ $kelas_id }})"
                        enctype="multipart/form-data" id="jadwalForm">

                        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-input')

                        {{-- 3. Footer / Button Action --}}
                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form.jadwal-message-form')
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'targetX' => 'addJadwal, saveJadwal, editJadwal, updateJadwal',
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
