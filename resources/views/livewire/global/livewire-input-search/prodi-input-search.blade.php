@include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'user',
        'isLivewire' => 1,
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr',
    
        'idString' => 'pr_id',
        'itemsAllString' => 'pr_items',
    
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