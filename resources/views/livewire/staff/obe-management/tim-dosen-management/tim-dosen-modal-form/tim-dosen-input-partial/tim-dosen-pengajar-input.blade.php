<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Pilih Dosen Pengajar</h4>

        @if (Auth::user()->admin || $parent !== 'user')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'dosen',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'tim-dosen'
            ])
        @endif
    </div>

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'addTimDosen, editTimDosen',
            'heightContainer' => '32',
        ])

        @if ($this->showTimDosenModal)
            @include('livewire.global.modal-form.input-array.search-input-dosen-form', [
                'alpine' => 'tim_dosen',
                'xResults' => $dosenResults,
                'selectX' => 'selectDosenArray',
                'modelString' => 'nama_dosen_search',
            
                'idString' => 'dosen_id_array',
                'itemsAllString' => 'dosen_items_array',
                'itemsPertemuanString' => 'dosen_pertemuan_array',
            
                'typeXString' => 'name',
                'typeX2String' => 'nidn',
                'typeX3String' => 'nidk',
                'typeX4String' => 'status',
                'typeX5String' => 'prodi',
            
                'nameXString' => 'Dosen Pengajar',
                'nameSearchString' => 'dosenNameSearch',
                'fetchString' => 'fetchDosen',
                'iconString' => 'user',
                'wireLoading' => 'fetchDosen',
            ])
        @endif

    </div>

</div>
