<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Referensi RPS</h4>

        @if (!$this->showRefModal || $this->isEditingRef == false)
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'ref',
                'isFlyout' => 1,
                'isSmall' => 1,
            ])
        @endif
    </div>


    <div class="relative">

        <div class="space-y-6">

            @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">

                {{-- 1. REFERENSI UTAMA (CPMK) --}}
                <div class="sm:col-span-2">
                    @include('livewire.staff.obe-management.obe-partial.referensi-list', [
                        'alpine' => 'rps',
                        'modelString' => 'ref_cpmk',
                        'targetString' => 'CPMK',
                        'textString' => 'Sumber Utama Mata Kuliah',
                        'colorLink' => 'blue',
                    ])
                </div>

                {{-- 2. REFERENSI PENDUKUNG (Sub-CPMK) --}}
                <div class="sm:col-span-2">
                    <div class="sm:col-span-2">
                        @include('livewire.staff.obe-management.obe-partial.referensi-list', [
                            'alpine' => 'rps',
                            'modelString' => 'ref_scpmk',
                            'targetString' => 'Sub-CPMK',
                            'textString' => 'Detail Sumber per Pertemuan',
                            'colorLink' => 'emerald',
                        ])
                    </div>
                </div>
            </div>

            @if ($this->showRPSModal)
                @include('livewire.global.modal-form.input-array.search-input-array-form', [
                    'alpine' => 'rps',
                    'xResults' => $refResults['rps'] ?? [],
                    'selectX' => 'selectRefArray',
                    'modelString' => 'nama_ref_search_rps',
                    'key' => 'rps',
                
                    'idString' => 'ref_id_array.rps',
                    'id2String' => 'ref_id_array',
                    'itemsAllString' => 'ref_items_array.rps',
                
                    'typeXString' => 'judul',
                    'typeX2String' => 'penulis_tahun',
                    'typeX3String' => 'penerbit',
                    'typeLinkString' => 'link',
                
                    'nameXString' => 'Referensi',
                    'nameX2String' => 'Tambah Referensi Baru',
                    'nameSearchString' => 'refNameSearch.rps',
                    'fetchString' => 'fetchRef',
                    'iconString' => 'book-open',
                
                    'parentIdString' => 'cpmk_id_array',
                    'nameXParent' => 'CPMK',
                    'wireLoading' => 'fetchRef',
                
                    'isRequired' => 0,
                ])
            @endif

        </div>

    </div>

</div>
