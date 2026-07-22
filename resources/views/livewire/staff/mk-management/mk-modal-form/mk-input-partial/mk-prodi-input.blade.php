<div class="space-y-4">

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'mk',
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr_search',
    
        'idString' => 'pr_id',
        'itemsAllString' => 'pr_items',
    
        'resetXInput' => 'resetPrInput()',
        'typeXString' => 'prodi',
        // 'typeX2String' => 'departemen',
        'typeX2String' => 'fakultas',
    
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
