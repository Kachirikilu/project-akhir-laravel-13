<div class="space-y-4">
    @if ($mkType == 4)
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
            'wireLoading' => 'fetchPr',
        ])
    @endif
</div>
