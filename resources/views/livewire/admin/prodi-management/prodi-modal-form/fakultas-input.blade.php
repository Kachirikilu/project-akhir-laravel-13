{{-- ****************************************************** --}}
{{-- 3. INPUT FAKULTAS --}}
{{-- ****************************************************** --}}
<div
    class="form-container">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Fakultas</h4>

    {{-- 📧 Fakultas Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Fakultas',
        'modelString' => 'nama_fk',
        'iconString' => 'building-library',
        'placeholder' => 'Masukkan nama Fakultas',
        'message' => $errors->first('nama_fk')
    ])

    {{-- 📧 Kode Fakultas Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Kode Fakultas',
        'modelString' => 'kode_fk',
        'iconString' => 'hashtag',
        'placeholder' => 'Masukkan 3 huruf Kode Fakultas',
        'message' => $errors->first('kode_fk'),
        'isKode' => 3,
        'isFocusSelect' => 1,
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'idString' => 'dosen_id[0]',
        'itemsAllString' => 'dosen_items[0]',

        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x4HeadString' => 'Status:',

        'resetXInput' => 'resetDosenInput()',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'status',
        'typeX5String' => 'prodi',
    
        'nameXString' => 'Dekan',
        'nameSearchString' => 'dosenNameSearch[0]',
        'fetchString' => 'fetchDosen',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosen',
        'isRequired' => 0,
    ])

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'prodi',
        'xResults' => $dosenResults,
        'selectX' => 'selectDosen',
        'modelString' => 'nama_dosen_search',
    
        'idString' => 'dosen_id[1]',
        'itemsAllString' => 'dosen_items[1]',

        'kodeHeadString' => 'NIP:',
        'x2HeadString' => 'NIDN:',
        'x3HeadString' => 'NIDK:',
        'x4HeadString' => 'Status:',

        'resetXInput' => 'resetDosenInput()',
        'typeXString' => 'name',
        'typeX2String' => 'nidn',
        'typeX3String' => 'nidk',
        'typeX4String' => 'status',
        'typeX5String' => 'prodi',
    
        'nameXString' => 'Wakil Dekan (Wadek)',
        'nameSearchString' => 'dosenNameSearch[1]',
        'fetchString' => 'fetchDosen',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosen',
        'isRequired' => 0,
    ])
</div>
