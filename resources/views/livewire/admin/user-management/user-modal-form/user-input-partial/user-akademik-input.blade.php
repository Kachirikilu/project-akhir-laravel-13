{{-- ****************************************************** --}}
{{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Informasi Akademik</h4>


    <template x-if="$store.user?.typeModal !== 'dosen'" x-cloak>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Kode Wilayah',
            'modelString' => 'kode_wilayah',
            'xOptions' => ['IDL (Kampus Indralaya)', 'PLG (Kampus Bukit)'],
            'xValues' => ['IDL', 'PLG'],
            'iconString' => 'map-pin',
            'placeholder' => 'Pilih Kode Wilayah...',
            'message' => $errors->first('kode_wilayah'),
        ])
    </template>

    @if (Auth::user()->tingkat < 4)
        @include('livewire.global.modal-form.input-array.search-input-form', [
            'alpine' => 'user',
            'xResults' => $prResults,
            'selectX' => 'selectPr',
            'modelString' => 'nama_pr_search',
        
            'idString' => 'pr_id',
            'itemsAllString' => 'pr_items',
        
            'resetXInput' => 'resetPrInput()',
        
            'x3HeadString' => 'Fakultas',
        
            'typeXString' => 'prodi',
            'typeX2String' => 'departemen',
            'typeX3String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchPr',
        ])
    @endif
    {{-- <livewire:global.input-search.prodi-search-input /> --}}


    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
        {{-- 📧 Status Input --}}
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Status',
            'modelString' => 'status',
            'xOptions' => config('status.admin', []),
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'modelString' => 'status',
            'xOptions' => config('status.dosen', []),
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Tahun Angkatan',
            'modelString' => 'angkatan',
            'numberOnly' => 1,
            'maxLength' => 4,
            'iconString' => 'calendar-days',
            'placeholder' => 'Masukkan Tahun Angkatan (Contoh: 2022)',
            'message' => $errors->first('angkatan'),
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Status',
            'modelString' => 'status',
            'xOptions' => config('status.mahasiswa', []),
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>


</div>
