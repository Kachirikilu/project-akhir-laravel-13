{{-- ****************************************************** --}}
{{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Lainnya</h4>

    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Nomor Telepon',
            'isRequired' => 0,
        ])
        <div class="grid grid-cols-12 gap-1">

            <div class="col-span-3 sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'user',
                    'noLabel' => 1,
                    'modelString' => 'kode_no_hp',
                    'valueString' => '+62',
                    'iconString' => 'phone',
                ])
            </div>

            <div class="col-span-9 sm:col-span-10">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'user',
                    'noLabel' => 1,
                    'modelString' => 'no_hp_back',
                    'isNoHP' => 1,
                    // 'numberOnly' => 1,
                    // 'maxLenght' => 8,
                    'iconString' => 'device-phone-mobile',
                    'placeholder' => 'Contoh: 898 - 5655 - 826',
                    'isFocusSelect' => 1,
                ])
            </div>


        </div>
        @error('no_hp')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('no_hp') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'modelString' => 'tempat_lahir',
        'iconString' => 'map-pin',
        'placeholder' => 'Contoh: Kota Palembang',
        'isRequired' => 0,
        'message' => $errors->first('tempat_lahir'),
    ])

    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'modelString' => 'tanggal_lahir',
        'iconString' => 'calendar',
        'placeholder' => 'Contoh: Kota Palembang',
        'isDate' => 1,
        'isRequired' => 0,
        'message' => $errors->first('tanggal_lahir'),
    ])



</div>
