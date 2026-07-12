<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Tim Dosen</h4>


    <div>
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode Tim Dosen',
        ])
        <div class="grid grid-cols-6 gap-1 sm:gap-2 items-end" x-data="{}"
            x-effect="$store.tim_dosen.kode_tim_dosen = ($store.tim_dosen.kode_tim_dosen_1 || '') + ($store.tim_dosen.kode_tim_dosen_2 || '')">

            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'tim_dosen',
                    'noLabel' => 1,
                    'modelString' => 'kode_tim_dosen_1',
                    'iconString' => 'user-group',
                    'placeholder' => 'Kode Tim Dosen...',
                    'isKode' => 4,
                    'isFocusSelect' => 1,
                ])
            </div>
            <div class="col-span-3">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'tim_dosen',
                    'noLabel' => 1,
                    'modelString' => 'kode_tim_dosen_2',
                    'numberOnly' => 1,
                    'maxLength' => 6,
                    'iconString' => 'variable',
                    'placeholder' => 'Contoh: 1211',
                    'isFocusSelect' => 1,
                ])
            </div>

        </div>
        @error('kode_cpmk')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_cpmk') }}</span>
        @enderror
    </div>

    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'tim_dosen',
        'nameXString' => 'Nama Tim Dosen',
        'modelString' => 'nama_tim',
        'iconString' => 'book-open',
        'placeholder' => 'Masukkan Nama Tim Dosen...',
        'message' => $errors->first('nama_tim'),
    ])
    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'tim_dosen',
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr',
    
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

    {{-- <div x-text="$store.tim_dosen.pr_id"></div>
                <div x-text="$store.tim_dosen.nama_pr_search"></div>
                <div x-text="$store.tim_dosen.pr_items"></div> --}}
</div>
