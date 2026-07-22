<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Semester & SKS</h4>

    <div class="grid grid-cols-6 gap-2">
        <div class="col-span-3 sm:col-span-2">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'mk',
                'modelString' => 'semester',
                'xOptions' => [
                    'Semester 1',
                    'Semester 2',
                    'Semester 3',
                    'Semester 4',
                    'Semester 5',
                    'Semester 6',
                    'Semester 7',
                    'Semester 8',
                ],
                'xValues' => [1, 2, 3, 4, 5, 6, 7, 8],
                'iconString' => 'bookmark-square',
                'placeholder' => 'Pilih Semester...',
                'message' => $errors->first('semester'),
            ])
        </div>
        <div class="col-span-3 sm:col-span-2">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'mk',
                'isLivewire' => 1,
                'nameXString' => 'Wajib / Pilihan',
                'modelString' => 'is_wajib',
                'xOptions' => ['Wajib', 'Pilihan'],
                'xValues' => [1, 0],
                'iconString' => 'tag',
                'placeholder' => 'Wajib / Pilihan',
                'isRequired' => 0,
                'message' => $errors->first('is_wajib'),
            ])
        </div>
        <div class="col-span-6 sm:col-span-2 mt-2 sm:mt-0">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'mk',
                'nameXString' => 'Kategori Blok',
                'modelString' => 'kode_blok',
                'xOptions' => ['Reguler', 'Kerja Praktik / Tugas Akhir'],
                'xValues' => [1, 0],
                'iconString' => 'tag',
                'placeholder' => 'Pilih kategori...',
                'isRequired' => 0,
                'message' => $errors->first('kode_blok'),
            ])
        </div>
    </div>

    <div class="grid grid-cols-12 gap-2 sm:gap-4">
        <div class="col-span-8">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'mk',
                'isLivewire' => 1,
                'nameXString' => 'Tipe SKS',
                'modelString' => 'tipe_sks',
                'xOptions' => ['Tatap Muka', 'Praktikum', 'Praktek Lapangan', 'Simulasi'],
                'xValues' => [1, 2, 3, 4],
                'iconString' => 'bookmark-square',
                'placeholder' => 'Pilih tipe SKS...',
                'isRequired' => 0,
                'message' => $errors->first('tipe_sks'),
            ])
        </div>
        <div class="col-span-4">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'mk',
                'isLivewire' => 1,
                'nameXString' => 'SKS',
                'modelString' => 'sks_kuliah',
                'numberOnly' => 1,
                'maxValue' => 9,
                'noZero' => 1,
                'iconString' => 'identification',
                'placeholder' => 'SKS',
                'isRequired' => 0,
                'message' => $errors->first('sks_kuliah'),
                'isFocusSelect' => 1,
            ])
        </div>
    </div>

    {{-- <div x-data x-init="$watch('$store.mk.nama_mk', value => console.log('nama_mk: ', value))"></div> --}}
</div>
