<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Mata Kuliah</h4>

    {{-- 📧 Mata Kuliah Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'mk',
        'nameXString' => 'Nama Mata Kuliah',
        'modelString' => 'nama_mk',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan nama Mata Kuliah...',
        'message' => $errors->first('nama_mk'),
    ])

    <div class="relative">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'handleAddMK, editMK'])

        <div class="space-y-4">
            <div>
                <div class="grid grid-cols-6 gap-2 items-end">

                    <div class="col-span-3 sm:col-span-2">
                        <template x-if="$store.mk?.typeModal == 1" x-cloak>
                            @include('livewire.global.modal-form.kode-input', [
                                'alpine' => 'mk',
                                'nameXString' => 'Kode Mata Kuliah',
                                'pathString' => 'pr_items',
                                'modelString' => 'kode',
                                'placeholder' => '---',
                                'iconString' => 'academic-cap',
                            ])
                        </template>

                        <template x-if="$store.mk?.typeModal == 2" x-cloak>
                            @include('livewire.global.modal-form.kode-input', [
                                'alpine' => 'mk',
                                'nameXString' => 'Kode Mata Kuliah',
                                'pathString' => 'dp_items',
                                'modelString' => 'kode',
                                'placeholder' => '---',
                                'iconString' => 'book-open',
                            ])
                        </template>

                        <template x-if="$store.mk?.typeModal == 3" x-cloak>
                            @include('livewire.global.modal-form.kode-input', [
                                'alpine' => 'mk',
                                'nameXString' => 'Kode Mata Kuliah',
                                'pathString' => 'fk_items',
                                'modelString' => 'kode',
                                'placeholder' => '---',
                                'iconString' => 'building-library',
                            ])
                        </template>

                        <template x-if="$store.mk?.typeModal == 4" x-cloak>
                            @include('livewire.global.modal-form.kode-input', [
                                'alpine' => 'mk',
                                'nameXString' => 'Kode Mata Kuliah',
                                'modelString' => 'kode',
                                'valueString' => 'UNI',
                                'iconString' => 'globe-alt',
                            ])
                        </template>

                    </div>

                    <div class="col-span-3 sm:col-span-2">
                        @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-digit-semester')
                    </div>

                    <div class="col-span-6 sm:col-span-2 mt-2 sm:mt-0">
                        @include('livewire.global.modal-form.input-form', [
                            'alpine' => 'mk',
                            'nameXString' => 'Urutan Mata Kuliah',
                            'modelString' => 'digit_mk',
                            'numberOnly' => 1,
                            'maxValue' => 200,
                            'iconString' => 'identification',
                            'placeholder' => 'Contoh: 07',
                            'isFocusSelect' => 1,
                        ])
                    </div>
                </div>
                @error('digit_mk')
                    <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('digit_mk') }}</span>
                @enderror
            </div>

            <template x-if="$store.mk?.typeModal == 1" x-cloak>
                @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-prodi-input')
            </template>

            <template x-if="$store.mk?.typeModal == 2" x-cloak>
                @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-departemen-input')
            </template>

            <template x-if="$store.mk?.typeModal == 3" x-cloak>
                @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-fakultas-input')
            </template>

            <template x-if="$store.mk?.typeModal == 4" x-cloak>
                @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-universitas-input')
            </template>
        </div>

    </div>


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
