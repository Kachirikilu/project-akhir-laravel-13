<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Ketua dan Sekretaris Program Studi</h4>

    {{-- @dump($dosen_id_array, $dosen_items_array, $dosenNameSearchArray) --}}

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosenArray',
        'modelString' => 'nama_dosen_search',
    
        'selectIndex' => 4,
        'idString' => 'dosen_id_array[4]',
        'itemsAllString' => 'dosen_items_array[4]',
    
        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x5HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInputArray(4)',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'prodi',
        'typeX5String' => 'status',
    
        'nameXString' => 'Ketua Program Studi (Kaprodi)',
        'nameSearchString' => 'dosenNameSearchArray[4]',
        'fetchString' => 'fetchDosenArray',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosenArray',
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosenArray',
        'modelString' => 'nama_dosen_search',
    
        'selectIndex' => 5,
        'idString' => 'dosen_id_array[5]',
        'itemsAllString' => 'dosen_items_array[5]',
    
        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x5HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInputArray(5)',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'prodi',
        'typeX5String' => 'status',
    
        'nameXString' => 'Sekretaris Program Studi (Sekprodi)',
        'nameSearchString' => 'dosenNameSearchArray[5]',
        'fetchString' => 'fetchDosenArray',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosenArray',
    ])

</div>
