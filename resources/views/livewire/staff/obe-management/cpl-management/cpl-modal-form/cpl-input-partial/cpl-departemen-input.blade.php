<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'cpl',
                    'nameXString' => 'Kode CPL',
                    'pathString' => 'dp_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'book-open',
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpl',
                    'nameXString' => 'Kode CPL',
                    'modelString' => 'kode_cpl_1',
                    'iconString' => 'document-text',
                    'placeholder' => 'Kode CPL...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                    'noLabel' => 1,
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'cpl',
                    'noLabel' => 1,
                    'modelString' => 'kode_cpl_2',
                    'numberOnly' => 1,
                    'maxLength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 121104',
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>
        @error('kode_cpl')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('kode_cpl') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'cpl',
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

    @if ($cplType == 2)
        @include('livewire.global.modal-form.input-array.search-input-array-form', [
            'alpine' => 'cpl',
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
        
            'parentIdString' => 'dp_id',
            'nameXParent' => 'Departemen',
            'wireLoading' => 'fetchPr',
            'wireLoadingParent' => 'selectDp, resetDpInput, selectDpForFilter, resetDpFilter',
        ])
    @endif
</div>
