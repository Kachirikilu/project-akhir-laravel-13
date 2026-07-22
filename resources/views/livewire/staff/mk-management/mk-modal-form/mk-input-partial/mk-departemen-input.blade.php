<div class="space-y-4">
    @if (Auth::user()->tingkat < 3)
        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'mk',
            'xResults' => $dpResults,
            'selectX' => 'selectDp',
            'modelString' => 'nama_dp_search',
        
            'idString' => 'dp_id',
            'itemsAllString' => 'dp_items',
        
            'resetXInput' => 'resetDpInput()',
            'typeXString' => 'departemen',
            'typeX2String' => 'fakultas',
        
            'nameXString' => 'Departemen',
            'nameSearchString' => 'dpNameSearch',
            'fetchString' => 'fetchDp',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchDp',
        ])
    @endif

    @if ($mkType == 2)
        @include('livewire.global.modal-form.input-array.search-input-array-form', [
            'alpine' => 'mk',
            'xResults' => $prResults,
            'selectX' => 'selectPrArray',
            'modelString' => 'nama_pr_search',
        
            'idString' => 'pr_id_array',
            'itemsAllString' => 'pr_items_array',
        
            'typeXString' => 'prodi',
            // 'typeX2String' => 'departemen',
            'typeX2String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
        
            'parentIdString' => 'dp_id',
            'nameXParent' => 'Departemen',
            'wireLoading' => 'fetchPr',
            'wireLoadingParent' => 'selectDp, resetDpInput, selectDpForFilter, resetDpFilter',
        ])
    @endif
</div>
