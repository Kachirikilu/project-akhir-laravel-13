<div class="space-y-4">
    <div>
        <div class="grid sm:grid-cols-6 gap-1 items-end">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'mk',
                    'nameXString' => 'Kode Mata Kuliah',
                    'pathString' => 'pr_items',
                    'modelString' => 'kode',
                    'placeholder' => '---',
                    'iconString' => 'academic-cap',
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
