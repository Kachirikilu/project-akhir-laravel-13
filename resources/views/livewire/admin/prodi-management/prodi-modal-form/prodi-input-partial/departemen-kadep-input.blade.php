<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Ketua dan Sekretaris Departemen</h4>


        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'prodi',
            'xResults' => $dosenResults,
            'selectX' => 'selectDosen',
            'modelString' => 'nama_dosen_search',
        
        'selectIndex' => 2,
            'idString' => 'dosen_id_array[2]',
            'itemsAllString' => 'dosen_items_array[2]',
        
            'kodeHeadString' => 'NIP:',
            'x2HeadString' => 'NIDN:',
            'x3HeadString' => 'NIDK:',
            'x5HeadString' => 'Status:',
        
            'resetXInput' => 'resetDosenInputArray(2)',
            'typeXString' => 'name',
            'typeX2String' => 'nidn',
            'typeX3String' => 'nidk',
            'typeX4String' => 'prodi',
            'typeX5String' => 'status',
        
            'nameXString' => 'Ketua Departemen (Kadep)',
            'nameSearchString' => 'dosenNameSearchArray[2]',
            'fetchString' => 'fetchDosenArray',
            'iconString' => 'user',
            'wireLoading' => 'fetchDosenArray',
        ])

        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'prodi',
            'xResults' => $dosenResults,
            'selectX' => 'selectDosen',
            'modelString' => 'nama_dosen_search',
        
        'selectIndex' => 3,
            'idString' => 'dosen_id_array[3]',
            'itemsAllString' => 'dosen_items_array[3]',
        
            'kodeHeadString' => 'NIP:',
            'x2HeadString' => 'NIDN:',
            'x3HeadString' => 'NIDK:',
            'x4HeadString' => 'Prodi:',
            'x5HeadString' => 'Status:',
        
            'resetXInput' => 'resetDosenInputArray(3)',
            'typeXString' => 'name',
            'typeX2String' => 'nidn',
            'typeX3String' => 'nidk',
            'typeX4String' => 'prodi',
            'typeX5String' => 'status',
        
            'nameXString' => 'Sekretaris Departemen (Sekdep)',
            'nameSearchString' => 'dosenNameSearchArray[3]',
            'fetchString' => 'fetchDosenArray',
            'iconString' => 'user',
            'wireLoading' => 'fetchDosenArray',
        ])

</div>
