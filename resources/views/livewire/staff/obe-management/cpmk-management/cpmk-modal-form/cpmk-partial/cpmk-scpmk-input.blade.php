<div
    class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Pilih Sub Capaian Pembelajaran Mata Kuliah</h4>

        @if ($parent !== 'scpmk')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'scpmk',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'cpmk'
            ])
        @endif

    </div>

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'addCPMK, editCPMK',
            'heightContainer' => 32,
        ])

        <div class="space-y-4">

            @if ($this->showCPMKModal)
                @include('livewire.global.modal-form.input-array.search-input-scpmk-form', [
                    'alpine' => 'cpmk',
                    'xResults' => $scpmkResults,
                    'selectX' => 'selectSCPMKArray',
                    'modelString' => 'nama_scpmk_search',
                
                    'idString' => 'scpmk_id_array',
                    'itemsAllString' => 'scpmk_items_array',
                    'subItemsString' => 'scpmk_sub_items_array',
                
                    'typeXString' => 'deskripsi',
                    'typeX2String' => 'metode',
                    'typeX3String' => 'bobot',
                    'withParent' => 'cpmk',
                
                    'nameXString' => 'Sub Capaian Pempebalajaran Mata Kuliah (Sub-CPMK)',
                    'nameSearchString' => 'scpmkNameSearch',
                    'fetchString' => 'fetchSCPMK',
                    'iconString' => 'academic-cap',
                    'wireLoading' => 'fetchSCPMK',
                ])
            @endif
        </div>
    </div>


</div>
