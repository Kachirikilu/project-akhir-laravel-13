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
            'nameXString' => 'Kode Wilayah',
            'modelString' => 'kode_wilayah',
            'xOptions' => ['IDL (Kampus Indralaya)', 'PLG (Kampus Bukit)'],
            'xValues' => ['IDL', 'PLG'],
            'iconString' => 'map-pin',
            'placeholder' => 'Pilih Kode Wilayah...',
            'message' => $errors->first('kode_wilayah'),
        ])
    </template>

    @include('livewire.global.modal-form.input-array.search-input-form', [
        'alpine' => 'user',
        'xResults' => $prResults,
        'selectX' => 'selectPr',
        'modelString' => 'nama_pr',
    
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

    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
        {{-- 📧 Status Input --}}
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'nameXString' => 'Status',
            'modelString' => 'status',
            'xOptions' => [
                'Aktif', // Hijau (Produktif)
                'Tugas Belajar', // Kuning (Transisi/Sementara)
                'Mutasi', // Kuning (Transisi/Sementara)
                'Cuti Luar Tanggungan', // Kuning (Transisi/Sementara)
                'Resign', // Orange (Keluar Prosedural)
                'Pensiun', // Orange (Keluar Prosedural)
                'Diberhentikan', // Merah (Masalah/Sanksi)
                'Meninggal Dunia', // Merah (Permanen)
            ],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        {{-- 📧 Status Input --}}
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'modelString' => 'status',
            'xOptions' => [
                'Aktif', // Hijau (Produktif)
                'Tugas Belajar', // Kuning (Transisi/Studi)
                'Izin Belajar', // Kuning (Transisi/Studi)
                'Cuti Sabatika', // Kuning (Transisi/Riset)
                'Alih Tugas', // Orange (Perubahan Jabatan)
                'Resign', // Orange (Keluar Prosedural)
                'Pensiun', // Orange (Keluar Prosedural)
                'Diberhentikan', // Merah (Masalah/Sanksi)
                'Meninggal Dunia', // Merah (Permanen)
            ],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'alpine' => 'user',
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
        {{-- 📧 Status Input --}}
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            'nameXString' => 'Status',
            'modelString' => 'status',
            'xOptions' => [
                'Aktif', // Hijau (Aktif Kuliah)
                'Lulus', // Biru (Output Positif)
                'Cuti', // Kuning (Jeda Resmi)
                'Pindah', // Kuning (Transisi Keluar)
                'Non-Aktif', // Orange (Masalah Administrasi)
                'Mengundurkan Diri', // Orange (Keluar Prosedural)
                'Drop Out', // Merah (Masalah Akademik/Sanksi)
                'Hilang', // Merah (Tanpa Kabar/Ghaib)
                'Meninggal Dunia', // Merah (Permanen)
            ],
            // 'typeString' => 'text',
            // 'colorIcon' => $colorIcon,
            'iconString' => 'tag',
            'placeholder' => 'Pilih Status...',
            'message' => $errors->first('status'),
            'isRequired' => 0,
        ])
    </template>


</div>
