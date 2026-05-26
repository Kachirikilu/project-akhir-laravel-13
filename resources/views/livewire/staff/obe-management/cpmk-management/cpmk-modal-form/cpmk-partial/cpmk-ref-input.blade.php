<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

    <h4
        class="text-[var(--contrast-main-text)] text-lg font-medium">
        Referensi CPMK</h4>

        @if (!$this->showRefModal || $this->isEditingRef == false)
            @include('livewire.staff.obe-management.obe-toolbar', ['typeXString' => 'ref', 'isFlyout' => 1, 'isSmall' => 1])
        @endif
    </div>


    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addCPMK, editCPMK'])

        <div class="space-y-6">

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
                @include('livewire.global.modal-form.search-input-array-form', [
                    'alpine' => 'cpmk',
                    'xResults' => $refResults['cpmk'] ?? [],
                    'selectX' => 'selectRefArray',
                    'modelString' => 'nama_ref_search_cpmk',
                    'key' => 'cpmk',
                
                    'idString' => 'ref_id_array.cpmk',
                    'id2String' => 'ref_id_array',
                    'itemsAllString' => 'ref_items_array.cpmk',
                
                    'typeXString' => 'judul',
                    'typeX2String' => 'penulis_tahun',
                    'typeX3String' => 'penerbit',
                    'typeLinkString' => 'link',
                
                    'nameXString' => 'Referensi',
                    'nameX2String' => 'Tambah Referensi Baru',
                    'nameSearchString' => 'refNameSearch.cpmk',
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

</div>
