<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Capaian Pembelajaran Mata Kuliah</h4>



    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode CPMK',
        ])
        <div class="grid grid-cols-6 gap-2 sm:gap-2 items-end" x-data="{}"
            x-effect="$store.cpmk.kode_cpmk = ($store.cpmk.kode_cpmk_1 || '') + ($store.cpmk.kode_cpmk_2 || '')">

            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpmk',
                    'noLabel' => 1,
                    'modelString' => 'kode_cpmk_1',
                    'iconString' => 'academic-cap',
                    'placeholder' => 'Kode CPMK...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpmk',
                    'noLabel' => 1,
                    'modelString' => 'kode_cpmk_2',
                    'numberOnly' => 1,
                    'maxLength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 1211',
                    'isFocusSelect' => 1,
                ])
            </div>

        </div>
        @error('kode_cpmk')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_cpmk') }}</span>
        @enderror
    </div>

</div>
