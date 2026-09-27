<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Ketua dan Sekretaris Fakultas</h4>

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'selectIndex' => 0,
        'idString' => 'dosen_id_array[0]',
        'itemsAllString' => 'dosen_items_array[0]',
    
        'kodeHeadString' => 'NIP.',
        'x2HeadString' => 'NIDN.',
        'x3HeadString' => 'NIDK.',
        'x5HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInputArray(0)',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'prodi',
        'typeX5String' => 'status',
    
        'nameXString' => 'Dekan',
        'nameSearchString' => 'dosenNameSearchArray[0]',
        'fetchString' => 'fetchDosenArray',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosenArray',
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'selectIndex' => 1,
        'idString' => 'dosen_id_array[1]',
        'itemsAllString' => 'dosen_items_array[1]',
    
        'kodeHeadString' => 'NIP.',
        'x2HeadString' => 'NIDN.',
        'x3HeadString' => 'NIDK.',
        'x5HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInputArray(1)',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'prodi',
        'typeX5String' => 'status',
    
        'nameXString' => 'Wakil Dekan (Wadek)',
        'nameSearchString' => 'dosenNameSearchArray[1]',
        'fetchString' => 'fetchDosenArray',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosenArray',
    ])

</div>
