<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium mr-2">
            Referensi CPMK</h4>

        @if ($parent !== 'ref')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'ref',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'cpmk',
            ])
        @endif
    </div>



    <div class="space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            {{-- 1. REFERENSI PENDUKUNG (Sub-CPMK) --}}
            <div class="sm:col-span-2">
                @include('livewire.staff.obe-management.obe-partial.referensi-list', [
                    'alpine' => 'cpmk',
                    'modelString' => 'ref_scpmk',
                    'targetString' => 'Sub-CPMK',
                    'textString' => 'Detail Sumber per Pertemuan',
                    'colorLink' => 'emerald',
                ])
            </div>
        </div>

        @if ($this->showCPMKModal)
            @include('livewire.global.modal-form.input-array.search-input-array-form', [
                'alpine' => 'cpmk',
                'xResults' => $refResults,
                'selectX' => 'selectRefArray',
                'modelString' => 'nama_ref_search',
            
                'idString' => 'ref_id_array',
                'itemsAllString' => 'ref_items_array',
            
                'typeXString' => 'judul',
                'typeX2String' => 'penulis_tahun',
                'typeX3String' => 'penerbit',
                'typeLinkString' => 'link',
            
                'nameXString' => 'Referensi',
                'nameX2String' => 'Tambah Referensi Baru',
                'nameSearchString' => 'refNameSearch',
                'fetchString' => 'fetchRef',
                'iconString' => 'book-open',
            
                'parentIdString' => 'scpmk_id_array',
                'nameXParent' => 'Sub-CPMK',
                'wireLoading' => 'fetchRef',
            
                'isRequired' => 0,
            ])
        @endif

    </div>

</div>
