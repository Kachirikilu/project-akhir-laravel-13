<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Kelas Perkuliahan</h4>


    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode Jadwal Kelas' . ($isSesi ? ' (Terkunci)' : ''),
            'isRequired' => !$isSesi,
        ])
        <div class="grid grid-cols-12 gap-1 sm:gap-2 items-end" x-data="{
            getSuffix(tahun) {
                let ta = parseInt(tahun);
                if (!ta) return '';
        
                if (ta >= 3000) return String(ta);
                if (ta >= 2100) return String(ta).slice(-3);
                if (ta >= 2000) return String(ta).slice(-2);
                return String(ta);
            }
        }"
            x-effect="
                if ($store.jadwal) {
                    let label = $store.jadwal.label_kelas;
                    let wilayah = $store.jadwal.kode_wilayah;

                    if (!label || !wilayah) {
                        $store.jadwal.label_wilayah = '';
                    } else {
                        $store.jadwal.label_wilayah = label + '-' + wilayah;
                    }

                    let tahun = $store.jadwal.sesi_1 || $store.jadwal.tanggal_mulai_fix;

                    if (!tahun) {
                        $store.jadwal.digit_tahun = '';
                        $store.jadwal.digit_tahun_old = ''; 
                    } else {
                        let suffix = getSuffix(tahun);
                        $store.jadwal.digit_tahun = suffix;
                        if (!$store.jadwal.digit_tahun_old) {
                            $store.jadwal.digit_tahun_old = suffix;
                        }
                    }
                }
            ">
            <div class="col-span-12 sm:col-span-5">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'jadwal',
                    'noLabel' => 1,
                    'modelString' => 'kode_kelas',
                    'valueString' => $kode_kelas ?? null,
                    'placeholder' => '---',
                    'iconString' => 'rectangle-group',
                ])
            </div>


            <div class="col-span-8 sm:col-span-4">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'jadwal',
                    'modelString' => 'label_wilayah',
                    'placeholder' => '--',
                    'iconString' => 'variable',
                    'noLabel' => 1,
                ])
            </div>
            <div class="col-span-4 sm:col-span-3">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'jadwal',
                    'modelString' => 'digit_tahun',
                    'placeholder' => '--',
                    'iconString' => 'variable',
                    'noLabel' => 1,
                ])
            </div>

        </div>
        @error('kode_kelas')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_kelas') }}</span>
        @enderror
    </div>

    <div class="grid sm:grid-cols-4 gap-1 sm:gap-3 items-start">
        <div class="sm:col-span-2">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'jadwal',
                // 'noLabel' => 1,
                'nameXString' => 'Label Kelas'. ($isSesi ? ' (Terkunci)' : ''),
                'modelString' => 'label_kelas',
                'iconString' => 'presentation-chart-bar',
                'placeholder' => 'Contoh: A, B, C',
                'isKode' => 1,
                'isFocusSelect' => 1,
                'isReadonly' => $isSesi,
                'isRequired' => !$isSesi,
                'message' => $errors->first('label_kelas'),
            ])
        </div>
        <div class="sm:col-span-2">
            @if ($isSesi)
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Kode Wilayah (Terkunci)',
                    'modelString' => 'kode_wilayah',
                    'iconString' => 'map-pin',
                    'placeholder' => 'Pilih Kode Wilayah...',
                    'isFocusSelect' => 1,
                    'isReadonly' => $isSesi,
                    'isRequired' => !$isSesi,
                    'message' => $errors->first('label_kelas'),
                ])
            @else
                @include('livewire.global.modal-form.select-form', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Kode Wilayah',
                    'modelString' => 'kode_wilayah',
                    'xOptions' => ['IDL (Kampus Indralaya)', 'PLG (Kampus Bukit)'],
                    'xValues' => ['IDL', 'PLG'],
                    'iconString' => 'map-pin',
                    'placeholder' => 'Pilih Kode Wilayah...',
                    'message' => $errors->first('kode_wilayah'),
                ])
            @endif
        </div>
    </div>

    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'jadwal',
        'modelString' => 'password',
        'iconString' => 'lock-closed',
        'placeholder' => 'Contoh: AIDL22',
        'isRequired' => 0,
        'message' => $errors->first('password'),
    ])

    <div x-data x-init="$watch('$store.jadwal?.kode_jadwal', value => console.log('kode_jadwal: ', value))"></div>
</div>
