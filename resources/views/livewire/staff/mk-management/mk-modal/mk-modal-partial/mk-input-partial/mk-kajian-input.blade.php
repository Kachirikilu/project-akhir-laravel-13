<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Deskripsi & Bahan Kajian</h4>

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'mk',
        'isLivewire' => 1,
        'nameXString' => 'Deskripsi Mata Kuliah',
        'modelString' => 'deskripsi',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Deskripsi dari Mata Kuliah...',
        'message' => $errors->first('deskripsi'),
    ])

    @include('livewire.global.modal-form.textarea-form', [
        'alpine' => 'mk',
        'isLivewire' => 1,
        'nameXString' => 'Bahan Kajian',
        'modelString' => 'bahan_kajian',
        'iconString' => 'rectangle-stack',
        'placeholder' => 'Masukkan Bahan Kajian untuk Mata Kuliah...',
        'message' => $errors->first('bahan_kajian'),
    ])


</div>
