{{-- ****************************************************** --}}
{{-- 2. INPUT JURUSAN --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Departemen</h4>

    {{-- 📧 Departemen Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        // 'colorIcon' => $colorIcon,
        'nameXString' => 'Nama Departemen',
        'modelString' => 'nama_dp',
        // 'typeString' => 'text',
        'iconString' => 'book-open',
        'placeholder' => 'Masukkan nama Departemen',
        'message' => $errors->first('nama_dp'),
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
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
        'iconString' => 'building-library',
        'wireLoading' => 'fetchFk',
    ])

    {{-- 📧 Kode Departemen Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Kode Departemen',
        'modelString' => 'kode_dp',
        'iconString' => 'hashtag',
        'placeholder' => 'Masukkan 3 huruf Kode Departemen',
        'message' => $errors->first('kode_dp'),
        'isKode' => 3,
        'isFocusSelect' => 1,
    ])

</div>
