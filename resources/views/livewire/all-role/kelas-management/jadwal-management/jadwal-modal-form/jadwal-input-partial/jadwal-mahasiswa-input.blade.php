<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Mahasiswa Kelas</h4>


    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'jadwal',
        'nameXString' => 'Kapasitas Kelas',
        'modelString' => 'kapasitas',
        'numberOnly' => 1,
        'maxLength' => 3,
        'iconString' => 'users',
        'placeholder' => 'Contoh: 50',
        'message' => $errors->first('kapasitas'),
    ])

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'addJadwal, editJadwal',
            'heightContainer' => 32,
        ])

        @if ($this->showJadwalModal)
            @include('livewire.global.modal-form.input-array.search-input-array-form', [
                'alpine' => 'jadwal',
                'xResults' => $mahasiswaResults,
                'selectX' => 'selectMahasiswaArray',
                'modelString' => 'nama_mahasiswa_search',
            
                'idString' => 'mahasiswa_id_array',
                'itemsAllString' => 'mahasiswa_items_array',
            
                'typeXString' => 'name',
                'typeX2String' => 'prodi',
                'typeX3String' => 'wilayah',
                'typeX4String' => 'angkatan_full',
                'typeX5String' => 'status_full',
            
                'nameXString' => 'Mahasiswa',
                'nameSearchString' => 'mahasiswaNameSearch',
                'fetchString' => 'fetchMahasiswa',
                'iconString' => 'academic-cap',
                'wireLoading' => 'fetchMahasiswa',
                'isRequired' => 0,
            ])
        @endif
    </div>
</div>
