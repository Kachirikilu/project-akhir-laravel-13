<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Ketua dan Sekretaris Program Studi</h4>

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'idString' => 'dosen_id_array[4]',
        'itemsAllString' => 'dosen_items_array[4]',
    
        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x4HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInput()',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'status',
        'typeX5String' => 'prodi',
    
        'nameXString' => 'Ketua Program Studi (Kaprodi)',
        'nameSearchString' => 'dosenNameSearch[4]',
        'fetchString' => 'fetchDosen',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosen',
        'isRequired' => 0,
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'idString' => 'dosen_id_array[5]',
        'itemsAllString' => 'dosen_items_array[5]',
    
        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x4HeadString' => 'Status:',
    
        'resetXInput' => 'resetDosenInput()',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'status',
        'typeX5String' => 'prodi',
    
        'nameXString' => 'Sekretaris Program Studi (Sekprodi)',
        'nameSearchString' => 'dosenNameSearch[5]',
        'fetchString' => 'fetchDosen',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosen',
        'isRequired' => 0,
    ])

</div>
