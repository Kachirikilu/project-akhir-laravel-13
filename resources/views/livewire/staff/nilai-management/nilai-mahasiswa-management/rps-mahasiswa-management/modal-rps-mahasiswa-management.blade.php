<div>
    <flux:modal name="rps-mahasiswa-modal" wire:model.live="showEditNilai" x-data wire:key="rps-mahasiswa-modal"
        @refresh-data-rps-mahasiswa.window="$store.nilai?.reset()"
        class="modal-flux md:w-4xl max-w-5xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'updateNilaiMahasiswa'])

        <div class="modal-flux-main scrollbar-large">
            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="academic-cap" color="emerald" size="lg">
                            <span>Nilai Mahasiswa</span>
                        </flux:badge>
                    </h3>
                </div>

                {{-- 2. Konten & Form --}}
                <div class="modal-flux-body">
                    <form
                        @if (Auth::user()->admin || Auth::user()->dosen) x-on:submit.prevent="$wire.updateNilaiMahasiswa($store.nilai.getDataNilai())" enctype="multipart/form-data" id="nilaiForm" @endif>

                        @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form.rps-mhs-input')

                        @if (Auth::user()->admin || Auth::user()->dosen)
                            <div class="form-message-container">
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
            @else
                @include('livewire.global.livewire-skeletons.modal-full-skeleton')
            @endif
        </div>
    </flux:modal>


</div>
