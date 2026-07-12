<div
    class="form-container">

    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Materi Sub-CPMK</h4>


    <div class="relative space-y-4">

            @include('livewire.global.modal-form.textarea-form', [
                'alpine' => 'scpmk',
                'modelString' => 'materi',
                'iconString' => 'book-open',
                'placeholder' => 'Masukkan Materi Sub-CPMK...',
                'message' => $errors->first('materi'),
            ])
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'scpmk',
                'modelString' => 'metodologi',
                'iconString' => 'beaker',
                'placeholder' => 'Masukkan Metodologi Sub-CPMK...',
                'message' => $errors->first('metodologi'),
            ])
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'scpmk',
                'modelString' => 'indikator',
                'iconString' => 'clipboard-document-check',
                'placeholder' => 'Masukkan Indikator Sub-CPMK...',
                'message' => $errors->first('indikator'),
            ])

    </div>

</div>
