<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">
        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Input Capaian Pembelajaran Lulusan
        </h4>

        @if (!$this->showCPLModal || $this->isEditingCPL == false)
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'cpl',
                'isFlyout' => 1,
                'isSmall' => 1,
            ])
        @endif
    </div>


    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addCPMK, editCPMK'])

        <div class="space-y-4">

            @include('livewire.global.modal-form.input-array.search-input-array-form', [
                'alpine' => 'cpmk',
                'xResults' => $cplResults['cpmk'] ?? [],
                'selectX' => 'selectCPLArray',
                'modelString' => 'nama_cpl_search_cpmk',
                'key' => 'cpmk',
            
                'idString' => 'cpl_id_array.cpmk',
                'id2String' => 'cpl_id_array',
                'itemsAllString' => 'cpl_items_array.cpmk',
            
                'typeXString' => 'deskripsi',
            
                'nameXString' => 'Capaian Pembelajaran Lulusan',
                'nameSearchString' => 'cplNameSearch.cpmk',
                'fetchString' => 'fetchCPL',
                'iconString' => 'document-text',
            
                'wireLoading' => 'fetchCPL',
            ])


            {{-- @include('livewire.global.modal-form.input-form', [
                'alpine' => 'cpmk',
                'nameXString' => 'Deskripsi',
                'modelString' => 'deskripsi',
                'iconString' => 'academic-cap',
                'placeholder' => 'Ini akan mengubah deskripsi CPL...',
                'message' => $errors->first('deskripsi'),
            
                'isRequired' => 0,
            ]) --}}

            @include('livewire.global.modal-form.textarea-form', [
                'alpine' => 'cpmk',
                'nameXString' => 'Deskripsi CPMK',
                'modelString' => 'deskripsi',
                'iconString' => 'academic-cap',
                'placeholder' => 'Kosongkan jika ingin sama persis dengan CPL...',
                'parentIdString' => 'cpl_id_array.cpmk',
                'nameXParent' => 'CPL',
                'isRequired' => 0,
                'message' => $errors->first('deskripsi'),
            ])
        </div>

    </div>


</div>
