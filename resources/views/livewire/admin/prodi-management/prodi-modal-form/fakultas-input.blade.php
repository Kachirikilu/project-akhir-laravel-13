{{-- ****************************************************** --}}
{{-- 3. INPUT FAKULTAS --}}
{{-- ****************************************************** --}}
<div
    class="form-container">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Fakultas</h4>

    {{-- 📧 Fakultas Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Nama Fakultas',
        'modelString' => 'nama_fk',
        'iconString' => 'building-library',
        'placeholder' => 'Masukkan nama Fakultas',
        'message' => $errors->first('nama_fk')
    ])

    {{-- 📧 Kode Fakultas Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        'nameXString' => 'Kode Fakultas',
        'modelString' => 'kode_fk',
        'iconString' => 'hashtag',
        'placeholder' => 'Masukkan 3 huruf Kode Fakultas',
        'message' => $errors->first('kode_fk'),
        'isKode' => 3,
        'isFocusSelect' => 1,
    ])
</div>
