<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'cpl',
                    'nameXString' => 'Kode CPL',
                    'pathString' => 'fk_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'building-library',
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
                    'noLabel' => 1
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
    
        'parentIdString' => 'fk_id',
        'nameXParent' => 'Fakultas',
        'wireLoading' => 'fetchPr',
        'wireLoadingParent' => 'selectFk, resetFkInput, selectFkForFilter, resetFkFilter',
    ])
</div>
