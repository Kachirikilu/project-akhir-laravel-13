{{-- ****************************************************** --}}
{{-- 2. INPUT JURUSAN --}}
{{-- ****************************************************** --}}
<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Departemen</h4>

    {{-- 📧 Departemen Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Departemen',
        'modelString' => 'nama_dp',
        'iconString' => 'book-open',
        'placeholder' => 'Masukkan nama Departemen',
        'message' => $errors->first('nama_dp'),
    ])

    @if (Auth::user()->tingkat < 2)
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
            'isRequired' => 0,
        ])
    @endif

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
    
        'nameXString' => 'Ketua Departemen (Kadep)',
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
    
        'nameXString' => 'Sekretaris Departemen (Sekdep)',
        'nameSearchString' => 'dosenNameSearch[1]',
        'fetchString' => 'fetchDosen',
        'iconString' => 'user',
        'wireLoading' => 'fetchDosen',
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
