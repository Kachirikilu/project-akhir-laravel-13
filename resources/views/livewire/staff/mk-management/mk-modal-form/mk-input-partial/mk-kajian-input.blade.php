<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Input Deskripsi & Bahan Kajian</h4>

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'mk',
        'nameXString' => 'Deskripsi Mata Kuliah',
        'modelString' => 'deskripsi',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Deskripsi dari Mata Kuliah...',
        'message' => $errors->first('deskripsi'),
    ])

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'mk',
        'nameXString' => 'Bahan Kajian',
        'modelString' => 'bahan_kajian',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Bahan Kajian untuk Mata Kuliah...',
        'message' => $errors->first('bahan_kajian'),
    ])


</div>
