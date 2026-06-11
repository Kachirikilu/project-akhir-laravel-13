<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Capaian Pembelajaran Lulusan</h4>

        @if (!$this->showCPLModal || $this->isEditingCPL == false)
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'cpl',
                'isFlyout' => 1,
                'isSmall' => 1,
            ])
        @endif
    </div>

    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addRPS, editRPS'])

        <div class="space-y-6">

            @include('livewire.staff.obe-management.obe-partial.cpl-list')

            {{-- @if ($this->showRPSModal)
                @include('livewire.global.modal-form.input-array.search-input-array-form', [
                    'alpine' => 'rps',
                    'xResults' => $cplResults['rps'] ?? [],
                    'selectX' => 'selectCPLArray',
                    'modelString' => 'nama_cpl_search_rps',
                    'key' => 'rps',
                
                    'idString' => 'cpl_id_array.rps',
                    'id2String' => 'cpl_id_array',
                    'itemsAllString' => 'cpl_items_array.rps',
                
                    'typeXString' => 'deskripsi',
                
                    'nameXString' => 'Capaian Pembelajaran Lulusan',
                    'nameX2String' => 'Tambah CPL Baru',
                    'nameSearchString' => 'cplNameSearch.rps',
                    'fetchString' => 'fetchCPL',
                    'iconString' => 'document-text',
                
                    'parentIdString' => 'cpmk_id_array',
                    'nameXParent' => 'CPMK',
                    'wireLoading' => 'fetchCPL',
                
                    'isRequired' => 0,
                ])
            @endif --}}

        </div>



    </div>


</div>
