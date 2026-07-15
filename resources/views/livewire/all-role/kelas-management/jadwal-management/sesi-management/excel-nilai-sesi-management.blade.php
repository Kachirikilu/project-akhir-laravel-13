<div>
    <flux:modal name="nilai-excel-modal" wire:model.live="showNilaiExcelModal" flyout wire:key="nilai-excel-modal-flyout"
        @refresh-data-rps-mahasiswa.window="if (!$wire.showNilaiExcelModal) $store.sesi.reset()"
        class="w-full md:w-screen-2xl max-w-screen-2xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm no-scrollbar">
        @if ($isReady)
            {{-- Loading Overlay --}}
            <div wire:loading wire:target="saveNilaiExcel">
                <div
                    class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
                    <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
                    <p wire:stream="import-progress" class="mt-4 text-sm font-medium text-[var(--contrast-second-text)] italic">
                        Menyinkronkan...
                    </p>
                </div>
            </div>

            <div class="flex flex-col h-full">

                <div class="md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

                    <h3 class="text-xl font-semibold">
                        <flux:badge icon="cog-6-tooth" color="green" size="lg">
                            <span>Input Nilai Mahasiswa - Excel</span>
                        </flux:badge>
                    </h3>
                </div>

                <div class="flex-1 overflow-y-auto sm:p-6 py-6 scrollbar-large">

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
