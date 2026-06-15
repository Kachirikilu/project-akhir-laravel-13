{{-- ****************************************************** --}}
{{-- 3. INPUT FAKULTAS --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4 class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Fakultas</h4>

    {{-- 📧 Fakultas Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'prodi',
        // 'colorIcon' => $colorIcon,
        'nameXString' => 'Nama Fakultas',
        'modelString' => 'nama_fk',
        // 'typeString' => 'text',
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
