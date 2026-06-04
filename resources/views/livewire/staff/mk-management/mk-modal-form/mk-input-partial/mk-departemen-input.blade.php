<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'nameXString' => 'Kode Mata Kuliah',
                    'pathString' => 'dp_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'book-open',
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
                    'maxValue' => 200,
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
    
        'parentIdString' => 'dp_id',
        'nameXParent' => 'Departemen',
        'wireLoading' => 'fetchPr',
        'wireLoadingParent' => 'selectDp, resetDpInput, selectDpForFilter, resetDpFilter',
    ])
</div>
