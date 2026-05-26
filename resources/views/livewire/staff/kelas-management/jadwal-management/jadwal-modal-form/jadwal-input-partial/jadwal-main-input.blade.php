<div
    class="px-4 py-6 mt-4 
        bg-[var(--main-table-color)] border-[var(--border-table-color)]
        shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Kelas Perkuliahan</h4>

    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'jadwal',
                    'nameXString' => 'Kode Jadwal Kelas',
                    'modelString' => 'kode_kelas',
                    'valueString' => $kelas->kode ?? null,
                    'placeholder' => '---',
                    'iconString' => 'rectangle-group',
                ])
            </div>
            <div class="sm:col-span-2" x-data="{}"
                x-effect="
                    if ($store.jadwal) {
                        let label = $store.jadwal.label_kelas;
                        let wilayah = $store.jadwal.kode_wilayah;

                        if (!label || !wilayah) {
                            $store.jadwal.label_wilayah = '';
                        } else {
                            $store.jadwal.label_wilayah = label + '-' + wilayah;
                        }
                    }
                ">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'jadwal',
                    'modelString' => 'label_wilayah',
                    'placeholder' => '--',
                    'iconString' => 'variable',
                    'noLabel' => 1,
                ])
            </div>
            <div class="sm:col-span-2" x-data="{
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
                            let tahun = $store.jadwal.sesi_1;
                            if (!tahun) {
                                $store.jadwal.digit_tahun = '';
                            } else {
                                $store.jadwal.digit_tahun = getSuffix(tahun);
                            }
                        }
                    ">

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
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_kelas') }}</span>
        @enderror
    </div>

    <div class="grid sm:grid-cols-4 gap-1 sm:gap-3 items-start">
        <div class="sm:col-span-2">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'jadwal',
                // 'noLabel' => 1,
                'nameXString' => 'Label Kelas',
                'modelString' => 'label_kelas',
                'iconString' => 'presentation-chart-bar',
                'placeholder' => 'Contoh: A, B, C',
                'isKode' => 1,
                'isFocusSelect' => 1,
                'message' => $errors->first('label_kelas'),
            ])
        </div>
        <div class="sm:col-span-2">
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
        </div>
    </div>

    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'jadwal',
        'modelString' => 'password',
        'iconString' => 'lock-closed',
        'placeholder' => 'Contoh: AIDL22',
        'message' => $errors->first('password'),
    ])

    <div x-data x-init="$watch('$store.jadwal?.kode_jadwal', value => console.log('kode_jadwal: ', value))"></div>
</div>
