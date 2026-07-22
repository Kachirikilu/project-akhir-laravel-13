<div>
    <flux:modal name="nilai-excel-modal" wire:model.live="showNilaiExcelModal" flyout wire:key="nilai-excel-modal-flyout"
        @refresh-data-rps-mahasiswa.window="if (!$wire.showNilaiExcelModal) $store.sesi.reset()"
        class="modal-flux md:w-screen-2xl max-w-screen-2xl !p-0 !bg-[var(--second-pop-up-color)] no-scrollbar">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'saveNilaiExcel',
            'stream' => 'import-progress',
        ])

        <div class="modal-flux-main scrollbar-large">

            @if ($isReady)
                <div class="modal-flux-header">
                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="cog-6-tooth" color="green" size="lg">
                            <span>Input Nilai Mahasiswa - Excel</span>
                        </flux:badge>
                    </h3>
                </div>

                <div class="modal-flux-body">
                    <form wire:submit.prevent="saveNilaiExcel" enctype="multipart/form-data" id="nilaiForm">
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.nilai-modal-form.nilai-excel-input')
                        {{-- 3. Footer/Tombol --}}
                        <div class="form-message-container">

                            <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                                {{-- @include('livewire.admin.nilai-management.nilai-modal-form.nilai-message-form') --}}
                                @include('livewire.global.modal-form.footer.button-form', [
                                    'xType' => 'excel',
                                    'wireLoading' =>
                                        'excel_nilai_file, parseExcelNilaiFile, procesImportNilaiExcel',
                                    'wireLoading2' => 'saveNilaiExcel',
                                    'targetX' => 'addNilai, saveNilai, editNilai, updateNilai',
                                    'isLeft' => 1,
                                    'mt' => '',
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
