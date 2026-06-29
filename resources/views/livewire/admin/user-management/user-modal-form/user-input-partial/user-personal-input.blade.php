{{-- ****************************************************** --}}
{{-- 2. PERSONAL INFORMATION (SESUAI ROLE) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Informasi Personal</h4>

    {{-- 👤 Nama Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'user',
        'isLivewire' => 1,
        'nameXString' => 'Full Name',
        'modelString' => 'name',
        'iconString' => 'user-circle',
        'placeholder' => 'Masukkan Nama Lengkap',
        'message' => $errors->first('name'),
    ])

    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'isLivewire' => 1,
        'nameXString' => 'Nomor Induk Kependudukan (NIK)',
        'modelString' => 'nik',
        'numberOnly' => 1,
        'maxLength' => 16,
        'iconString' => 'identification',
        'placeholder' => 'Masukkan NIK',
        'message' => $errors->first('nik'),
    ])

    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'user',
        'isLivewire' => 1,
        'modelString' => 'jenis_kelamin',
        'xOptions' => ['Laki-laki', 'Perempuan'],
        'iconString' => 'users',
        'placeholder' => 'Pilih Gender...',
        'message' => $errors->first('jenis_kelamin'),
    ])
    
    @include('livewire.global.modal-form.select-form', [
        'alpine' => 'user',
        'isLivewire' => 1,
        'modelString' => 'agama',
        'xOptions' => ['Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya'],
        'iconString' => 'bookmark',
        'placeholder' => 'Pilih Agama...',
        'message' => $errors->first('agama'),
    ])

</div>
