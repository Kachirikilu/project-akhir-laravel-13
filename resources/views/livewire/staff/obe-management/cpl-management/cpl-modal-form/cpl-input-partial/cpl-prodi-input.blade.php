<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'cpl',
                    'nameXString' => 'Kode CPL',
                    'pathString' => 'pr_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'academic-cap',
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
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr_search',
    
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


    {{-- <div x-data x-init="$watch('$store.mk.nama_mk', value => console.log('nama_mk: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.pr_id', value => console.log('pr_id: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.prodi_kode', value => console.log('kode_pr: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.prodi_kode', value => console.log('prodi_kode: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.digit_semester', value => console.log('digit_semester: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.digit_mk', value => console.log('digit_mk: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.semester', value => console.log('semester: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.kode_blok', value => console.log('kode_blok: ', value))"></div>

    <div x-data x-init="$watch('$store.mk.tipe_sks', value => console.log('tipe_sks: ', value))"></div>
    <div x-data x-init="$watch('$store.mk.sks_kuliah', value => console.log('sks_kuliah: ', value))"></div> --}}
</div>
