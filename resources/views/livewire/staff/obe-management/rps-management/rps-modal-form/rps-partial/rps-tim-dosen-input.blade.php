<div class="form-container">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Pilih Tim Dosen Pengajar</h4>

        @if ($parent !== 'tim_dosen')
            @include('livewire.staff.obe-management.obe-toolbar', [
                'typeXString' => 'tim_dosen',
                'isFlyout' => 1,
                'isSmall' => 1,
                'parent' => 'rps',
            ])
        @endif
    </div>

    <div class="relative">

        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'addRPS, editRPS',
            'heightContainer' => '32',
        ])

        @if ($this->showRPSModal)
            @include('livewire.global.modal-form.input-array.search-input-tim-dosen-form', [
                'alpine' => 'rps',
                'xResults' => $timDosenResults,
                'selectX' => 'selectTimDosenArray',
                'modelString' => 'nama_tim_dosen_search',
            
                'idString' => 'tim_dosen_id_array',
                'itemsAllString' => 'tim_dosen_items_array',
                'subItemsString' => 'tim_dosen_sub_items_array',
            
                'typeXString' => 'tim',
                'typeX2String' => 'ketua',
                'typeX3String' => 'prodi',
                'typeX4String' => 'anggota',
                'typeVString' => 'pr_id',
            
                'nameXString' => 'Tim Dosen Pengajar',
                'nameSearchString' => 'timDosenNameSearch',
                'fetchString' => 'fetchTimDosen',
                'iconString' => 'user-group',
                'wireLoading' => 'fetchTimDosen',
            ])
            {{-- @include('livewire.global.modal-form.input-array.search-input-dosen-form', [
                'alpine' => 'rps',
                'xResults' => $timDosenResults,
                'selectX' => 'selectTimDosenArray',
                'modelString' => 'nama_tim_dosen_search',
            
                'idString' => 'tim_dosen_id_array',
                'itemsAllString' => 'tim_dosen_items_array',
                'itemsPertemuanString' => 'tim_dosen_pertemuan_array',
            
                'typeXString' => 'tim',
                'typeX2String' => 'ketua',
                'typeX3String' => 'prodi',
                'typeX4String' => 'anggota',
            
                'nameXString' => 'Dosen Pengajar',
                'nameSearchString' => 'timDosenNameSearch',
                'fetchString' => 'fetchTimDosen',
                'iconString' => 'user',
                'wireLoading' => 'fetchTimDosen',
            ]) --}}
        @endif

    </div>

</div>
