<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'nameXString' => 'Kode Mata Kuliah',
                    'pathString' => 'fk_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'building-library',
                ])
            </div>

            <div class="sm:col-span-2">
                @include('livewire.staff.mk-management.mk-modal-form.mk-input-partial.mk-digit-semester')
            </div>

            <div class="sm:col-span-2 mt-1 sm:mt-0">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'mk',
                    'nameXString' => 'Urutan Mata Kuliah',
                    'modelString' => 'digit_mk',
                    'numberOnly' => 1,
                    'maxlength' => 2,
                    'iconString' => 'identification',
                    'placeholder' => 'Contoh: 07',
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>
        @error('digit_mk')
            <span class="text-red-500 text-sm mt-1 block">{{ $errors->first('digit_mk') }}</span>
        @enderror
    </div>


    @include('livewire.global.modal-form.search-input-form', [
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

    @include('livewire.global.modal-form.search-input-array-form', [
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
</div>
