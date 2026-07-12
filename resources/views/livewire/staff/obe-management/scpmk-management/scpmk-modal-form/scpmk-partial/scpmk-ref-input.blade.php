<div
    class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium mr-2">
            Referensi Sub-CPMK</h4>

        @if ($parent !== 'ref')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'ref',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'scpmk'
            ])
        @endif
    </div>


    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addSCPMK, editSCPMK'])

        @if ($this->showSCPMKModal)
            @include('livewire.global.modal-form.input-array.search-input-array-form', [
                'alpine' => 'scpmk',
                'xResults' => $refResults['scpmk'] ?? [],
                'selectX' => 'selectRefArray',
                'modelString' => 'nama_ref_search_scpmk',
                'key' => 'scpmk',
            
                'idString' => 'ref_id_array.scpmk',
                'id2String' => 'ref_id_array',
                'itemsAllString' => 'ref_items_array.scpmk',
            
                'typeXString' => 'judul',
                'typeX2String' => 'penulis_tahun',
                'typeX3String' => 'penerbit',
                'typeLinkString' => 'link',
            
                'nameXString' => 'Referensi',
                'nameSearchString' => 'refNameSearch.scpmk',
                'fetchString' => 'fetchRef',
                'iconString' => 'book-open',
                'wireLoading' => 'fetchRef',
                // 'message' => $errors->first('referensi'),
            ])
        @endif


    </div>

</div>
