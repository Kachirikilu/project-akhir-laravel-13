<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Antrean Excel Data Pengguna</h4>


    @include('livewire.global.modal-form.file-input-form', [
        'alpine' => 'user',
        'modelString' => 'excel_user_file',
        'wireKeyString' => 'excel-input-field',
        'nameXString' => 'Pilih File Excel Pengguna',
        'wireLoading' => 'parseExcelUserFile',
        'multiFile' => 1,
        'fileDelete' => 'clearUserExcelFile',
        'message' => $errors->first('excel_user_file'),
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'user',
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr',
    
        'idString' => 'pr_id',
        'itemsAllString' => 'pr_items',
    
        'x2HeadString' => 'Departemen',
        'x3HeadString' => 'Fakultas',
    
        'resetXInput' => 'resetPrInput()',
        'typeXString' => 'prodi',
        'typeX2String' => 'departemen',
        'typeX3String' => 'fakultas',
    
        'nameXString' => 'Program Studi',
        'nameSearchString' => 'prNameSearch',
        'fetchString' => 'fetchPr',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchPr',
    ])

    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'user',
        'isLivewire' => 1,
        'modelString' => 'role',
        'xOptions' => ['Admin', 'Dosen', 'Mahasiswa'],
        'iconString' => 'users',
        'placeholder' => 'Pilih Role Utama ketika data tidak memiliki Role...',
        'isRequired' => 0,
        'message' => $errors->first('role'),
    ])

    <flux:checkbox wire:model="update_or_create_mode"
        x-on:change="$store.user.update_or_create_mode = $el.checked ? 1 : 0" :value="1"
        label="Update or Create"
        description="Jika memiliki data yang identik dengan data yang sudah ada, sistem hanya memperbarui data yang sudah ada."
        class="cursor-pointer" />

    <template x-if="$store.user.update_or_create_mode == 1">
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'modelString' => 'update_or_create',
            'xOptions' => ['Berdasarkan NIP/NIM', 'Berdasarkan NIK', 'Berdasarkan Email'],
            'xValues' => ['identity1', 'nik', 'email'],
            'iconString' => 'users',
            'placeholder' => 'Default berdasarkan NIP/NIM...',
            'isRequired' => 0,
            'message' => $errors->first('update_or_create'),
        ])
    </template>
</div>
