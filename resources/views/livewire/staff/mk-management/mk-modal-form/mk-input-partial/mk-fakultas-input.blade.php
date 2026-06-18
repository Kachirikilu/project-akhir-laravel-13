<div class="space-y-4">
    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'mk',
        'xResults' => $fkResults,
        'selectX' => 'selectFk',
        'modelString' => 'nama_fk_search',
    
        'idString' => 'fk_id',
        'itemsAllString' => 'fk_items',
    
        'resetXInput' => 'resetFkInput()',
        'typeXString' => 'fakultas',
        'nameXString' => 'Fakultas',
        'nameSearchString' => 'fkNameSearch',
        'fetchString' => 'fetchFk',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchFk',
    ])

    @if ($mkType == 3)
        @include('livewire.global.modal-form.input-array.search-input-array-form', [
            'alpine' => 'mk',
            'xResults' => $prResults,
            'selectX' => 'selectPrArray',
            'modelString' => 'nama_pr_search',
        
            'idString' => 'pr_id_array',
            'itemsAllString' => 'pr_items_array',
        
            'typeXString' => 'prodi',
            'typeX2String' => 'departemen',
            'typeX3String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
        
            'parentIdString' => 'fk_id',
            'nameXParent' => 'Fakultas',
            'wireLoading' => 'fetchPr',
            'wireLoadingParent' => 'selectFk, resetFkInput, selectFkForFilter, resetFkFilter',
        ])
    @endif
</div>
