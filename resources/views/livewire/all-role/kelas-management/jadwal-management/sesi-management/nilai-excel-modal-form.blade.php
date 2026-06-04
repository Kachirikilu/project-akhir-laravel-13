<flux:modal name="nilai-excel-modal" wire:model="showNilaiExcelModal" flyout
    @refresh-data-nilai.window="if (!$wire.showNilaiExcelModal) $store.sesi.reset()"
    class="md:w-[98vw] lg:max-w-[95vw] xl:max-w-[90vw] h-[98vh] !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="saveNilaiExcel">
        <div
            class="absolute inset-0 z-50 bg-[var(--second-table-color)]/60 backdrop-blur-[2px] flex flex-col items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
            <p wire:stream="import-progress" class="mt-4 text-sm font-medium text-gray-600 italic">Menyinkronkan...</p>
        </div>
    </div>

    <div class="flex flex-col h-full">

        <div class="sm:px-2 md:px-4 lg:px-6 py-6 pb-4 border-b border-[var(--contrast-second-text)]">

            <h3 class="text-xl font-semibold">
                <flux:badge icon="cog-6-tooth" color="green" size="lg">
                    <span>Input Nilai Mahasiswa - Excel</span>
                </flux:badge>
            </h3>
        </div>

        <div class="flex-1 overflow-y-auto p-6 scrollbar-large">

            <form wire:submit.prevent="saveNilaiExcel" enctype="multipart/form-data" id="nilaiForm">

                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.nilai-modal-form.nilai-excel-input')

                {{-- 3. Footer/Tombol --}}
                <div
                    class="bg-[var(--sub-table-color)] border-[var(--border-table-color)] p-4 mt-4 rounded-lg gap-4 shadow-sm border-t transition-colors duration-300">

                    <div class="flex-1 text-xs text-[var(--second-text)] space-y-3">
                        {{-- @include('livewire.admin.nilai-management.nilai-modal-form.nilai-message-form') --}}

                        @include('livewire.global.modal-form.footer.button-form', [
                            'xType' => 'excel',
                            'wireLoading' => 'excel_nilai_file, parseExcelNilaiFile, procesImportNilaiExcel',
                            'wireLoading2' => 'saveNilaiExcel',
                            'targetX' => 'addNilai, saveNilai, editNilai, updateNilai',
                            'isLeft' => 1,
                        ])
                    </div>

                </div>
            </form>
        </div>

    </div>

</flux:modal>
