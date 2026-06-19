<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Kelas Perkuliahan</h4>

    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode Kelas',
        ])
        <div class="grid grid-cols-6 gap-1 sm:gap-2 items-end" x-data="{}"
            x-effect="$store.kelas.kode_kelas = ($store.kelas.kode_kelas_1 || '') + ($store.kelas.kode_kelas_2 || '')">

            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'kelas',
                    'noLabel' => 1,
                    'modelString' => 'kode_kelas_1',
                    'iconString' => 'document-text',
                    'placeholder' => 'Masukkan mutu Kode Kelas...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'kelas',
                    'noLabel' => 1,
                    'modelString' => 'kode_kelas_2',
                    'numberOnly' => 1,
                    'maxLength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 121104',
                    'isFocusSelect' => 1,
                ])
            </div>

        </div>
        @error('kode_kelas')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_kelas') }}</span>
        @enderror
    </div>

    {{-- 📧 Mata Kuliah Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'kelas',
        'nameXString' => 'Nama Kelas',
        'modelString' => 'nama_kelas',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan nama Kelas...',
        'message' => $errors->first('nama_kelas'),
    ])

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'kelas',
        'nameXString' => 'Deskripsi Kelas',
        'modelString' => 'deskripsi',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Deskripsi dari Kelas...',
        'message' => $errors->first('deskripsi'),
        'isRequired' => 0,
    ])

    {{-- <div x-data x-init="$watch('$store.kelas.kode_kelas', value => console.log('kode_kelas: ', value))"></div>
    <div x-data x-init="$watch('$store.kelas.nama_kelas', value => console.log('nama_kelas: ', value))"></div>
    <div x-data x-init="$watch('$store.kelas.pr_id', value => console.log('pr_id: ', value))"></div>
    <div x-data x-init="$watch('$store.kelas.rps_id', value => console.log('rps_id: ', value))"></div> --}}
</div>
