<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
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
