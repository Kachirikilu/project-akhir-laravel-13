<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

    <h4
        class="text-[var(--contrast-main-text)] text-lg font-medium">
        Pilih Capaian Pembelajaran Mata Kuliah</h4>

        @if (!$this->showCPMKModal || $this->isEditingCPMK == false)
            @if (!$this->showSCPMKModal || $this->isEditingSCPMK == false)
                @include('livewire.staff.obe-management.obe-toolbar', ['typeXString' => 'cpmk-scpmk', 'isFlyout' => 1, 'isSmall' => 1])
            @else
                @include('livewire.staff.obe-management.obe-toolbar', ['typeXString' => 'cpmk', 'isFlyout' => 1, 'isSmall' => 1])
            @endif
        @endif
    </div>

    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        <div class="space-y-4">
            @if ($this->showRPSModal)
                @include('livewire.global.modal-form.search-input-cpmk-form', [
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
                
                    'nameXString' => 'Capaian Pembelajaran Mata Kuliah (CPMK)',
                    'nameSearchString' => 'cpmkNameSearch',
                    'fetchString' => 'fetchCPMK',
                    'iconString' => 'academic-cap',
                    'wireLoading' => 'fetchCPMK',
                ])
            @else
                <div class="h-32"></div>
            @endif
        </div>

    </div>


</div>
