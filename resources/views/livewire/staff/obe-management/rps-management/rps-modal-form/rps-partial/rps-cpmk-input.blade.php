<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium mr-2">
            Pilih Capaian Pembelajaran Mata Kuliah</h4>

        @if ($parent !== 'cpmk')
            @if ($parent !== 'scpmk')
                @include('livewire.staff.obe-management.obe-toolbar', [
                    'typeXString' => 'cpmk-scpmk',
                    'isFlyout' => 1,
                    'isSmall' => 1,
                    'parent' => 'rps',
                ])
            @else
                @include('livewire.staff.obe-management.obe-toolbar', [
                    'typeXString' => 'cpmk',
                    'isFlyout' => 1,
                    'isSmall' => 1,
                    'parent' => 'rps',
                ])
            @endif
        @endif

    </div>

    @include('livewire.global.modal-form.input-array.search-input-cpmk-form', [
        'alpine' => 'rps',
        'xResults' => $cpmkResults,
        'selectX' => 'selectCPMKArray',
        'modelString' => 'nama_cpmk_search',
    
        'idString' => 'cpmk_id_array',
        'itemsAllString' => 'cpmk_items_array',
        'subItemsString' => 'cpmk_sub_items_array',
    
        'typeXString' => 'deskripsi',
        'typeX2String' => 'count_scpmk',
        'typeX3String' => 'total_bobot',
        'withParent' => 'rps',
    
        'nameXString' => 'Capaian Pembelajaran Mata Kuliah (CPMK)',
        'nameSearchString' => 'cpmkNameSearch',
        'fetchString' => 'fetchCPMK',
        'iconString' => 'academic-cap',
        'wireLoading' => 'fetchCPMK',
    ])

</div>
