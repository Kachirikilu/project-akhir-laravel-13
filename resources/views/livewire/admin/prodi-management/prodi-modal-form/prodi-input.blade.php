{{-- ****************************************************** --}}
{{-- 1. INPUT PROGRAM STUDI --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Program Studi</h4>

    {{-- 📧 Program Studi Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Program Studi',
        'modelString' => 'nama_pr',
        // 'typeString' => 'text',
        // 'colorIcon' => $colorIcon,
        'iconString' => 'academic-cap',
        'placeholder' => 'Masukkan nama Program Studi',
        'message' => $errors->first('nama_pr'),
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
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
        'iconString' => 'book-open',
        'wireLoading' => 'fetchDp',
    ])

    {{-- 📧 Kode Program Studi Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Kode Program Studi',
        'modelString' => 'kode_pr',
        'iconString' => 'hashtag',
        'placeholder' => 'Masukkan 3 huruf Kode Program Studi',
        'message' => $errors->first('kode_pr'),
        'isKode' => 3,
        'isFocusSelect' => 1,
    ])

    {{-- 📧 Nama Strata Input --}}
    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Strata',
        'modelString' => 'strata',
        'xOptions' => ['Sarjana', 'Magister', 'Doktor'],
        'iconString' => 'bookmark-square',
        'placeholder' => 'Pilih Strata...',
        'message' => $errors->first('strata'),
    ])
</div>
