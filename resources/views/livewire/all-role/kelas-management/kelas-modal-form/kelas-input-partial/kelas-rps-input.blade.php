<div
    class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Program Studi & Rencana Pembelajaran Semester</h4>

    @if (Auth::user()->tingkat < 4)
        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'kelas',
            'xResults' => $prResults,
            'selectX' => 'selectPr',
            'modelString' => 'nama_pr_search',
        
            'idString' => 'pr_id',
            'itemsAllString' => 'pr_items',
        
            'resetXInput' => 'resetPrInput()',
        
            'typeXString' => 'prodi',
            // 'typeX2String' => 'departemen',
            'typeX2String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchPr',
        ])
    @endif

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'kelas',
        'xResults' => $rpsResults,
        'selectX' => 'selectRPS',
        'modelString' => 'nama_rps_search',
    
        'idString' => 'rps_id',
        'itemsAllString' => 'rps_items',
    
        'resetXInput' => 'resetRPSInput()',
        'typeXString' => 'rps',
        'typeX2String' => 'sks_full',
        'typeX3String' => 'wajib_text',
        'typeX4String' => 'draf_full',
    
        'nameXString' => 'Rencana Pembelajaran Semester',
        'nameSearchString' => 'rpsNameSearch',
        'fetchString' => 'fetchRPS',
        'iconString' => 'academic-cap',
    
        'parentIdString' => 'pr_id',
        'nameXParent' => 'Program Studi',
        'wireLoading' => 'fetchRPS',
        'wireLoadingParent' => 'selectPr, resetPrInput, selectPrForFilter, resetPrFilter',
    ])
</div>
