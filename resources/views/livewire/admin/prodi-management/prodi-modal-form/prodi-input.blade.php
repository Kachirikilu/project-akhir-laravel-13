{{-- ****************************************************** --}}
{{-- 1. INPUT PROGRAM STUDI --}}
{{-- ****************************************************** --}}
<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Program Studi</h4>

    {{-- 📧 Program Studi Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Program Studi',
        'modelString' => 'nama_pr',
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



    <div> 
        @include('livewire.global.modal-form.partial.label', [
            'nameXString' => 'Kode Program Studi',
        ])
        <div class="grid grid-cols-8 gap-2 sm:gap-4">
            <div class="col-span-3" x-data="{}"
                x-effect="
                    if ($store.prodi) {
                        strata = $store.prodi.strata;
                        let strLow = strata.toLowerCase();

                        if (strLow === 'sarjana') {
                            $store.prodi.strata_short = 'S1';
                        } else if (strLow === 'magister') {
                            $store.prodi.strata_short = 'S2';
                        } else if (strLow === 'doktor') {
                            $store.prodi.strata_short = 'S3';
                        }
                    }
            ">
                @include('livewire.global.modal-form.kode-input', [
                    'alpine' => 'prodi',
                    'noLabel' => 1,
                    'nameXString' => 'Kode Program Studi',
                    'modelString' => 'strata_short',
                    'placeholder' => '--',
                    'iconString' => 'variable',
                ])
            </div>
            <div class="col-span-5">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'prodi',
                    'noLabel' => 1,
                    'modelString' => 'kode_pr',
                    'iconString' => 'hashtag',
                    'placeholder' => 'Masukkan 3 huruf Kode Program Studi',
                    'isKode' => 3,
                    'isFocusSelect' => 1,
                ])
            </div>
        </div>
        @error('kode_pr')
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $errors->first('kode_pr') }}</span>
        @enderror
    </div>


    <div class="grid grid-cols-12 gap-2 sm:gap-4">
        <div class="col-span-7 sm:col-span-8">
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
        <div class="col-span-5 sm:col-span-4">
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'prodi',
                'nameXString' => 'Target SKS',
                'modelString' => 'target_sks',
                'numberOnly' => 1,
                'maxValue' => 255,
                'noZero' => 1,
                'iconString' => 'scale',
                'placeholder' => 'Default 144 SKS',
                'isRequired' => 0,
                'message' => $errors->first('target_sks'),
                'isFocusSelect' => 1,
            ])
        </div>
    </div>

</div>
