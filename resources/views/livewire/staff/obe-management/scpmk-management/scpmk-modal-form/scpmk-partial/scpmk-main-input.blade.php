<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Sub Capaian Pembelajaran Mata Kuliah</h4>


    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode Sub-CPMK',
        ])
        <div class="grid grid-cols-6 gap-2 sm:gap-2 items-end" x-data="{}"
            x-effect="$store.scpmk.kode_scpmk = ($store.scpmk.kode_scpmk_1 || '') + ($store.scpmk.kode_scpmk_2 || '')">

            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'scpmk',
                    'noLabel' => 1,
                    'modelString' => 'kode_scpmk_1',
                    'iconString' => 'academic-cap',
                    'placeholder' => 'Masukkan mutu Kode Sub-CPMK...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'scpmk',
                    'noLabel' => 1,
                    'modelString' => 'kode_scpmk_2',
                    'numberOnly' => 1,
                    'maxLength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 121104',
                    'isFocusSelect' => 1,
                ])
            </div>

        </div>
        @error('kode_scpmk')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_scpmk') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'scpmk',
        'nameXString' => 'Deskripsi',
        'modelString' => 'deskripsi',
        'iconString' => 'document-text',
        'placeholder' => 'Masukkan deskripsi ringkas tentang Sub-CPMK...',
        'message' => $errors->first('deskripsi'),
    ])

</div>
