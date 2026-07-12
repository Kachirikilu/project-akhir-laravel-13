{{-- ****************************************************** --}}
{{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        ID Akademik</h4>

    <template x-if="$store.user?.typeModal == 'admin' || $store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Nomor Induk Pegawai (NIP)',
            'modelString' => 'nip',
            'numberOnly' => 1,
            'maxLength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIP...',
            'message' => $errors->first('nip'),
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Nomor Induk Tenaga Kerja (NITK)',
            'modelString' => 'nitk',
            'numberOnly' => 1,
            'maxLength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NITK...',
            'message' => $errors->first('nitk'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Nomor Induk Dosen Nasional (NIDN)',
            'modelString' => 'nidn',
            'numberOnly' => 1,
            'maxLength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIDN...',
            'message' => $errors->first('nidn'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Nomor Induk Dosen Khusus (NIDK)',
            'modelString' => 'nidk',
            'numberOnly' => 1,
            'maxLength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIDK...',
            'message' => $errors->first('nidk'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'nameXString' => 'Nomor Induk Mahasiswa (NIM)',
            'modelString' => 'nim',
            'numberOnly' => 1,
            'maxLength' => 20,
            'iconString' => 'identification',
            'placeholder' => 'Masukkan NIM...',
            'message' => $errors->first('nim'),
        ])
    </template>



</div>
