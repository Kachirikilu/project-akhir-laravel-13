<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Referensi Sub-CPMK</h4>

        @if (!$this->showRefModal || $this->isEditingRef == false)
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'ref',
                'isFlyout' => 1,
                'isSmall' => 1,
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
