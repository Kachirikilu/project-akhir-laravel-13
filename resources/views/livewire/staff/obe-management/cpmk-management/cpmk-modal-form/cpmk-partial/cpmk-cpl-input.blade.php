<div
    class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">
        <h4 class="text-[var(--contrast-main-text)] text-sm sm:text-md md:text-lg font-medium mr-2">
            Input Capaian Pembelajaran Lulusan
        </h4>

        @if ($parent !== 'cpl')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'cpl',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'cpmk'
            ])
        @endif
    </div>


    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'addCPMK, editCPMK'])

        <div class="space-y-4">

            @if ($this->showCPMKModal)
                @include('livewire.global.modal-form.input-array.search-input-array-form', [
                    'alpine' => 'cpmk',
                    'xResults' => $cplResults,
                    'selectX' => 'selectCPLArray',
                    'modelString' => 'nama_cpl_search',
                
                    'idString' => 'cpl_id_array',
                    'itemsAllString' => 'cpl_items_array',
                
                    'typeXString' => 'deskripsi',
                
                    'nameXString' => 'Capaian Pembelajaran Lulusan',
                    'nameSearchString' => 'cplNameSearch',
                    'fetchString' => 'fetchCPL',
                    'iconString' => 'document-text',
                
                    'wireLoading' => 'fetchCPL',
                ])
            @endif


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
                'parentIdString' => 'cpl_id_array',
                'nameXParent' => 'CPL',
                'isRequired' => 0,
                'message' => $errors->first('deskripsi'),
            ])
        </div>

    </div>


</div>
