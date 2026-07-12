<div
    class="form-container">

    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Materi Sub-CPMK</h4>


    <div class="relative space-y-4">

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'sesi',
            'nameXString' => 'Deskripsi',
            'modelString' => 'deskripsi',
            'iconString' => 'document-text',
            'placeholder' => 'Masukkan deskripsi ringkas tentang Sub-CPMK...',
            'message' => $errors->first('deskripsi'),
        ])

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'sesi',
            'modelString' => 'materi',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan Materi Sub-CPMK...',
            'message' => $errors->first('materi'),
            'isRequired' => 0,
        ])
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'sesi',
            'modelString' => 'metodologi',
            'iconString' => 'beaker',
            'placeholder' => 'Masukkan Metodologi Sub-CPMK...',
            'message' => $errors->first('metodologi'),
            'isRequired' => 0,
        ])
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'sesi',
            'modelString' => 'indikator',
            'iconString' => 'clipboard-document-check',
            'placeholder' => 'Masukkan Indikator Sub-CPMK...',
            'message' => $errors->first('indikator'),
            'isRequired' => 0,
        ])

    </div>

</div>

<div
    class="form-container">

    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
        Input Metode Sub-CPMK</h4>


    <div class="relative space-y-4">

        @include('livewire.global.modal-form.textarea-form', [
            'alpine' => 'sesi',
            'nameXString' => 'Deskripsi Tugas',
            'modelString' => 'deskripsi_tugas',
            'iconString' => 'book-open',
            'placeholder' => 'Masukkan Deskripsi Tugas...',
            'message' => $errors->first('deskripsi_tugas'),
            'isRequired' => 0,
        ])

        <div class="grid sm:grid-cols-4 gap-3 items-start">

            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'sesi',
                    'nameXString' => 'Waktu Tugas (Menit)',
                    'modelString' => 'waktu_tugas',
                    'numberOnly' => 1,
                    'maxLength' => 3,
                    'iconString' => 'clock',
                    'placeholder' => 'Default: ' . $sks * 60 . ' menit',
                    'isRequired' => 0,
                    'message' => $errors->first('waktu_tugas'),
                ])
            </div>
            <div class="sm:col-span-2">
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'sesi',
                    'nameXString' => 'Waktu Mandiri (Menit)',
                    'modelString' => 'waktu_mandiri',
                    'numberOnly' => 1,
                    'maxLength' => 3,
                    'iconString' => 'clock',
                    'placeholder' => 'Default: ' . $sks * 60 . ' menit',
                    'isRequired' => 0,
                    'message' => $errors->first('waktu_mandiri'),
                ])
            </div>
        </div>

    </div>

</div>
